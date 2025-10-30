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
 * External service to obtain details of a discussion with AI response.
 *
 * Define the webservice function `local_forum_ai_get_details`
 * which returns course, forum, discussion, posts and AI response status.
 *
 * @package    local_forum_ai
 * @category   external
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use context_module;
use moodle_exception;

/**
 * External API class to get details of AI responses in forum discussions.
 */
class get_details extends external_api {
    /**
     * Define the input parameters of the webservice function.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'token' => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación'),
        ]);
    }

    /**
     * Executes the AI ​​discussion and response detail retrieval.
     *
     * @param string $token Approval token
     * @return array Course information, forum, discussion, and posts
     * @throws moodle_exception If the record is not found or permission is missing
     */
    public static function execute($token) {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), ['token' => $token]);

        $pending = $DB->get_record('local_forum_ai_pending', ['approval_token' => $params['token']], '*', MUST_EXIST);
        $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
        $forum = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/forum:viewdiscussion', $context);

        $posts = $DB->get_records('forum_posts', ['discussion' => $discussion->id], 'created ASC');

        $data = [
            'course' => format_string($course->fullname),
            'forum' => format_string($forum->name),
            'discussion' => format_string($discussion->name),
            'posts' => self::buildhierarchicalposts($posts),
            'airesponse' => format_text($pending->message, FORMAT_HTML),
            'token' => $pending->approval_token,
            'status' => $pending->status,
        ];

        return $data;
    }

    /**
     * Build the hierarchical structure of posts in the correct order.
     *
     * @param array $posts List of posts
     * @return array Posts in a flat hierarchical structure
     */
    private static function buildhierarchicalposts($posts) {
        global $DB;

        $postsbyid = [];
        $hierarchical = [];

        foreach ($posts as $post) {
            $user = $DB->get_record('user', ['id' => $post->userid], 'id,firstname,lastname');
            $formattedpost = [
                'id' => $post->id,
                'parent' => $post->parent,
                'subject' => format_string($post->subject),
                'message' => format_text($post->message, $post->messageformat),
                'author' => fullname($user),
                'created' => userdate($post->created),
                'created_timestamp' => $post->created,
                'children' => [],
                'level' => 0,
            ];
            $postsbyid[$post->id] = $formattedpost;
        }

        foreach ($postsbyid as &$post) {
            if ($post['parent'] == 0) {
                $hierarchical[] = &$post;
            } else {
                if (isset($postsbyid[$post['parent']])) {
                    $postsbyid[$post['parent']]['children'][] = &$post;
                }
            }
        }

        self::sortchildrenrecursive($hierarchical);

        return self::flattenhierarchical($hierarchical, 0);
    }

    /**
     * Recursively sort children by creation date.
     *
     * @param array $posts
     */
    private static function sortchildrenrecursive(&$posts) {
        foreach ($posts as &$post) {
            if (!empty($post['children'])) {
                usort($post['children'], function ($a, $b) {
                    return $a['created_timestamp'] - $b['created_timestamp'];
                });

                self::sortchildrenrecursive($post['children']);
            }
        }
    }

    /**
     * Convert the hierarchical structure to a flat one while maintaining order.
     *
     * @param array $posts
     * @param int $level Current level
     * @return array Flattened posts with levels
     */
    private static function flattenhierarchical($posts, $level) {
        $result = [];

        foreach ($posts as $post) {

            $post['level'] = $level;

            $children = $post['children'];
            unset($post['children']);

            $result[] = $post;

            if (!empty($children)) {
                $childposts = self::flattenhierarchical($children, $level + 1);
                $result = array_merge($result, $childposts);
            }
        }

        return $result;
    }

    /**
     * Define the return structure of the web service function.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
    return new external_single_structure([
        'course' => new external_value(PARAM_TEXT, 'Course name'),
        'forum' => new external_value(PARAM_TEXT, 'Forum name'),
        'discussion' => new external_value(PARAM_TEXT, 'Discussion title'),
        'posts' => new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Post ID'),
                'parent' => new external_value(PARAM_INT, 'Parent post ID'),
                'subject' => new external_value(PARAM_TEXT, 'Subject'),
                'message' => new external_value(PARAM_RAW, 'Message'),
                'author' => new external_value(PARAM_TEXT, 'Author'),
                'created' => new external_value(PARAM_TEXT, 'Creation date'),
                'level' => new external_value(PARAM_INT, 'Nesting level'),
            ])
        ),
        'airesponse' => new external_value(PARAM_RAW, 'Proposed AI response'),
        'token' => new external_value(PARAM_ALPHANUMEXT, 'Approval token'),
        'status' => new external_value(PARAM_ALPHA, 'Message status (pending, approved, rejected)'),
    ]);
}
}
