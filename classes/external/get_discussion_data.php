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
 * External service to approve or reject AI-generated responses in forums.
 *
 * Define the webservice function `local_forum_ai_approve response`
 * which allows you to approve or reject pending responses.
 *
 * @package    local_forum_ai
 * @category   external
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;

/**
 * External service for retrieving discussion and AI response data.
 *
 * @package   local_forum_ai
 * @copyright 2025
 */
class get_discussion_data extends external_api {
    /**
     * Define parameters for the function.
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'token' => new external_value(PARAM_ALPHANUMEXT, 'Approval token', VALUE_REQUIRED),
        ]);
    }

    /**
     * Main function logic.
     */
    public static function execute($token) {
        global $DB, $USER;

        // Parameter validation.
        $params = self::validate_parameters(self::execute_parameters(), ['token' => $token]);

        // Retrieve records.
        $pending = $DB->get_record('local_forum_ai_pending', ['approval_token' => $params['token']], '*', MUST_EXIST);
        $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
        $forum = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);

        $context = \context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/forum:viewdiscussion', $context);

        // Retrieve posts.
        $posts = $DB->get_records('forum_posts', ['discussion' => $discussion->id], 'created ASC');

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

        return $data;
    }

    /**
     * Define the structure of the returned data.
     */
    public static function execute_returns() {
        return new external_single_structure([
            'discussion' => new external_value(PARAM_TEXT, 'Discussion name'),
            'forum'      => new external_value(PARAM_TEXT, 'Forum name'),
            'course'     => new external_value(PARAM_TEXT, 'Course name'),
            'airesponse' => new external_value(PARAM_RAW, 'AI-generated message'),
            'posts' => new external_multiple_structure(
                new external_single_structure([
                    'subject' => new external_value(PARAM_TEXT, 'Post subject'),
                    'message' => new external_value(PARAM_RAW, 'Post content'),
                    'author'  => new external_value(PARAM_TEXT, 'Post author'),
                    'created' => new external_value(PARAM_TEXT, 'Creation date'),
                ])
            ),
        ]);
    }
}
