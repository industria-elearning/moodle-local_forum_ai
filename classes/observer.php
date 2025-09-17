<?php
namespace local_forum_ai;

defined('MOODLE_INTERNAL') || die();

use mod_forum\event\discussion_created;

class observer {

    /**
     * Observer para cuando se crea una nueva discusión en un foro
     *
     * @param discussion_created $event
     * @return bool
     */
    public static function discussion_created(discussion_created $event) {
        global $DB, $CFG, $USER;

        try {
            // Obtener datos del evento
            $data = $event->get_data();
            $discussionid = $data['objectid'];
            $forumid = $data['other']['forumid'];

            // Obtener configuración específica del foro
            $config = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);

            // Si no hay configuración específica, usar valores por defecto
            if (!$config) {
                $enabled = get_config('local_forum_ai', 'default_enabled');
                $bot_userid = get_config('local_forum_ai', 'default_bot_userid');
                $reply_message = get_config('local_forum_ai', 'default_reply_message');
            } else {
                $enabled = $config->enabled;
                $bot_userid = $config->bot_userid;
                $reply_message = $config->reply_message;
            }

            // Verificar si la AI está habilitada para este foro
            if (!$enabled) {
                error_log("forum_ai: AI deshabilitada para foro {$forumid}");
                return true;
            }

            // Verificar que exista un usuario bot configurado
            if (empty($bot_userid)) {
                error_log("forum_ai: No hay usuario bot configurado para foro {$forumid}");
                return true;
            }

            // Verificar que el usuario bot exista
            $bot_user = $DB->get_record('user', ['id' => $bot_userid]);
            if (!$bot_user) {
                error_log("forum_ai: Usuario bot {$bot_userid} no encontrado para foro {$forumid}");
                return true;
            }

            // Obtener información de la discusión
            $discussion = $DB->get_record('forum_discussions', ['id' => $discussionid], '*', MUST_EXIST);

            // Evitar que el bot responda a sus propias discusiones
            if ($discussion->userid == $bot_userid) {
                error_log("forum_ai: Evitando auto-respuesta del bot en foro {$forumid}");
                return true;
            }

            // Obtener el post original para contexto
            $original_post = $DB->get_record('forum_posts', ['id' => $discussion->firstpost]);

            // Generar respuesta (por ahora mockeada)
            $ai_response = self::generate_ai_response($original_post, $reply_message, $config);

            // Crear la respuesta automática
            $success = self::create_auto_reply($discussion, $bot_userid, $ai_response);

            if ($success) {
                error_log("forum_ai: Respuesta AI creada exitosamente para discusión {$discussionid} en foro {$forumid}");
            } else {
                error_log("forum_ai: Error al crear respuesta AI para discusión {$discussionid} en foro {$forumid}");
            }

            return true;

        } catch (\Exception $e) {
            error_log("forum_ai: Error en observer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Genera una respuesta usando AI (mockeada por ahora)
     *
     * @param object $original_post Post original
     * @param string $base_message Mensaje base configurado
     * @param object|null $config Configuración del foro
     * @return string Respuesta generada
     */
    private static function generate_ai_response($original_post, $base_message, $config = null) {
        // Por ahora es mockeado, pero aquí integrarías la API de AI real

        // Usar el mensaje base configurado
        if (empty($base_message)) {
            $base_message = "Gracias por tu participación. Un moderador revisará tu mensaje.";
        }

        // Agregar algo de contexto "AI-like" (mockeado)
        $ai_responses = [
            $base_message,
            $base_message . " Tu pregunta es muy interesante y seguramente generará una buena discusión.",
            $base_message . " ¡Excelente tema para debatir!",
            $base_message . " Gracias por iniciar esta conversación, esperamos más participación.",
            $base_message . " Tu aportación enriquece mucho este foro."
        ];

        // Seleccionar respuesta aleatoria para simular variedad
        $selected_response = $ai_responses[array_rand($ai_responses)];

        // Log para debugging
        error_log("forum_ai: Respuesta generada - " . substr($selected_response, 0, 50) . "...");

        return $selected_response;
    }

    /**
     * Crea la respuesta automática en el foro
     *
     * @param object $discussion Objeto de discusión
     * @param int $bot_userid ID del usuario bot
     * @param string $message Mensaje a enviar
     * @return bool Success
     */
    private static function create_auto_reply($discussion, $bot_userid, $message) {
        global $DB, $CFG, $USER;

        try {
            // Preparar el post
            $post = new \stdClass();
            $post->discussion = $discussion->id;
            $post->parent     = $discussion->firstpost; // Responder al post original
            $post->userid     = $bot_userid;
            $post->created    = time();
            $post->modified   = time();
            $post->subject    = "Re: " . $discussion->name;
            $post->message    = format_text($message, FORMAT_HTML);
            $post->messageformat = FORMAT_HTML;
            $post->messagetrust = 1;
            $post->attachment = '';

            // Guardar el usuario actual y cambiar temporalmente al bot
            $realuser = $USER;
            $USER = \core_user::get_user($bot_userid);

            // Crear el post usando la función de Moodle
            require_once($CFG->dirroot . '/mod/forum/lib.php');
            $postid = forum_add_new_post($post, null);

            // Restaurar el usuario original
            $USER = $realuser;

            if ($postid) {
                error_log("forum_ai: Post creado exitosamente con ID {$postid}");
                return true;
            } else {
                error_log("forum_ai: Error al crear post - forum_add_new_post retornó false");
                return false;
            }

        } catch (\Exception $e) {
            // Asegurar que se restaure el usuario original en caso de error
            if (isset($realuser)) {
                global $USER;
                $USER = $realuser;
            }

            error_log("forum_ai: Error al crear respuesta automática: " . $e->getMessage());
            return false;
        }
    }
}
