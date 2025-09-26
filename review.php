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
 * Página para revisar y aprobar o rechazar respuestas generadas por AI.
 *
 * @package    local_forum_ai
 * @category   admin
 * @copyright  2025 Piero Llanos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$token = required_param('token', PARAM_ALPHANUMEXT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

try {
    $pending = $DB->get_record(
        'local_forum_ai_pending',
        ['approval_token' => $token, 'status' => 'pending'],
        '*',
        IGNORE_MISSING
    );

    if (!$pending) {
        // Configuración de la página aunque no haya pending.
        $PAGE->set_url('/local/forum_ai/review.php', ['token' => $token]);
        $PAGE->set_pagelayout('incourse');
        $PAGE->set_title('Revisar respuesta AI');
        $PAGE->set_heading(get_string('pluginname', 'local_forum_ai'));

        echo $OUTPUT->header();

        // Mensaje bonito con Bootstrap.
        echo $OUTPUT->notification(
            'Esta solicitud ya fue aprobada, rechazada o no existe.',
            \core\output\notification::NOTIFY_INFO
        );

        echo $OUTPUT->continue_button(new moodle_url('/my'));
        exit;
    }

    $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
    $forum = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
    $originalpost = $DB->get_record('forum_posts', ['id' => $discussion->firstpost], '*', MUST_EXIST);
    $author = $DB->get_record('user', ['id' => $originalpost->userid], '*', MUST_EXIST);

    $context = context_course::instance($course->id);

    // Validar roles permitidos.
    $roles = get_user_roles($context, $USER->id);
    $allowedroles = ['manager', 'editingteacher'];
    $hasrole = false;
    foreach ($roles as $role) {
        if (in_array($role->shortname, $allowedroles, true)) {
            $hasrole = true;
            break;
        }
    }

    if (!$hasrole) {
        throw new moodle_exception('nopermission', 'local_forum_ai');
    }

    $PAGE->set_url('/local/forum_ai/review.php', ['token' => $token]);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('incourse');
    $PAGE->set_title('Revisar respuesta AI');
    $PAGE->set_heading($course->fullname);
    $PAGE->requires->js_call_amd('local_forum_ai/review', 'init');

    $forumurl = new moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id]);

    // Preparar datos para Mustache.
    $data = [
        'course' => format_string($course->fullname),
        'forum' => format_string($forum->name),
        'discussion' => format_string($discussion->name),
        'discussionid' => $discussion->id,
        'timecreated' => userdate($pending->timecreated),
        'originalsubject' => format_string($originalpost->subject),
        'originalmessage' => format_text($originalpost->message, $originalpost->messageformat),
        'author' => fullname($author),
        'originaldate' => userdate($originalpost->created),
        'aisubject' => format_string($pending->subject),
        'aimessage' => format_text($pending->message, FORMAT_HTML),
        'aiformatted' => s($pending->message),
        'token' => $token,
        'forumurl' => $forumurl->out(),
    ];

    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('local_forum_ai/review', $data);
    echo $OUTPUT->footer();

} catch (Exception $e) {
    throw new moodle_exception($e->getMessage(), 'local_forum_ai');
}
