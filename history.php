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
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$forumid  = optional_param('forumid', 0, PARAM_INT);

$context = context_course::instance($courseid);

require_capability('local/forum_ai:approveresponses', $context);

$PAGE->set_url(new moodle_url('/local/forum_ai/history.php', $params));
$PAGE->set_context($context);
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('historyresponses', 'local_forum_ai'));
$PAGE->set_heading(get_string('historyresponses', 'local_forum_ai'));
$PAGE->requires->css('/local/forum_ai/styles/review.css');

$PAGE->navbar->ignore_active();

local_forum_ai_cleanup_pending();

$records = local_forum_ai_get_history($courseid, $forumid);

$statusmap = [
    'approved' => get_string('statusapproved', 'local_forum_ai'),
    'rejected' => get_string('statusrejected', 'local_forum_ai'),
    'pending'  => get_string('statuspending', 'local_forum_ai'),
];

$renderer = $PAGE->get_renderer('core');
$headerlogo = new \local_assign_ai\output\header_logo();
$logocontext = $headerlogo->export_for_template($renderer);

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
    'headerlogo' => $logocontext,
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
