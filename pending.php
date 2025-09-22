<?php
require_once(__DIR__ . '/../../config.php');

require_login();

// Solo profesores/managers deberían entrar.
$courseid = optional_param('courseid', 0, PARAM_INT);

$context = $courseid ? context_course::instance($courseid) : context_system::instance();
require_capability('mod/forum:replypost', $context);

// $PAGE->set_url('/local/forum_ai/pending.php', ['courseid' => $courseid]);
$PAGE->set_url('/local/forum_ai/pending.php');
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pendingresponses', 'local_forum_ai'));
$PAGE->set_heading(get_string('pendingresponses', 'local_forum_ai'));

global $DB;

$sql = "SELECT p.*, d.name AS discussionname, f.name AS forumname,
               c.fullname AS coursename, u.firstname, u.lastname,
               fp.subject AS discussionsubject, fp.message AS discussionmessage, fp.messageformat
          FROM {local_forum_ai_pending} p
          JOIN {forum_discussions} d ON d.id = p.discussionid
          JOIN {forum} f ON f.id = p.forumid
          JOIN {course} c ON c.id = f.course
          JOIN {user} u ON u.id = p.creator_userid
          JOIN {forum_posts} fp ON fp.id = d.firstpost
         WHERE p.status = :status
      ORDER BY p.timecreated DESC";


$params = ['status' => 'pending'];
$pendings = $DB->get_records_sql($sql, $params);

// Contexto para la plantilla.
$templatecontext = [
    // 'heading' => get_string('pendingresponses', 'local_forum_ai'),
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
