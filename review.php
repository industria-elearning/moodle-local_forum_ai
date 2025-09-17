<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$token = required_param('token', PARAM_ALPHANUMEXT);

require_login();

try {
    // Buscar la solicitud pendiente
    $pending = $DB->get_record('local_forum_ai_pending',
        ['approval_token' => $token, 'status' => 'pending'], '*', MUST_EXIST);

    // Verificar que el usuario actual sea el creador del debate
    if ($USER->id != $pending->creator_userid) {
        throw new moodle_exception('nopermission', 'error');
    }

    // Obtener información adicional
    $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
    $forum = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
    $original_post = $DB->get_record('forum_posts', ['id' => $discussion->firstpost], '*', MUST_EXIST);
    $bot_user = $DB->get_record('user', ['id' => $pending->bot_userid], '*', MUST_EXIST);

    $context = context_course::instance($course->id);
    $PAGE->set_url('/local/forum_ai/review.php', ['token' => $token]);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('incourse');
    $PAGE->set_title('Revisar respuesta AI');
    $PAGE->set_heading($course->fullname);

    // URLs de acción
    $approve_url = new moodle_url('/local/forum_ai/approve.php', [
        'token' => $token,
        'action' => 'approve',
        'sesskey' => sesskey()
    ]);

    $reject_url = new moodle_url('/local/forum_ai/approve.php', [
        'token' => $token,
        'action' => 'reject',
        'sesskey' => sesskey()
    ]);

    $forum_url = new moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id]);

    echo $OUTPUT->header();

    echo $OUTPUT->heading('Revisar respuesta AI');

    // Información del contexto
    echo '<div class="alert alert-info">';
    echo '<h5><i class="icon fa fa-info-circle" aria-hidden="true"></i> Información del debate</h5>';
    echo '<p><strong>Curso:</strong> ' . format_string($course->fullname) . '</p>';
    echo '<p><strong>Foro:</strong> ' . format_string($forum->name) . '</p>';
    echo '<p><strong>Debate:</strong> ' . format_string($discussion->name) . '</p>';
    echo '<p><strong>Creado:</strong> ' . userdate($pending->timecreated) . '</p>';
    echo '</div>';

    echo '<div class="row">';
    echo '<div class="col-md-6">';

    // Post original
    echo '<div class="card mb-3">';
    echo '<div class="card-header">';
    echo '<h5><i class="icon fa fa-comment" aria-hidden="true"></i> Tu mensaje original</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<h6 class="card-title">' . format_string($original_post->subject) . '</h6>';
    echo '<div class="card-text">';
    echo format_text($original_post->message, $original_post->messageformat);
    echo '</div>';
    echo '<small class="text-muted">Por: ' . fullname($USER) . ' el ' . userdate($original_post->created) . '</small>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
    echo '<div class="col-md-6">';

    // Respuesta propuesta
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-primary text-white">';
    echo '<h5><i class="icon fa fa-robot" aria-hidden="true"></i> Respuesta AI propuesta</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<h6 class="card-title">' . format_string($pending->subject) . '</h6>';
    echo '<div class="card-text">';
    echo format_text($pending->message, FORMAT_HTML);
    echo '</div>';
    echo '<small class="text-muted">Por: ' . fullname($bot_user) . ' (Bot AI)</small>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
    echo '</div>';

    // Botones de acción
    echo '<div class="row mt-4">';
    echo '<div class="col-12 text-center">';
    echo '<div class="btn-group" role="group">';
    echo '<a href="' . $approve_url . '" class="btn btn-success btn-lg">';
    echo '<i class="fa fa-check"></i> Aprobar y Publicar';
    echo '</a>';
    echo '<a href="' . $reject_url . '" class="btn btn-danger btn-lg ml-2">';
    echo '<i class="fa fa-times"></i> Rechazar';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="row mt-3">';
    echo '<div class="col-12 text-center">';
    echo '<a href="' . $forum_url . '" class="btn btn-secondary">';
    echo '<i class="fa fa-arrow-left"></i> Volver al debate';
    echo '</a>';
    echo '</div>';
    echo '</div>';

    echo $OUTPUT->footer();

} catch (Exception $e) {
    throw new moodle_exception($e->getMessage(), 'local_forum_ai');
}
