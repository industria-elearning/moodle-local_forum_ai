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
 * External service to update a pending AI response in a forum.
 *
 * Defines the webservice function `local_forum_ai_update_response`
 * that allows modifying the message of an AI response before its approval.
 *
 * @package    local_forum_ai
 * @category   external
 * @copyright  2025 Datacurso
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
     * Defines the input parameters of the webservice function.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'token'   => new external_value(PARAM_ALPHANUMEXT, 'Approval token'),
            'message' => new external_value(PARAM_RAW, 'New AI message'),
        ]);
    }

    /**
     * Executes the update of a pending AI message.
     *
     * @param string $token Approval token
     * @param string $message New AI message
     * @return array Result with status and updated message
     */
    public static function execute($token, $message) {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), [
            'token' => $token,
            'message' => $message,
        ]);

        $pending = $DB->get_record('local_forum_ai_pending', ['approval_token' => $params['token']], '*', MUST_EXIST);

        // Permission validation.
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
     * Defines the return structure of the webservice function.
     *
     * @return \external_single_structure
     */
    public static function execute_returns() {
        return new \external_single_structure([
            'status'  => new external_value(PARAM_TEXT, 'Operation status'),
            'message' => new external_value(PARAM_RAW, 'Updated message'),
        ]);
    }
}
