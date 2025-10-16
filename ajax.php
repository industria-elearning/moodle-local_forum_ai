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
 * Ajax endpoint for local_forum_ai plugin.
 *
 * Receives an approval token and returns discussion, forum,
 * course, posts, and AI response in JSON format.
 *
 * @package    local_forum_ai
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_login();

$token = required_param('token', PARAM_ALPHANUMEXT);

$pending = $DB->get_record('local_forum_ai_pending', ['approval_token' => $token], '*', MUST_EXIST);
$discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
$forum = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);

$context = context_module::instance($cm->id);
require_capability('mod/forum:viewdiscussion', $context);

// Obtener posts del hilo.
$posts = $DB->get_records('forum_posts', ['discussion' => $discussion->id], 'created ASC');

// Construir datos para JSON.
$data = [
    'discussion' => format_string($discussion->name),
    'forum'      => format_string($forum->name),
    'course'     => format_string($course->fullname),
    'posts'      => [],
    'airesponse' => format_text($pending->message, FORMAT_HTML),
];

foreach ($posts as $post) {
    $user = $DB->get_record('user', ['id' => $post->userid], 'id,firstname,lastname');
    $data['posts'][] = [
        'subject' => format_string($post->subject),
        'message' => format_text($post->message, $post->messageformat),
        'author'  => fullname($user),
        'created' => userdate($post->created),
    ];
}

echo json_encode($data);
die;
