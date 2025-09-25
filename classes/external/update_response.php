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
 * Servicio externo para actualizar la respuesta AI pendiente en un foro.
 *
 * Define la función webservice `local_forum_ai_update_response`
 * que permite modificar el mensaje de una respuesta AI antes de su aprobación.
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
use context_system;

/**
 * External API class to update pending AI responses in forum discussions.
 */
class update_response extends external_api {

    /**
     * Define los parámetros de entrada de la función webservice.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'token'   => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación'),
            'message' => new external_value(PARAM_RAW, 'Nuevo mensaje de la IA'),
        ]);
    }

    /**
     * Ejecuta la actualización de un mensaje AI pendiente.
     *
     * @param string $token Token de aprobación
     * @param string $message Nuevo mensaje de la IA
     * @return array Resultado con estado y mensaje actualizado
     */
    public static function execute($token, $message) {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), [
            'token' => $token,
            'message' => $message,
        ]);

        $pending = $DB->get_record('local_forum_ai_pending', ['approval_token' => $params['token']], '*', MUST_EXIST);

        // Validación de permisos.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('mod/forum:replypost', $context);

        $pending->message = $params['message'];
        $pending->timemodified = time();
        $DB->update_record('local_forum_ai_pending', $pending);

        return [
            'status'  => 'ok',
            'message' => $pending->message,
        ];
    }

    /**
     * Define la estructura de retorno de la función webservice.
     *
     * @return \external_single_structure
     */
    public static function execute_returns() {
        return new \external_single_structure([
            'status'  => new external_value(PARAM_TEXT, 'Estado de la operación'),
            'message' => new external_value(PARAM_RAW, 'Mensaje actualizado'),
        ]);
    }
}
