<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$token = required_param('token', PARAM_ALPHANUMEXT);
$action = required_param('action', PARAM_ALPHA);

require_login();
require_sesskey(); // Seguridad adicional

try {
    $pending = $DB->get_record('local_forum_ai_pending',
        ['approval_token' => $token, 'status' => 'pending'], '*', MUST_EXIST);

    if ($USER->id != $pending->creator_userid) {
        throw new moodle_exception('nopermission', 'error');
    }

    $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
    $forum = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);

    if ($action === 'approve') {
        $success = \local_forum_ai\observer::create_auto_reply($discussion, $pending->bot_userid, $pending->message);

        if ($success) {
            $pending->status = 'approved';
            $pending->approved_at = time();
            $pending->timemodified = time();
            $DB->update_record('local_forum_ai_pending', $pending);

            $message = 'La respuesta AI ha sido aprobada y publicada exitosamente.';
            $notification_type = \core\output\notification::NOTIFY_SUCCESS;
        } else {
            $message = 'Error al publicar la respuesta. Inténtalo de nuevo.';
            $notification_type = \core\output\notification::NOTIFY_ERROR;
        }

    } elseif ($action === 'reject') {
        $pending->status = 'rejected';
        $pending->timemodified = time();
        $DB->update_record('local_forum_ai_pending', $pending);

        $message = 'La respuesta AI ha sido rechazada.';
        $notification_type = \core\output\notification::NOTIFY_INFO;
    } else {
        throw new moodle_exception('invalidaction', 'error');
    }

    $forum_url = new moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id]);
    redirect($forum_url, $message, null, $notification_type);

} catch (Exception $e) {
    print_error('error', 'local_forum_ai', '', $e->getMessage());
}
