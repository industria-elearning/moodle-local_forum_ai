<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Observadores de eventos para forum_ai.
 *
 * @package    local_forum_ai
 * @category   event
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai;

use aiprovider_datacurso\httpclient\ai_services_api;
use mod_forum\event\discussion_created;

/**
 * Clase observadora de eventos para forum_ai.
 */
class observer {
    /**
     * Envía el payload al servicio externo de IA y devuelve el reply.
     *
     * @param array $payload
     * @return string
     * @throws moodle_exception
     */
    protected static function call_ai_service(array $payload) {
        $client = new ai_services_api();
        $response = $client->request('POST', '/forum/chat', $payload);

        return $response['reply'];
    }


    /**
     * Maneja la creación de foros de tipo "single".
     *
     * @param \core\event\course_module_created $event
     * @return bool
     */
    public static function course_module_created(\core\event\course_module_created $event): bool {
        global $DB;

        try {
            if ($event->other['modulename'] !== 'forum') {
                return true;
            }

            $forumid = $event->other['instanceid'];
            $forum = $DB->get_record('forum', ['id' => $forumid], '*', MUST_EXIST);

            if ($forum->type !== 'single') {
                return true;
            }

            $maxattempts = 5;
            $discussion = null;
            for ($i = 0; $i < $maxattempts; $i++) {
                $discussion = $DB->get_record('forum_discussions', ['forum' => $forum->id], '*', IGNORE_MULTIPLE);
                if ($discussion) {
                    break;
                }
                sleep(1);
            }

            if (!$discussion) {
                debugging("forum_ai: No se encontró discusión inicial para foro tipo single ID {$forum->id}", DEBUG_DEVELOPER);
                return true;
            }

            $singleevent = \mod_forum\event\discussion_created::create([
                'objectid' => $discussion->id,
                'context' => $event->get_context(),
                'courseid' => $event->courseid,
                'relateduserid' => $discussion->userid,
                'other' => ['forumid' => $forumid],
            ]);

            self::discussion_created($singleevent);

            return true;
        } catch (\Exception $e) {
            debugging("forum_ai: Error en course_module_created: " . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Maneja la creación de discusiones.
     *
     * @param discussion_created $event
     * @return bool
     */
    public static function discussion_created(discussion_created $event): bool {
        global $DB;

        try {
            $data = $event->get_data();
            $discussionid = $data['objectid'];
            $forumid = $data['other']['forumid'];

            $config = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);

            $enabled = $config->enabled ?? get_config('local_forum_ai', 'default_enabled');
            $replymessage = $config->reply_message ?? get_config('local_forum_ai', 'default_reply_message');
            $requireapproval = $config->require_approval ?? 1;

            if (!$enabled) {
                return true;
            }

            $discussion = $DB->get_record('forum_discussions', ['id' => $discussionid], '*', MUST_EXIST);
            $post = $DB->get_record('forum_posts', ['id' => $discussion->firstpost], '*', MUST_EXIST);
            $forum = $DB->get_record('forum', ['id' => $forumid], '*', MUST_EXIST);
            $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);

            $payload = [
                'course' => $course->fullname,
                'forum' => $forum->name,
                'discussion' => $discussion->name,
                'userid' => $discussion->userid,
                'post' => [
                    'subject' => $post->subject,
                    'message' => strip_tags($post->message),
                ],
                'prompt' => $replymessage,
            ];

            $airesponse = self::call_ai_service($payload);

            if ($requireapproval) {
                self::create_approval_request($discussion, $airesponse, 'pending');
            } else {
                self::create_approval_request($discussion, $airesponse, 'approved');
                self::create_auto_reply($discussion, $airesponse);
            }

            return true;
        } catch (\Exception $e) {
            debugging("forum_ai: Error en observer discussion_created: " . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Crea solicitud de aprobación y envía notificación.
     *
     * @param object $discussion
     * @param string $message
     * @param string $status
     * @return void
     */
    private static function create_approval_request($discussion, string $message, string $status = 'pending'): void {
        global $DB;

        try {
            $approvaltoken = hash('sha256', $discussion->id . time() . random_string(20));

            $pending = new \stdClass();
            $pending->discussionid = $discussion->id;
            $pending->forumid = $discussion->forum;
            $pending->creator_userid = $discussion->userid;
            $pending->subject = "Re: " . $discussion->name;
            $pending->message = $message;
            $pending->status = $status;
            $pending->approval_token = $approvaltoken;
            $pending->timecreated = time();

            $pendingid = $DB->insert_record('local_forum_ai_pending', $pending);

            if ($status === 'pending') {
                self::send_moodle_notification($discussion, $pendingid, $approvaltoken);
            }
        } catch (\Exception $e) {
            debugging("forum_ai: Error al crear solicitud de aprobación: " . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Envía notificación usando el sistema nativo de Moodle.
     *
     * @param object $discussion
     * @param int $pendingid
     * @param string $approvaltoken
     * @return bool
     */
    private static function send_moodle_notification($discussion, int $pendingid, string $approvaltoken): bool {
        global $DB, $CFG;

        try {
            // Obtener datos necesarios.
            $creator = $DB->get_record('user', ['id' => $discussion->userid]);
            $forum = $DB->get_record('forum', ['id' => $discussion->forum]);
            $course = $DB->get_record('course', ['id' => $forum->course]);
            $pending = $DB->get_record('local_forum_ai_pending', ['id' => $pendingid]);

            if (!$creator || !$forum || !$course || !$pending) {
                throw new \Exception('Datos incompletos para notificación');
            }

            // Obtener el cmid del foro y el contexto del módulo.
            $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
            $context = \context_module::instance($cm->id);

            // Buscar usuarios con capacidad.
            $recipients = get_users_by_capability($context, 'mod/forum:replypost');

            // Filtrar por roles permitidos.
            $allowedroles = ['manager', 'editingteacher', 'coursecreator'];
            $finalrecipients = [];

            foreach ($recipients as $recipient) {
                $roles = get_user_roles($context, $recipient->id);
                foreach ($roles as $role) {
                    if (in_array($role->shortname, $allowedroles)) {
                        $finalrecipients[$recipient->id] = $recipient;
                    }
                }
            }

            // URL para ver detalles y aprobar.
            $reviewurl = new \moodle_url('/local/forum_ai/review.php', [
                'token' => $approvaltoken,
            ]);

            $approveurl = new \moodle_url('/local/forum_ai/approve.php', [
                'token' => $approvaltoken,
                'action' => 'approve',
            ]);

            $rejecturl = new \moodle_url('/local/forum_ai/approve.php', [
                'token' => $approvaltoken,
                'action' => 'reject',
            ]);

            // Mandar mensaje solo a los permitidos.
            foreach ($finalrecipients as $recipient) {
                $message = new \core\message\message();
                $message->component = 'local_forum_ai';
                $message->name = 'ai_approval_request';
                $message->userfrom = \core_user::get_noreply_user();
                $message->userto = $recipient;
                $message->subject = 'Aprobación requerida: Respuesta AI';

                $message->fullmessage = "Hola {$recipient->firstname},\n\n"
                    . "Se ha generado una respuesta automática para el debate \"{$discussion->name}\" "
                    . "en el foro \"{$forum->name}\" del curso \"{$course->fullname}\".\n\n"
                    . "Vista previa: " . format_string(substr(strip_tags($pending->message), 0, 100)) . "...\n\n"
                    . "Revisa y aprueba la respuesta en: {$reviewurl}\n\n"
                    . "Aprobar directamente: {$approveurl}\n"
                    . "Rechazar: {$rejecturl}";

                $message->fullmessageformat = FORMAT_PLAIN;

                $message->fullmessagehtml = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px;'>
                        <h3 style='color: #0f6cbf;'>Aprobación requerida: Respuesta AI</h3>

                        <p><strong>Hola {$recipient->firstname},</strong></p>

                        <p>Se ha generado una respuesta automática para el debate
                        <strong>\"{$discussion->name}\"</strong> en el foro
                        <strong>\"{$forum->name}\"</strong>.</p>

                        <div style='background-color: #f8f9fa; padding: 15px;
                                    border-left: 4px solid #0f6cbf; margin: 15px 0;'>
                            <h4 style='margin-top: 0;'>Vista previa:</h4>
                            <div style='background: white; padding: 10px;
                                        border: 1px solid #ddd; border-radius: 4px;'>
                                " . format_string(substr(strip_tags($pending->message), 0, 150)) . "...
                            </div>
                        </div>

                        <div style='margin: 20px 0;'>
                            <a href='{$reviewurl}'
                               style='background-color: #0f6cbf; color: white;
                                      padding: 12px 25px; text-decoration: none;
                                      border-radius: 6px; display: inline-block;
                                      margin-right: 10px;'>
                                Ver completa y decidir
                            </a>
                        </div>
                        <p style='color: #666; font-size: 0.9em;'><strong>Curso: {$course->fullname}</strong></p>
                    </div>
                ";

                $message->smallmessage = "Nueva respuesta AI pendiente en \"{$discussion->name}\"";
                $message->contexturl = $reviewurl;
                $message->contexturlname = 'Revisar respuesta';

                message_send($message);
            }

            return true;
        } catch (\Exception $e) {
            debugging("forum_ai: Error al enviar notificación Moodle: " . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Crea la respuesta automática en el foro.
     *
     * @param object $discussion
     * @param string $message
     * @return bool
     */
    public static function create_auto_reply($discussion, string $message): bool {
        global $DB;

        try {
            // Obtener el curso del debate.
            $course = $DB->get_record('course', ['id' => $discussion->course], '*', MUST_EXIST);
            $context = \context_course::instance($course->id);

            // Obtener todos los usuarios matriculados con el rol "editingteacher".
            $teachers = get_editingteachers($course->id);

            if (empty($teachers)) {
                debugging("forum_ai: No se encontró profesor para publicar respuesta", DEBUG_DEVELOPER);
                return false;
            }

            // Usar al primer profesor encontrado.
            $teacher = reset($teachers);

            $post = new \stdClass();
            $post->discussion = $discussion->id;
            $post->parent = $discussion->firstpost;
            $post->userid = $teacher->id;
            $post->created = time();
            $post->modified = time();
            $post->subject = $post->subject ?: ("Re: " . $discussion->name);
            $post->message = $message;
            $post->messageformat = FORMAT_HTML;

            $DB->insert_record('forum_posts', $post);
            return true;
        } catch (\Exception $e) {
            debugging("forum_ai: Error en create_auto_reply: " . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }
}
