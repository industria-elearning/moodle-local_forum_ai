<?php
namespace local_forum_ai\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use context_system;
use moodle_exception;

class approve_response extends external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'token' => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación'),
            'action' => new external_value(PARAM_ALPHA, 'Acción: approve|reject'),
        ]);
    }

    public static function execute($token, $action) {
        global $DB, $CFG;

        $params = self::validate_parameters(self::execute_parameters(), [
            'token' => $token,
            'action' => $action,
        ]);

        $pending = $DB->get_record('local_forum_ai_pending',
            ['approval_token' => $params['token'], 'status' => 'pending'], '*', MUST_EXIST);

        $context = context_system::instance();
        self::validate_context($context);

        require_capability('mod/forum:replypost', $context);

        if ($params['action'] === 'approve') {
            require_once($CFG->dirroot . '/mod/forum/lib.php');

            $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);

            $post = new \stdClass();
            $post->discussion = $discussion->id;
            $post->parent     = $discussion->firstpost;
            $post->userid     = $pending->bot_userid;
            $post->created    = time();
            $post->modified   = time();
            $post->subject    = $pending->subject;
            $post->message    = $pending->message;
            $post->messageformat = FORMAT_HTML;
            $post->messagetrust  = 1;

            forum_add_new_post($post, null);

            $pending->status = 'approved';
            $pending->approved_at = time();
            $DB->update_record('local_forum_ai_pending', $pending);

        } else if ($params['action'] === 'reject') {
            $pending->status = 'rejected';
            $DB->update_record('local_forum_ai_pending', $pending);
        } else {
            throw new moodle_exception('invalidaction', 'local_forum_ai');
        }

        return ['success' => true];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Si la acción fue exitosa'),
        ]);
    }
}
