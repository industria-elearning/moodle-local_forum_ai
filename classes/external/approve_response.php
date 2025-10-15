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
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once(__DIR__ . '/../../locallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use moodle_exception;


/**
 * Clase externa para aprobar o rechazar respuestas generadas por AI en foros.
 *
 * Expone el servicio `local_forum_ai_approve_response` que permite
 * aprobar o rechazar respuestas pendientes de aprobación mediante token.
 */
class approve_response extends external_api {

    /**
     * Define los parámetros de entrada de la función externa.
     *
     * @return external_function_parameters estructura de parámetros
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'token'  => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación'),
            'action' => new external_value(PARAM_ALPHA, 'Acción: approve|reject'),
        ]);
    }

    /**
     * Ejecuta la acción de aprobar o rechazar una respuesta.
     *
     * @param string $token  Token de aprobación asociado a la respuesta pendiente
     * @param string $action Acción a ejecutar: approve o reject
     * @return array resultado con clave 'success' en caso de éxito
     * @throws moodle_exception si no se cumplen las validaciones o permisos
     */
    public static function execute($token, $action) {
        global $DB, $CFG, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'token'  => $token,
            'action' => $action,
        ]);

        $pending = $DB->get_record('local_forum_ai_pending',
            ['approval_token' => $params['token'], 'status' => 'pending'], '*', MUST_EXIST);

        $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
        $forum      = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
        $course     = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
        $cm         = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
        $modcontext = \context_module::instance($cm->id);
        $coursectx  = \context_course::instance($course->id);

        self::validate_context($modcontext);

        $allowedroles = ['manager', 'editingteacher'];
        $userroles = get_user_roles($coursectx, $USER->id, true);
        $hasrole = false;
        foreach ($userroles as $ur) {
            $shortname = $DB->get_field('role', 'shortname', ['id' => $ur->roleid]);
            if ($shortname && in_array($shortname, $allowedroles, true)) {
                $hasrole = true;
                break;
            }
        }
        if (!$hasrole) {
            throw new moodle_exception('nopermission', 'error');
        }

        if ($params['action'] === 'approve') {
            require_once($CFG->dirroot . '/mod/forum/lib.php');

            $teacher = get_editingteachers($course->id, true);

            if (!$teacher) {
                throw new moodle_exception('noteachersfound', 'local_forum_ai');
            }

            $realuser = $USER;
            $USER = \core_user::get_user($teacher->id);

            $post = new \stdClass();
            $post->discussion    = $discussion->id;
            $post->parent        = $discussion->firstpost;
            $post->userid        = $teacher->id;
            $post->created       = time();
            $post->modified      = time();
            $post->subject       = $pending->subject ?: ("Re: " . $discussion->name);
            $post->message       = $pending->message;
            $post->messageformat = FORMAT_HTML;
            $post->messagetrust  = 1;

            forum_add_new_post($post, null);

            $USER = $realuser;

            $pending->status       = 'approved';
            $pending->approved_at  = time();
            $pending->timemodified = time();
            $DB->update_record('local_forum_ai_pending', $pending);

        } else if ($params['action'] === 'reject') {
            $pending->status       = 'rejected';
            $pending->timemodified = time();
            $DB->update_record('local_forum_ai_pending', $pending);
        } else {
            throw new moodle_exception('invalidaction', 'local_forum_ai');
        }

        return ['success' => true];
    }

    /**
     * Define la estructura de salida de la función externa.
     *
     * @return external_single_structure estructura de retorno
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Si la acción fue exitosa'),
        ]);
    }
}
