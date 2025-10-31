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
 * Restore handler for the local_forum_ai plugin.
 *
 * This class defines how the plugin data related to AI forum responses
 * is restored during course or activity restore operations.
 *
 * It handles the restoration of configuration records from the
 * `local_forum_ai_config` table and the pending AI-generated responses
 * stored in `local_forum_ai_pending`.
 *
 * @package    local_forum_ai
 * @category   backup
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_local_forum_ai_plugin extends restore_local_plugin {
    /**
     * Defines the structure of the data that will be restored for each forum instance.
     *
     * This method specifies the XML paths that correspond to the plugin's backup
     * data sections, allowing Moodle to know which elements to restore and
     * how to process them.
     *
     * @return array of restore_path_element The list of data paths to be processed
     */
    protected function define_forum_subplugin_structure() {
        $paths = [];

        // Add the paths for the plugin data in the backup XML structure.
        $paths[] = new restore_path_element('forum_ai_config', $this->get_pathfor('/forum_ai_config'));
        $paths[] = new restore_path_element('forum_ai_pending', $this->get_pathfor('/forum_ai_pending'));

        return $paths;
    }

    /**
     * Process and restore configuration data for the AI forum plugin.
     *
     * This method is triggered automatically when the restore process
     * encounters a `<forum_ai_config>` element in the backup file.
     * It re-inserts the configuration into the `local_forum_ai_config` table,
     * mapping the old forum ID to the new restored one.
     *
     * @param array $data The raw configuration data from the backup XML
     * @return void
     */
    public function process_forum_ai_config($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Map old forum ID to the new restored instance.
        $data->forumid = $this->get_new_parentid('forum');

        // Insert the restored configuration record.
        $DB->insert_record('local_forum_ai_config', $data);

        // Keep a mapping between old and new IDs for consistency.
        $this->set_mapping('forum_ai_config', $oldid, $data->id);
    }

    /**
     * Process and restore pending AI-generated responses for the forum.
     *
     * This method handles `<forum_ai_pending>` elements found in the backup.
     * It re-inserts pending responses into the `local_forum_ai_pending` table,
     * assigning a new approval token to prevent conflicts or duplication.
     *
     * @param array $data The raw pending response data from the backup XML
     * @return void
     */
    public function process_forum_ai_pending($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Map old forum ID to the new restored instance.
        $data->forumid = $this->get_new_parentid('forum');

        // Generate a new approval token to avoid duplication across backups.
        $data->approval_token = \core_text::randomid(16);

        // Insert the restored pending response.
        $DB->insert_record('local_forum_ai_pending', $data);

        // Keep a mapping between old and new record IDs.
        $this->set_mapping('forum_ai_pending', $oldid, $data->id);
    }
}
