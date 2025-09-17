<?php
require_once(__DIR__ . '/../../config.php');

require_login();

// Solo profesores/managers deberían entrar.
$courseid = optional_param('courseid', 0, PARAM_INT);

$context = $courseid ? context_course::instance($courseid) : context_system::instance();
require_capability('mod/forum:replypost', $context);

$PAGE->set_url('/local/forum_ai/pending.php', ['courseid' => $courseid]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title('Respuestas AI pendientes');
$PAGE->set_heading('Respuestas AI pendientes');

echo $OUTPUT->header();
echo $OUTPUT->heading('Respuestas AI pendientes de aprobación');

// Consultar todos los pendientes
global $DB;
$sql = "SELECT p.*, d.name AS discussionname, f.name AS forumname, c.fullname AS coursename, u.firstname, u.lastname
          FROM {local_forum_ai_pending} p
          JOIN {forum_discussions} d ON d.id = p.discussionid
          JOIN {forum} f ON f.id = p.forumid
          JOIN {course} c ON c.id = f.course
          JOIN {user} u ON u.id = p.creator_userid
         WHERE p.status = :status
      ORDER BY p.timecreated DESC";

$params = ['status' => 'pending'];
$pendings = $DB->get_records_sql($sql, $params);

if (!$pendings) {
    echo $OUTPUT->notification('No hay respuestas pendientes de aprobación.', 'info');
    echo $OUTPUT->footer();
    exit;
}

// Construir tabla
$table = new html_table();
$table->head = [
    'Curso', 'Foro', 'Debate', 'Usuario', 'Vista previa', 'Acciones'
];
$table->align = ['left', 'left', 'left', 'left', 'left', 'center'];

foreach ($pendings as $p) {
    $preview = shorten_text(strip_tags($p->message), 100);

    $approveurl = new moodle_url('/local/forum_ai/approve.php', [
        'token' => $p->approval_token,
        'action' => 'approve',
        'sesskey' => sesskey()
    ]);

    $rejecturl = new moodle_url('/local/forum_ai/approve.php', [
        'token' => $p->approval_token,
        'action' => 'reject',
        'sesskey' => sesskey()
    ]);

    $row = [];
    $row[] = format_string($p->coursename);
    $row[] = format_string($p->forumname);
    $row[] = format_string($p->discussionname);
    $row[] = fullname($p);
    $row[] = format_text($preview, FORMAT_PLAIN);
    $row[] = html_writer::link($approveurl, '✅ Aprobar') . ' | ' .
             html_writer::link($rejecturl, '❌ Rechazar');

    $table->data[] = $row;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
