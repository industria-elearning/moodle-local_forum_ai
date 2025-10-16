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
 * Página que lista las respuestas pendientes de aprobación en Forum AI.
 *
 * @package    local_forum_ai
 * @category   admin
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$forumid  = optional_param('forumid', 0, PARAM_INT);

$context = context_system::instance();

$coursecontext = context_course::instance($courseid);
$allowedroles = ['manager', 'editingteacher', 'coursecreator'];

$hasrole = false;
$userroles = get_user_roles($coursecontext, $USER->id, true);
foreach ($userroles as $ur) {
    $shortname = $DB->get_field('role', 'shortname', ['id' => $ur->roleid]);
    if ($shortname && in_array($shortname, $allowedroles, true)) {
        $hasrole = true;
        break;
    }
}

$params = ['courseid' => $courseid];
if ($forumid) {
    $params['forumid'] = $forumid;
}

$PAGE->set_url(new moodle_url('/local/forum_ai/pending.php', $params));
$PAGE->set_context($context);
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('pendingresponses', 'local_forum_ai'));
$PAGE->set_heading(get_string('pendingresponses', 'local_forum_ai'));
$PAGE->requires->css('/local/forum_ai/styles/review.css');

$PAGE->navbar->ignore_active();

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

// Limpieza de caducados y eliminados.
local_forum_ai_cleanup_pending();
$removed = local_forum_ai_cleanup_expired();
if ($removed > 0) {
    debugging("Forum AI: Se eliminaron {$removed} respuestas caducadas.", DEBUG_DEVELOPER);
}

$pendings = local_forum_ai_get_pending($courseid, $forumid);
$renderer = $PAGE->get_renderer('core');
$headerlogo = new \local_assign_ai\output\header_logo();
$logocontext = $headerlogo->export_for_template($renderer);

$templatecontext = [
    'col_course' => get_string('coursename', 'local_forum_ai'),
    'col_forum' => get_string('forumname', 'local_forum_ai'),
    'col_discussion' => get_string('discussionname', 'local_forum_ai'),
    'col_message' => get_string('col_message', 'local_forum_ai'),
    'col_user' => get_string('username', 'local_forum_ai'),
    'col_preview' => get_string('preview', 'local_forum_ai'),
    'col_actions' => get_string('actions', 'local_forum_ai'),
    'approve' => get_string('approve', 'local_forum_ai'),
    'reject' => get_string('reject', 'local_forum_ai'),
    'noresponses' => get_string('noresponses', 'local_forum_ai'),
    'haspendings' => !empty($pendings),
    'pendings' => [],
    'headerlogo' => $logocontext,
];

foreach ($pendings as $p) {
    $user = (object)['id' => $p->creator_userid, 'firstname' => $p->firstname, 'lastname' => $p->lastname];

    $templatecontext['pendings'][] = [
        'coursename' => format_string($p->coursename),
        'forumname' => format_string($p->forumname),
        'discussionname' => format_string($p->discussionname),
        'discussionmsg'   => format_text($p->discussionmessage, $p->messageformat),
        'userfullname' => fullname($user),
        'preview' => shorten_text(strip_tags($p->message), 100),
        'viewdetails'    => get_string('viewdetails', 'local_forum_ai'),
        'token' => $p->approval_token,
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_forum_ai/pending', $templatecontext);
$PAGE->requires->js_call_amd('local_forum_ai/pending', 'init');
echo $OUTPUT->footer();
