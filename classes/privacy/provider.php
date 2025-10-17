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
 * Privacy subsystem implementation for local_forum_ai.
 *
 * @package    local_forum_ai
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai\privacy;

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\core_userlist_provider;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy subsystem implementation for local_forum_ai.
 */
class provider implements
    local_forum_ai_userlist,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\metadata\provider {
    /**
     * Describe the types of personal data stored by this plugin.
     *
     * @param collection $collection The initialized collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_database_table(
            'local_forum_ai',
            [
                'creator_userid' => 'privacy:metadata:local_forum_ai:creator_userid',
                'discussionid'   => 'privacy:metadata:local_forum_ai:discussionid',
                'forumid'        => 'privacy:metadata:local_forum_ai:forumid',
                'message'        => 'privacy:metadata:local_forum_ai:message',
                'subject'        => 'privacy:metadata:local_forum_ai:subject',
                'status'         => 'privacy:metadata:local_forum_ai:status',
                'approved_at'    => 'privacy:metadata:local_forum_ai:approved_at',
                'approval_token' => 'privacy:metadata:local_forum_ai:approval_token',
                'timecreated'    => 'privacy:metadata:local_forum_ai:timecreated',
                'timemodified'   => 'privacy:metadata:local_forum_ai:timemodified',
            ],
            'privacy:metadata:local_forum_ai'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        if (self::user_has_forumai_data($userid)) {
            $contextlist->add_user_context($userid);
        }
        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if (!$context instanceof \context_user) {
            return;
        }

        if (self::user_has_forumai_data($context->instanceid)) {
            $userlist->add_user($context->instanceid);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $user = $contextlist->get_user();
        $context = \context_user::instance($user->id);

        $records = $DB->get_records('local_forum_ai', ['creator_userid' => $user->id]);

        foreach ($records as $record) {
            writer::with_context($context)->export_data(
                [get_string('privacy:metadata:local_forum_ai', 'local_forum_ai')],
                $record
            );
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        if ($context->contextlevel == CONTEXT_USER) {
            self::delete_user_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_USER) {
                self::delete_user_data($context->instanceid);
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();
        if ($context instanceof \context_user) {
            self::delete_user_data($context->instanceid);
        }
    }

    /**
     * Return true if the specified userid has data in local_forum_ai table.
     *
     * @param int $userid The user to check for.
     * @return boolean
     */
    private static function user_has_forumai_data(int $userid): bool {
        global $DB;
        return $DB->record_exists('local_forum_ai', ['creator_userid' => $userid]);
    }

    /**
     * This deletes all user data given a userid.
     *
     * @param int $userid The user ID
     */
    private static function delete_user_data(int $userid) {
        global $DB;
        $DB->delete_records('local_forum_ai', ['creator_userid' => $userid]);
    }
}
