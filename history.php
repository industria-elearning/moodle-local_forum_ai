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
 * Configuración del plugin Forum AI.
 *
 * @package    local_forum_ai
 * @category   admin
 * @copyright  2025 Piero Llanos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$context = context_course::instance($courseid);

$allowedroles = ['manager', 'editingteacher', 'coursecreator'];

$hasrole = false;
$userroles = get_user_roles($context, $USER->id, true);
foreach ($userroles as $ur) {
    $shortname = $DB->get_field('role', 'shortname', ['id' => $ur->roleid]);
    if ($shortname && in_array($shortname, $allowedroles, true)) {
        $hasrole = true;
        break;
    }
}

$PAGE->set_url(new moodle_url('/local/forum_ai/history.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('historyresponses', 'local_forum_ai'));
$PAGE->set_heading(get_string('historyresponses', 'local_forum_ai'));

if (!$hasrole) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(
        'No tienes permisos para ver esta página. Solo gestores y profesores pueden acceder.',
        \core\output\notification::NOTIFY_ERROR
    );
    echo $OUTPUT->footer();
    exit;
}

global $DB;

$sql = "SELECT p.*, d.name AS discussionname, f.name AS forumname, c.fullname AS coursename,
               u.firstname, u.lastname
          FROM {local_forum_ai_pending} p
          JOIN {forum_discussions} d ON d.id = p.discussionid
          JOIN {forum} f ON f.id = p.forumid
          JOIN {course} c ON c.id = f.course
          JOIN {user} u ON u.id = p.creator_userid
         WHERE p.status IN ('approved', 'rejected')
      ORDER BY p.timecreated DESC";

$records = $DB->get_records_sql($sql);

$statusmap = [
    'approved' => get_string('statusapproved', 'local_forum_ai'),
    'rejected' => get_string('statusrejected', 'local_forum_ai'),
    'pending'  => get_string('statuspending', 'local_forum_ai'),
];

// Contexto para la plantilla.
$templatecontext = [
    'col_course' => get_string('coursename', 'local_forum_ai'),
    'col_forum' => get_string('forumname', 'local_forum_ai'),
    'col_discussion' => get_string('discussionname', 'local_forum_ai'),
    'col_message' => get_string('discussionmsg', 'local_forum_ai'),
    'col_user' => get_string('username', 'local_forum_ai'),
    'col_status' => get_string('status', 'local_forum_ai'),
    'col_actions' => get_string('actions', 'local_forum_ai'),
    'noresponses' => get_string('nohistory', 'local_forum_ai'),
    'hashistory' => !empty($records),
    'responses' => [],
];

foreach ($records as $r) {
    $user = (object)['id' => $r->creator_userid, 'firstname' => $r->firstname, 'lastname' => $r->lastname];

    $templatecontext['responses'][] = [
        'coursename' => format_string($r->coursename),
        'forumname' => format_string($r->forumname),
        'discussionname' => format_string($r->discussionname),
        'discussionmsg' => shorten_text(strip_tags($r->message), 100),
        'userfullname' => fullname($user),
        'status' => $statusmap[$r->status] ?? $r->status,
        'viewdetails' => get_string('viewdetails', 'local_forum_ai'),
        'token' => $r->approval_token,
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_forum_ai/history', $templatecontext);
$PAGE->requires->js_call_amd('local_forum_ai/history', 'init');
echo $OUTPUT->footer();
