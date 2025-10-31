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
 * Backup handler for the local_forum_ai plugin.
 *
 * Define the structure and data to include when backing up the plugin
 * configuration and pending AI-generated responses associated with forums.
 *
 * This class integrates with Moodle's backup API and ensures that all
 * relevant AI configuration data and pending responses are preserved
 * during course or activity backups.
 *
 * @package    local_forum_ai
 * @category   backup
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_local_forum_ai_plugin extends backup_local_plugin {
    /**
     * Define the structure of the forum-level data included in the backup.
     *
     * This method specifies which database tables and fields are exported
     * for the local_forum_ai plugin as part of the forum backup process.
     *
     * @return backup_subplugin_element The constructed backup element
     */
    protected function define_forum_subplugin_structure() {
        $plugin = $this->get_subplugin_element(); // Reference to <plugin> XML node.

        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // AI config table data.
        $config = new backup_nested_element('forum_ai_config', ['id'], [
            'forumid', 'enabled', 'reply_message', 'require_approval',
            'timecreated', 'timemodified',
        ]);

        // Pending responses.
        $pending = new backup_nested_element('forum_ai_pending', ['id'], [
            'discussionid', 'forumid', 'creator_userid', 'subject', 'message',
            'status', 'approval_token', 'approved_at', 'timecreated', 'timemodified',
        ]);

        // Define hierarchy.
        $plugin->add_child($pluginwrapper);
        $pluginwrapper->add_child($config);
        $pluginwrapper->add_child($pending);

        // Define data sources.
        $config->set_source_table('local_forum_ai_config', ['forumid' => backup::VAR_ACTIVITYID]);
        $pending->set_source_table('local_forum_ai_pending', ['forumid' => backup::VAR_ACTIVITYID]);

        return $plugin;
    }

    /**
     * Define the structure of course-level data for the plugin (if any).
     *
     * This plugin does not include any course-level data in backups,
     * so this method is intentionally left empty.
     *
     * @return void
     */
    protected function define_course_subplugin_structure() {
        // No course-level data needed.
    }
}
