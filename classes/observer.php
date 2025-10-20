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
 * Event observers for forum_ai plugin.
 *
 * @package    local_forum_ai
 * @category   event
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai;

use aiprovider_datacurso\httpclient\ai_services_api;
use mod_forum\event\discussion_created;

/**
 * Event observer class for forum_ai.
 */
class observer {
    /**
     * Sends the payload to the external AI service and returns its reply.
     *
     * @param array $payload Data to send to the AI service.
     * @return string The AI-generated reply.
     * @throws \moodle_exception If the request fails.
     */
    protected static function call_ai_service(array $payload) {
        $client = new ai_services_api();
        $response = $client->request('POST', '/forum/chat', $payload);
        return $response['reply'];
    }

    /**
     * Handles creation of "single type" forums.
     *
     * @param \core\event\course_module_created $event The event triggered when a module is created.
     * @return bool True on success or when no action is needed, false on error.
     */
    public static function course_module_created(\core\event\course_module_created $event): bool {
        global $DB;

        try {
            if ($event->other['modulename'] !== 'forum') {
                return true;
            }

            $forumid = $event->other['instanceid'];
            $forum = $DB->get_record('forum', ['id' => $forumid], '*', MUST_EXIST);

            if ($forum->type !== 'single') {
                return true;
            }

            $maxattempts = 5;
            $discussion = null;
            for ($i = 0; $i < $maxattempts; $i++) {
                $discussion = $DB->get_record('forum_discussions', ['forum' => $forum->id], '*', IGNORE_MULTIPLE);
                if ($discussion) {
                    break;
                }
                sleep(1);
            }

            if (!$discussion) {
                return true;
            }

            $singleevent = \mod_forum\event\discussion_created::create([
                'objectid' => $discussion->id,
                'context' => $event->get_context(),
                'courseid' => $event->courseid,
                'relateduserid' => $discussion->userid,
                'other' => ['forumid' => $forumid],
            ]);

            self::discussion_created($singleevent);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Handles discussion creation events.
     *
     * @param discussion_created $event The discussion created event.
     * @return bool True on success, false on error.
     */
    public static function discussion_created(discussion_created $event): bool {
        global $DB;

        try {
            $data = $event->get_data();
            $discussionid = $data['objectid'];
            $forumid = $data['other']['forumid'];

            $config = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);

            $enabled = $config->enabled ?? get_config('local_forum_ai', 'default_enabled');
            $replymessage = $config->reply_message ?? get_config('local_forum_ai', 'default_reply_message');
            $requireapproval = $config->require_approval ?? 1;

            if (!$enabled) {
                return true;
            }

            $discussion = $DB->get_record('forum_discussions', ['id' => $discussionid], '*', MUST_EXIST);
            $post = $DB->get_record('forum_posts', ['id' => $discussion->firstpost], '*', MUST_EXIST);
            $forum = $DB->get_record('forum', ['id' => $forumid], '*', MUST_EXIST);
            $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);

            $payload = [
                'course' => $course->fullname,
                'forum' => $forum->name,
                'discussion' => $discussion->name,
                'userid' => $discussion->userid,
                'post' => [
                    'subject' => $post->subject,
                    'message' => strip_tags($post->message),
                ],
                'prompt' => $replymessage,
            ];

            $airesponse = self::call_ai_service($payload);

            if ($requireapproval) {
                self::create_approval_request($discussion, $forum, $airesponse, 'pending');
            } else {
                self::create_approval_request($discussion, $forum, $airesponse, 'approved');
                self::create_auto_reply($discussion, $airesponse);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Creates an approval request and sends a notification.
     *
     * @param object $discussion The discussion object.
     * @param object $forum The forum object.
     * @param string $message The AI-generated message.
     * @param string $status The approval status ('pending' or 'approved').
     * @return void
     */
    private static function create_approval_request($discussion, $forum, string $message, string $status = 'pending'): void {
        global $DB;

        try {
            $approvaltoken = hash('sha256', $discussion->id . time() . random_string(20));

            $pending = new \stdClass();
            $pending->discussionid = $discussion->id;
            $pending->forumid = $forum->id;
            $pending->creator_userid = $discussion->userid;
            $pending->subject = "Re: " . $discussion->name;
            $pending->message = $message;
            $pending->status = $status;
            $pending->approval_token = $approvaltoken;
            $pending->timecreated = time();

            $pendingid = $DB->insert_record('local_forum_ai_pending', $pending);

            if ($status === 'pending') {
                self::send_moodle_notification($discussion, $forum, $pendingid, $approvaltoken);
            }
        } catch (\Exception $e) {
            debugging('Error in create_approval_request: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Sends a notification using Moodle's messaging system.
     *
     * @param object $discussion The discussion object.
     * @param object $forum The forum object.
     * @param int $pendingid The pending approval ID.
     * @param string $approvaltoken The unique approval token.
     * @return bool True on success, false on error.
     */
    private static function send_moodle_notification($discussion, $forum, int $pendingid, string $approvaltoken): bool {
        global $DB, $PAGE;

        try {
            $creator = $DB->get_record('user', ['id' => $discussion->userid]);
            $course = $DB->get_record('course', ['id' => $forum->course]);
            $pending = $DB->get_record('local_forum_ai_pending', ['id' => $pendingid]);

            if (!$creator || !$forum || !$course || !$pending) {
                return false;
            }

            $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
            $context = \context_module::instance($cm->id);
            $recipients = get_users_by_capability($context, 'mod/forum:replypost');

            $allowedroles = ['manager', 'editingteacher', 'coursecreator'];
            $finalrecipients = [];

            foreach ($recipients as $recipient) {
                $roles = get_user_roles($context, $recipient->id);
                foreach ($roles as $role) {
                    if (in_array($role->shortname, $allowedroles)) {
                        $finalrecipients[$recipient->id] = $recipient;
                    }
                }
            }

            if (empty($finalrecipients)) {
                return false;
            }

            $reviewurl = new \moodle_url('/local/forum_ai/review.php', ['token' => $approvaltoken]);
            $approveurl = new \moodle_url('/local/forum_ai/approve.php', ['token' => $approvaltoken, 'action' => 'approve']);
            $rejecturl = new \moodle_url('/local/forum_ai/approve.php', ['token' => $approvaltoken, 'action' => 'reject']);

            foreach ($finalrecipients as $recipient) {
                $message = new \core\message\message();
                $message->component = 'local_forum_ai';
                $message->name = 'ai_approval_request';
                $message->userfrom = \core_user::get_noreply_user();
                $message->userto = $recipient;
                $message->subject = get_string('notification_subject', 'local_forum_ai');

                $templatedata = [
                    'str_greeting' => get_string('notification_greeting', 'local_forum_ai', ['firstname' => $recipient->firstname]),
                    'discussionname' => $discussion->name,
                    'forumname' => $forum->name,
                    'preview' => format_string(substr(strip_tags($pending->message), 0, 150)),
                    'reviewurl' => $reviewurl->out(false),
                    'coursefullname' => $course->fullname,
                    'str_subject' => get_string('notification_subject', 'local_forum_ai'),
                    'str_preview_label' => get_string('notification_preview', 'local_forum_ai'),
                    'str_review_button' => get_string('notification_review_button', 'local_forum_ai'),
                    'str_course_label' => get_string('notification_course_label', 'local_forum_ai'),
                ];

                $message->fullmessage = self::get_plain_text_message(
                    $recipient->firstname,
                    $discussion->name,
                    $forum->name,
                    $course->fullname,
                    $templatedata['preview'],
                    $reviewurl->out(false),
                    $approveurl->out(false),
                    $rejecturl->out(false)
                );

                $message->fullmessageformat = FORMAT_PLAIN;

                try {
                    $renderer = $PAGE->get_renderer('local_forum_ai');
                    $message->fullmessagehtml = $renderer->render_from_template('local_forum_ai/notification', $templatedata);
                } catch (\Exception $templateerror) {
                    $message->fullmessagehtml = $message->fullmessage;
                }

                $message->smallmessage = get_string(
                    'notification_smallmessage',
                    'local_forum_ai',
                    ['discussion' => $discussion->name],
                );
                $message->contexturl = $reviewurl;
                $message->contexturlname = get_string('notification_review_button', 'local_forum_ai');

                message_send($message);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generates the plain text message for the notification.
     *
     * @param string $firstname The recipient's first name.
     * @param string $discussionname The discussion name.
     * @param string $forumname The forum name.
     * @param string $coursefullname The course full name.
     * @param string $preview The AI message preview.
     * @param string $reviewurl The review URL.
     * @param string $approveurl The approval URL.
     * @param string $rejecturl The rejection URL.
     * @return string The formatted plain text message.
     */
    private static function get_plain_text_message(
        string $firstname,
        string $discussionname,
        string $forumname,
        string $coursefullname,
        string $preview,
        string $reviewurl,
        string $approveurl,
        string $rejecturl
    ): string {

        $message = get_string('notification_greeting', 'local_forum_ai', ['firstname' => $firstname]) . "\n\n"
            . get_string('notification_intro', 'local_forum_ai', [
                'discussion' => $discussionname,
                'forum' => $forumname,
                'course' => $coursefullname,
            ]) . "\n\n"
            . get_string('notification_preview', 'local_forum_ai') . " " . $preview . "...\n\n"
            . get_string('notification_review_link', 'local_forum_ai', ['url' => $reviewurl]) . "\n\n"
            . get_string('notification_approve_link', 'local_forum_ai', ['url' => $approveurl]) . "\n"
            . get_string('notification_reject_link', 'local_forum_ai', ['url' => $rejecturl]);

        return $message;
    }

    /**
     * Creates an automatic AI reply in the forum discussion.
     *
     * @param object $discussion The discussion object.
     * @param string $message The AI-generated message content.
     * @return bool True on success, false on failure.
     */
    public static function create_auto_reply($discussion, string $message): bool {
        global $DB;

        try {
            $course = $DB->get_record('course', ['id' => $discussion->course], '*', MUST_EXIST);
            $teachers = get_editingteachers($course->id);

            if (empty($teachers)) {
                return false;
            }

            $teacher = reset($teachers);

            $post = new \stdClass();
            $post->discussion = $discussion->id;
            $post->parent = $discussion->firstpost;
            $post->userid = $teacher->id;
            $post->created = time();
            $post->modified = time();
            $post->subject = "Re: " . $discussion->name;
            $post->message = $message;
            $post->messageformat = FORMAT_HTML;

            $DB->insert_record('forum_posts', $post);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
