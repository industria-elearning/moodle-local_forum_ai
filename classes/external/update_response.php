<?php
namespace local_forum_ai\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use context_system;

class update_response extends external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'token'   => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación'),
            'message' => new external_value(PARAM_RAW, 'Nuevo mensaje de la IA'),
        ]);
    }

    public static function execute($token, $message) {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), [
            'token' => $token,
            'message' => $message,
        ]);

        $pending = $DB->get_record('local_forum_ai_pending', ['approval_token' => $params['token']], '*', MUST_EXIST);

        // Validación de permisos
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('mod/forum:replypost', $context);

        $pending->message = $params['message'];
        $pending->timemodified = time();
        $DB->update_record('local_forum_ai_pending', $pending);

        return ['status' => 'ok', 'message' => $pending->message];
    }

    public static function execute_returns() {
        return new \external_single_structure([
            'status'  => new external_value(PARAM_TEXT, 'Estado de la operación'),
            'message' => new external_value(PARAM_RAW, 'Mensaje actualizado'),
        ]);
    }
}
