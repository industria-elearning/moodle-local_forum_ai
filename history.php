<?php
require_once(__DIR__ . '/../../config.php');

require_login();

$courseid = optional_param('courseid', 0, PARAM_INT);

$context = $courseid ? context_course::instance($courseid) : context_system::instance();
require_capability('mod/forum:viewdiscussion', $context);

$PAGE->set_url('/local/forum_ai/history.php', ['courseid' => $courseid]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('historyresponses', 'local_forum_ai'));
$PAGE->set_heading(get_string('historyresponses', 'local_forum_ai'));

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
