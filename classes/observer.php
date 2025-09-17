<?php
namespace local_forum_ai;

defined('MOODLE_INTERNAL') || die();

use mod_forum\event\discussion_created;

class observer {

    public static function discussion_created(discussion_created $event) {
        global $DB, $CFG, $USER;

        try {
            $data = $event->get_data();
            $discussionid = $data['objectid'];
            $forumid = $data['other']['forumid'];

            $config = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);

            if (!$config) {
                $enabled = get_config('local_forum_ai', 'default_enabled');
                $bot_userid = get_config('local_forum_ai', 'default_bot_userid');
                $reply_message = get_config('local_forum_ai', 'default_reply_message');
                $require_approval = 1;
            } else {
                $enabled = $config->enabled;
                $bot_userid = $config->bot_userid;
                $reply_message = $config->reply_message;
                $require_approval = $config->require_approval ?? 1;
            }

            if (!$enabled || empty($bot_userid)) {
                return true;
            }

            $discussion = $DB->get_record('forum_discussions', ['id' => $discussionid], '*', MUST_EXIST);

            if ($discussion->userid == $bot_userid) {
                return true;
            }

            $original_post = $DB->get_record('forum_posts', ['id' => $discussion->firstpost]);
            $ai_response = self::generate_ai_response($original_post, $reply_message, $config);

            if ($require_approval) {
                self::create_approval_request($discussion, $bot_userid, $ai_response);
            } else {
                self::create_auto_reply($discussion, $bot_userid, $ai_response);
            }

            return true;

        } catch (\Exception $e) {
            error_log("forum_ai: Error en observer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea solicitud de aprobación y envía notificación Moodle
     */
    private static function create_approval_request($discussion, $bot_userid, $message) {
        global $DB, $CFG;

        try {
            $approval_token = hash('sha256', $discussion->id . time() . random_string(20));

            $pending = new \stdClass();
            $pending->discussionid = $discussion->id;
            $pending->forumid = $discussion->forum;
            $pending->creator_userid = $discussion->userid;
            $pending->bot_userid = $bot_userid;
            $pending->subject = "Re: " . $discussion->name;
            $pending->message = $message;
            $pending->status = 'pending';
            $pending->approval_token = $approval_token;
            $pending->timecreated = time();

            $pending_id = $DB->insert_record('local_forum_ai_pending', $pending);

            // Enviar notificación nativa de Moodle
            self::send_moodle_notification($discussion, $pending_id, $approval_token);

            error_log("forum_ai: Solicitud de aprobación creada con ID {$pending_id}");

        } catch (\Exception $e) {
            error_log("forum_ai: Error al crear solicitud de aprobación: " . $e->getMessage());
        }
    }

    /**
     * Envía notificación usando el sistema nativo de Moodle
     */
    private static function send_moodle_notification($discussion, $pending_id, $approval_token) {
        global $DB, $CFG;

        try {
            // Obtener datos necesarios
            $creator = $DB->get_record('user', ['id' => $discussion->userid]);
            $forum = $DB->get_record('forum', ['id' => $discussion->forum]);
            $course = $DB->get_record('course', ['id' => $forum->course]);
            $pending = $DB->get_record('local_forum_ai_pending', ['id' => $pending_id]);

            if (!$creator || !$forum || !$course || !$pending) {
                throw new \Exception('Datos incompletos para notificación');
            }

            // Obtener el cmid del foro y el contexto del módulo
            $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
            $context = \context_module::instance($cm->id);

            // Buscar usuarios con capacidad de responder en foros (profesores, managers, admins)
            $recipients = get_users_by_capability($context, 'mod/forum:replypost');

            // URL para ver detalles y aprobar
            $review_url = new \moodle_url('/local/forum_ai/review.php', [
                'token' => $approval_token
            ]);

            // URLs rápidas (opcional, para botones directos)
            $approve_url = new \moodle_url('/local/forum_ai/approve.php', [
                'token' => $approval_token,
                'action' => 'approve'
            ]);

            $reject_url = new \moodle_url('/local/forum_ai/approve.php', [
                'token' => $approval_token,
                'action' => 'reject'
            ]);

            // Preparar el mensaje
            foreach ($recipients as $recipient) {
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
                . "Revisa y aprueba la respuesta en: {$review_url}\n\n"
                . "Aprobar directamente: {$approve_url}\n"
                . "Rechazar: {$reject_url}";

            $message->fullmessageformat = FORMAT_PLAIN;

            $message->fullmessagehtml = "
                <div style='font-family: Arial, sans-serif; max-width: 600px;'>
                    <h3 style='color: #0f6cbf;'>🤖 Aprobación requerida: Respuesta AI</h3>

                    <p><strong>Hola {$creator->firstname},</strong></p>

                    <p>Se ha generado una respuesta automática para tu debate
                    <strong>\"{$discussion->name}\"</strong> en el foro
                    <strong>\"{$forum->name}\"</strong>.</p>

                    <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0f6cbf; margin: 15px 0;'>
                        <h4 style='margin-top: 0;'>Vista previa:</h4>
                        <div style='background: white; padding: 10px; border: 1px solid #ddd; border-radius: 4px;'>
                            " . format_string(substr(strip_tags($pending->message), 0, 150)) . "...
                        </div>
                    </div>

                    <div style='margin: 20px 0;'>
                        <a href='{$review_url}' style='background-color: #0f6cbf; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; display: inline-block; margin-right: 10px;'>
                            📋 Ver completa y decidir
                        </a>
                    </div>

                    <div style='margin: 15px 0;'>
                        <a href='{$approve_url}' style='background-color: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 8px; font-size: 0.9em;'>
                            ✅ Aprobar
                        </a>
                        <a href='{$reject_url}' style='background-color: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 0.9em;'>
                            ❌ Rechazar
                        </a>
                    </div>

                    <p style='color: #666; font-size: 0.9em;'>Curso: {$course->fullname}</p>
                </div>
            ";

            $message->smallmessage = "Nueva respuesta AI pendiente en \"{$discussion->name}\"";
            $message->contexturl = $review_url;
            $message->contexturlname = 'Revisar respuesta';

            message_send($message);
        }

        return true;

        } catch (\Exception $e) {
            error_log("forum_ai: Error al enviar notificación Moodle: " . $e->getMessage());
            return false;
        }
    }

    private static function generate_ai_response($original_post, $base_message, $config = null) {
        if (empty($base_message)) {
            $base_message = "Gracias por tu participación. Un moderador revisará tu mensaje.";
        }

        $ai_responses = [
            $base_message,
            $base_message . " Tu pregunta es muy interesante y seguramente generará una buena discusión.",
            $base_message . " ¡Excelente tema para debatir!",
            $base_message . " Gracias por iniciar esta conversación, esperamos más participación.",
            $base_message . " Tu aportación enriquece mucho este foro."
        ];

        return $ai_responses[array_rand($ai_responses)];
    }

    public static function create_auto_reply($discussion, $bot_userid, $message) {
        global $DB, $CFG, $USER;

        try {
            $post = new \stdClass();
            $post->discussion = $discussion->id;
            $post->parent     = $discussion->firstpost;
            $post->userid     = $bot_userid;
            $post->created    = time();
            $post->modified   = time();
            $post->subject    = "Re: " . $discussion->name;
            $post->message    = format_text($message, FORMAT_HTML);
            $post->messageformat = FORMAT_HTML;
            $post->messagetrust = 1;
            $post->attachment = '';

            $realuser = $USER;
            $USER = \core_user::get_user($bot_userid);

            require_once($CFG->dirroot . '/mod/forum/lib.php');
            $postid = forum_add_new_post($post, null);

            $USER = $realuser;

            return $postid ? true : false;

        } catch (\Exception $e) {
            if (isset($realuser)) {
                global $USER;
                $USER = $realuser;
            }
            error_log("forum_ai: Error al crear respuesta automática: " . $e->getMessage());
            return false;
        }
    }
}
