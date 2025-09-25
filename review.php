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
    $pending = $DB->get_record('local_forum_ai_pending',
        ['approval_token' => $token, 'status' => 'pending'], '*', MUST_EXIST);

    $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
    $forum = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
    $originalpost = $DB->get_record('forum_posts', ['id' => $discussion->firstpost], '*', MUST_EXIST);
    $botuser = $DB->get_record('user', ['id' => $pending->bot_userid], '*', MUST_EXIST);
    $author = $DB->get_record('user', ['id' => $originalpost->userid], '*', MUST_EXIST);

    $context = context_course::instance($course->id);

    $roles = get_user_roles($context, $USER->id);
    $allowedroles = ['manager', 'editingteacher', 'coursecreator'];
    $hasrole = false;
    foreach ($roles as $role) {
        if (in_array($role->shortname, $allowedroles)) {
            $hasrole = true;
            break;
        }
    }

    if ($USER->id != $pending->creator_userid && !$hasrole) {
        throw new moodle_exception('nopermission', 'local_forum_ai');
    }

    $PAGE->set_url('/local/forum_ai/review.php', ['token' => $token]);
    $PAGE->set_context($context);
    $PAGE->set_pagelayout('incourse');
    $PAGE->set_title('Revisar respuesta AI');
    $PAGE->set_heading($course->fullname);
    $PAGE->requires->js_call_amd('local_forum_ai/review', 'init');

    // Guardar cambios si se actualizó el mensaje.
    if ($action === 'update' && confirm_sesskey()) {
        $newmessage = required_param('message', PARAM_RAW);
        $pending->message = $newmessage;
        $pending->timemodified = time();
        $DB->update_record('local_forum_ai_pending', $pending);

        redirect(new moodle_url('/local/forum_ai/review.php', ['token' => $token]),
            'Mensaje de la IA actualizado correctamente',
            null,
            \core\output\notification::NOTIFY_SUCCESS);
    }

    $approveurl = new moodle_url('/local/forum_ai/approve.php', [
        'token' => $token,
        'action' => 'approve',
        'sesskey' => sesskey(),
    ]);

    $rejecturl = new moodle_url('/local/forum_ai/approve.php', [
        'token' => $token,
        'action' => 'reject',
        'sesskey' => sesskey(),
    ]);

    $forumurl = new moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id]);

    echo $OUTPUT->header();
    echo $OUTPUT->heading('Revisar respuesta AI');

    // Información del debate.
    echo '<div class="alert alert-info">';
    echo '<h5><i class="icon fa fa-info-circle"></i> Información del debate</h5>';
    echo '<p><strong>Curso:</strong> ' . format_string($course->fullname) . '</p>';
    echo '<p><strong>Foro:</strong> ' . format_string($forum->name) . '</p>';
    echo '<p><strong>Debate:</strong> ' . format_string($discussion->name) . '</p>';
    echo '<p><strong>Creado:</strong> ' . userdate($pending->timecreated) . '</p>';
    echo '</div>';

    echo '<div class="row">';
    echo '<div class="col-md-6">';

    // Post original.
    echo '<div class="card mb-3">';
    echo '<div class="card-header"><h5><i class="fa fa-comment"></i> Mensaje original</h5></div>';
    echo '<div class="card-body">';
    echo '<h6>' . format_string($originalpost->subject) . '</h6>';
    echo format_text($originalpost->message, $originalpost->messageformat);
    echo '<br><small class="text-muted">Por: ' . fullname($author) . ' el ' . userdate($originalpost->created) . '</small>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
    echo '<div class="col-md-6">';

    // Respuesta AI con modo edición.
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">';
    echo '<h5 class="mb-0"><i class="fa fa-robot"></i> Respuesta AI propuesta</h5>';
    echo '<button id="edit-btn" class="btn btn-sm btn-light"><i class="fa fa-pencil"></i></button>';
    echo '</div>';
    echo '<div class="card-body">';

    // Vista normal.
    echo '<div id="airesponse-view">';
    echo '<h6>' . format_string($pending->subject) . '</h6>';
    echo '<div class="card-text">' . format_text($pending->message, FORMAT_HTML) . '</div>';
    echo '<small class="text-muted">Por: ' . fullname($botuser) . ' (Bot AI)</small>';
    echo '</div>';

    // Vista edición (oculta por defecto).
    echo '<form id="airesponse-edit" method="post" action="' . $PAGE->url . '" style="display:none;">';
    echo '<input type="hidden" name="action" value="update">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
    echo '<textarea name="message" class="form-control" rows="6">' . s($pending->message) . '</textarea>';
    echo '<button type="submit" class="btn btn-success btn-sm mt-2"><i class="fa fa-save"></i> Guardar</button> ';
    echo '<button type="button" id="cancel-edit" class="btn btn-secondary btn-sm mt-2">Cancelar</button>';
    echo '</form>';

    echo '</div></div>'; // Card-body y card.
    echo '</div></div>'; // Col y row.

    // Botones aprobar o rechazar.
    echo '<div class="row mt-4"><div class="col text-center">';
    echo '<a href="' . $approveurl . '" class="btn btn-success btn-lg"><i class="fa fa-check"></i> Aprobar</a> ';
    echo '<a href="' . $rejecturl . '" class="btn btn-danger btn-lg ml-2"><i class="fa fa-times"></i> Rechazar</a>';
    echo '</div></div>';

    echo '<div class="row mt-3"><div class="col text-center">';
    echo '<a href="' . $forumurl . '" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Volver al debate</a>';
    echo '</div></div>';

    echo $OUTPUT->footer();

} catch (Exception $e) {
    throw new moodle_exception($e->getMessage(), 'local_forum_ai');
}
