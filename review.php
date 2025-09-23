<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$token = required_param('token', PARAM_ALPHANUMEXT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

try {
    $pending = $DB->get_record('local_forum_ai_pending',
        ['approval_token' => $token, 'status' => 'pending'], '*', MUST_EXIST);

    if ($USER->id != $pending->creator_userid) {
        throw new moodle_exception('nopermission', 'error');
    }

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

    // Guardar cambios si se actualizó el mensaje
    if ($action === 'update' && confirm_sesskey()) {
        $newmessage = required_param('message', PARAM_RAW);
        $pending->message = $newmessage;
        $DB->update_record('local_forum_ai_pending', $pending);
        redirect(new moodle_url('/local/forum_ai/review.php', ['token' => $token]),
            'Mensaje de la IA actualizado correctamente',
            null,
            \core\output\notification::NOTIFY_SUCCESS);
    }

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

    // Info del debate
    echo '<div class="alert alert-info">';
    echo '<h5><i class="icon fa fa-info-circle"></i> Información del debate</h5>';
    echo '<p><strong>Curso:</strong> ' . format_string($course->fullname) . '</p>';
    echo '<p><strong>Foro:</strong> ' . format_string($forum->name) . '</p>';
    echo '<p><strong>Debate:</strong> ' . format_string($discussion->name) . '</p>';
    echo '<p><strong>Creado:</strong> ' . userdate($pending->timecreated) . '</p>';
    echo '</div>';

    echo '<div class="row">';
    echo '<div class="col-md-6">';

    // Post original
    echo '<div class="card mb-3">';
    echo '<div class="card-header"><h5><i class="fa fa-comment"></i> Tu mensaje original</h5></div>';
    echo '<div class="card-body">';
    echo '<h6>' . format_string($original_post->subject) . '</h6>';
    echo format_text($original_post->message, $original_post->messageformat);
    echo '<br><small class="text-muted">Por: ' . fullname($USER) . ' el ' . userdate($original_post->created) . '</small>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
    echo '<div class="col-md-6">';

    // Respuesta AI con modo edición
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">';
    echo '<h5 class="mb-0"><i class="fa fa-robot"></i> Respuesta AI propuesta</h5>';
    echo '<button id="edit-btn" class="btn btn-sm btn-light"><i class="fa fa-pencil"></i></button>';
    echo '</div>';
    echo '<div class="card-body">';

    // Vista normal
    echo '<div id="airesponse-view">';
    echo '<h6>' . format_string($pending->subject) . '</h6>';
    echo '<div class="card-text">' . format_text($pending->message, FORMAT_HTML) . '</div>';
    echo '<small class="text-muted">Por: ' . fullname($bot_user) . ' (Bot AI)</small>';
    echo '</div>';

    // Vista edición (oculta por defecto)
    echo '<form id="airesponse-edit" method="post" action="' . $PAGE->url . '" style="display:none;">';
    echo '<input type="hidden" name="action" value="update">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
    echo '<textarea name="message" class="form-control" rows="6">' . s($pending->message) . '</textarea>';
    echo '<button type="submit" class="btn btn-success btn-sm mt-2"><i class="fa fa-save"></i> Guardar</button>';
    echo '<button type="button" id="cancel-edit" class="btn btn-secondary btn-sm mt-2">Cancelar</button>';
    echo '</form>';

    echo '</div></div>'; // card-body y card
    echo '</div></div>'; // col y row

    // Botones aprobar/rechazar
    echo '<div class="row mt-4"><div class="col text-center">';
    echo '<a href="' . $approve_url . '" class="btn btn-success btn-lg"><i class="fa fa-check"></i> Aprobar</a> ';
    echo '<a href="' . $reject_url . '" class="btn btn-danger btn-lg ml-2"><i class="fa fa-times"></i> Rechazar</a>';
    echo '</div></div>';

    echo '<div class="row mt-3"><div class="col text-center">';
    echo '<a href="' . $forum_url . '" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Volver al debate</a>';
    echo '</div></div>';

    echo $OUTPUT->footer();

    // JS inline para manejar edición
    ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.getElementById("edit-btn");
    const viewDiv = document.getElementById("airesponse-view");
    const editForm = document.getElementById("airesponse-edit");
    const cancelBtn = document.getElementById("cancel-edit");

    if (editBtn) {
        editBtn.addEventListener("click", () => {
            viewDiv.style.display = "none";
            editForm.style.display = "block";
        });
    }
    if (cancelBtn) {
        cancelBtn.addEventListener("click", () => {
            editForm.style.display = "none";
            viewDiv.style.display = "block";
        });
    }
});
</script>
<?php

} catch (Exception $e) {
    throw new moodle_exception($e->getMessage(), 'local_forum_ai');
}