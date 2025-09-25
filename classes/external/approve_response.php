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
 * Servicio externo para aprobar o rechazar respuestas generadas por AI en foros.
 *
 * Define la función webservice `local_forum_ai_approve_response`
 * que permite aprobar o rechazar respuestas pendientes de aprobación.
 *
 * @package    local_forum_ai
 * @category   external
 * @copyright  2025 Piero Llanos <piero@datacurso.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use context_system;
use moodle_exception;

/**
 * External API class to approve or reject AI responses in forum discussions.
 */
class approve_response extends external_api {

    /**
     * Define los parámetros de entrada de la función webservice.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'token' => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación'),
            'action' => new external_value(PARAM_ALPHA, 'Acción: approve|reject'),
        ]);
    }

    /**
     * Ejecuta la acción de aprobar o rechazar una respuesta AI pendiente.
     *
     * @param string $token  Token de aprobación
     * @param string $action Acción a ejecutar: approve o reject
     * @return array Resultado con clave success => bool
     * @throws moodle_exception Si la acción no es válida o no hay permisos
     */
    public static function execute($token, $action) {
        global $DB, $CFG;

        $params = self::validate_parameters(self::execute_parameters(), [
            'token' => $token,
            'action' => $action,
        ]);

        $pending = $DB->get_record('local_forum_ai_pending',
            ['approval_token' => $params['token'], 'status' => 'pending'], '*', MUST_EXIST);

        $context = context_system::instance();
        self::validate_context($context);

        require_capability('mod/forum:replypost', $context);

        if ($params['action'] === 'approve') {
            require_once($CFG->dirroot . '/mod/forum/lib.php');

            $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);

            $post = new \stdClass();
            $post->discussion = $discussion->id;
            $post->parent     = $discussion->firstpost;
            $post->userid     = $pending->bot_userid;
            $post->created    = time();
            $post->modified   = time();
            $post->subject    = $pending->subject;
            $post->message    = $pending->message;
            $post->messageformat = FORMAT_HTML;
            $post->messagetrust  = 1;

            forum_add_new_post($post, null);

            $pending->status = 'approved';
            $pending->approved_at = time();
            $DB->update_record('local_forum_ai_pending', $pending);

        } else if ($params['action'] === 'reject') {
            $pending->status = 'rejected';
            $DB->update_record('local_forum_ai_pending', $pending);
        } else {
            throw new moodle_exception('invalidaction', 'local_forum_ai');
        }

        return ['success' => true];
    }

    /**
     * Define la estructura de retorno de la función webservice.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Si la acción fue exitosa'),
        ]);
    }
}
