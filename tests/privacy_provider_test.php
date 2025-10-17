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
 * Privacy test for local_forum_ai
 *
 * @package   local_forum_ai
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai\tests;

use context_system;
use context_user;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use local_forum_ai\privacy\provider;
use stdClass;

/**
 * Privacy provider tests for the local_forum_ai plugin.
 *
 * @group local_forum_ai
 * @group local_forum_ai_privacy
 */
final class privacy_provider_test extends provider_testcase {
    /**
     * Set up the test environment.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Ensure that no context is returned if there is no user data.
     *
     * @covers \local_forum_ai\privacy\provider::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));

        // Create user data.
        self::create_userdata($user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, $contextlist);

        $usercontext = context_user::instance($user->id);
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Test that get_users_in_context returns the correct users.
     *
     * @covers \local_forum_ai\privacy\provider::get_users_in_context
     */
    public function test_get_users_in_context(): void {
        $component = 'local_forum_ai';
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);

        $userlist = new userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        // Create user data.
        self::create_userdata($user->id);

        // Should now return the user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertEquals([$user->id], $userlist->get_userids());

        // Check that system context does not return any user.
        $systemcontext = context_system::instance();
        $userlist = new userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that data is exported correctly.
     *
     * @covers \local_forum_ai\privacy\provider::export_user_data
     */
    public function test_export_user_data(): void {
        $user = $this->getDataGenerator()->create_user();
        $record = self::create_userdata($user->id);

        $usercontext = context_user::instance($user->id);
        $writer = writer::with_context($usercontext);

        $this->assertFalse($writer->has_any_data());
        $approvedlist = new approved_contextlist($user, 'local_forum_ai', [$usercontext->id]);
        provider::export_user_data($approvedlist);

        $data = $writer->get_data([get_string('privacy:metadata:local_forum_ai_pending', 'local_forum_ai')]);
        foreach ($record as $field => $value) {
            if (isset($data->$field)) {
                $this->assertEquals((string) $value, (string) $data->$field);
            }
        }
    }

    /**
     * Test that data is deleted for a given context.
     *
     * @covers \local_forum_ai\privacy\provider::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        self::create_userdata($user1->id);
        self::create_userdata($user2->id);

        $this->assertEquals(2, $DB->count_records('local_forum_ai_pending'));

        $context1 = context_user::instance($user1->id);
        provider::delete_data_for_all_users_in_context($context1);

        $this->assertEquals(0, $DB->count_records('local_forum_ai_pending', ['creator_userid' => $user1->id]));
        $this->assertEquals(1, $DB->count_records('local_forum_ai_pending', ['creator_userid' => $user2->id]));
    }

    /**
     * Test that user-specific data is deleted properly.
     *
     * @covers \local_forum_ai\privacy\provider::delete_data_for_user
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        self::create_userdata($user1->id);
        self::create_userdata($user2->id);

        $context1 = context_user::instance($user1->id);
        $approvedlist = new approved_contextlist($user1, 'local_forum_ai', [$context1->id]);
        provider::delete_data_for_user($approvedlist);

        $this->assertEquals(0, $DB->count_records('local_forum_ai_pending', ['creator_userid' => $user1->id]));
        $this->assertEquals(1, $DB->count_records('local_forum_ai_pending', ['creator_userid' => $user2->id]));
    }

    /**
     * Create dummy user data for tests.
     *
     * @param int $userid
     * @return stdClass
     */
    private static function create_userdata(int $userid): stdClass {
        global $DB;

        $record = new stdClass();
        $record->creator_userid = $userid;
        $record->discussionid = rand(1, 10);
        $record->forumid = rand(1, 5);
        $record->message = 'Mensaje generado por la IA';
        $record->subject = 'Tema de prueba';
        $record->status = 'approved';
        $record->approved_at = time();
        $record->approval_token = md5(uniqid((string)$userid, true));
        $record->timecreated = time();
        $record->timemodified = time();
        $record->id = $DB->insert_record('local_forum_ai_pending', $record);

        return $record;
    }
}
