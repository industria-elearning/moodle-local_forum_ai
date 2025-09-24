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
 * Approve/reject AI-generated responses in forum discussions.
 *
 * Handles requests to approve or reject a pending AI-generated
 * forum reply, updating the database and redirecting back to the
 * forum discussion with a status notification.
 *
 * @package    local_forum_ai
 * @copyright  2025 Piero Llanos <piero@datacurso.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$token = required_param('token', PARAM_ALPHANUMEXT);
$action = required_param('action', PARAM_ALPHA);

require_login();
require_sesskey();

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
            $notificationtype = \core\output\notification::NOTIFY_SUCCESS;
        } else {
            $message = 'Error al publicar la respuesta. Inténtalo de nuevo.';
            $notificationtype = \core\output\notification::NOTIFY_ERROR;
        }

    } else if ($action === 'reject') {
        $pending->status = 'rejected';
        $pending->timemodified = time();
        $DB->update_record('local_forum_ai_pending', $pending);

        $message = 'La respuesta AI ha sido rechazada.';
        $notificationtype = \core\output\notification::NOTIFY_INFO;
    } else {
        throw new moodle_exception('invalidaction', 'error');
    }

    $forumurl = new moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id]);
    redirect($forumurl, $message, null, $notificationtype);

} catch (Exception $e) {
    throw new moodle_exception('error', 'local_forum_ai', '', $e->getMessage());
}
