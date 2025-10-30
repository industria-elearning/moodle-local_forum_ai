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
 * Forum AI plugin configuration.
 *
 * @package    local_forum_ai
 * @category   admin
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extends the settings navigation (the "More" menu in forum activities).
 *
 * @param settings_navigation $nav
 * @param context $context
 */
function local_forum_ai_extend_settings_navigation(settings_navigation $nav, context $context) {
    global $PAGE, $DB, $USER;

    // Only apply in forum module contexts.
    if ($context->contextlevel != CONTEXT_MODULE || $PAGE->cm->modname !== 'forum') {
        return;
    }

    // Require capability to see AI management options.
    if (!has_capability('local/forum_ai:approveresponses', $context, $USER)) {
        return;
    }

    $forumid = $PAGE->cm->instance;
    $courseid = $PAGE->course->id;

    $urlpending = new moodle_url('/local/forum_ai/pending.php', [
        'courseid' => $courseid,
        'forumid'  => $forumid,
    ]);
    $urlhistory = new moodle_url('/local/forum_ai/history.php', [
        'courseid' => $courseid,
        'forumid'  => $forumid,
    ]);
    $urlconfig = new moodle_url('/local/forum_ai/config.php', ['forumid' => $forumid]);

    $modulesettings = $nav->find('modulesettings', navigation_node::TYPE_SETTING);

    if ($modulesettings) {
        $modulesettings->add(
            get_string('pluginname', 'local_forum_ai'),
            $urlconfig,
            navigation_node::TYPE_SETTING,
            null,
            'forum_ai_config',
            new pix_icon('i/settings', '')
        );
        $modulesettings->add(
            get_string('pendingresponses', 'local_forum_ai'),
            $urlpending,
            navigation_node::TYPE_SETTING,
            null,
            'forum_ai_pending',
            new pix_icon('i/warning', '')
        );
        $modulesettings->add(
            get_string('historyresponses', 'local_forum_ai'),
            $urlhistory,
            navigation_node::TYPE_SETTING,
            null,
            'forum_ai_history',
            new pix_icon('i/log', '')
        );
    }
}

/**
 * Extends the course navigation tree (left-hand side menu).
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param stdClass $context
 */
function local_forum_ai_extend_navigation_course($navigation, $course, $context) {
    global $USER;

    // Only display if the user has the approveresponses capability.
    if (has_capability('local/forum_ai:approveresponses', $context, $USER)) {
        $pendingurl = new moodle_url('/local/forum_ai/pending.php', ['courseid' => $course->id]);
        $historyurl = new moodle_url('/local/forum_ai/history.php', ['courseid' => $course->id]);

        $navigation->add(
            get_string('pendingresponses', 'local_forum_ai'),
            $pendingurl,
            navigation_node::TYPE_SETTING,
            null,
            'forum_ai_pending',
            new pix_icon('i/warning', '')
        );

        $navigation->add(
            get_string('historyresponses', 'local_forum_ai'),
            $historyurl,
            navigation_node::TYPE_SETTING,
            null,
            'forum_ai_history',
            new pix_icon('i/log', '')
        );
    }
}

/**
 * Extends the forum module edit form with AI-related fields.
 *
 * @param mod_forum_mod_form $formwrapper
 * @param MoodleQuickForm $mform
 */
function local_forum_ai_coursemodule_standard_elements($formwrapper, $mform) {
    global $DB, $USER;

    // Only apply to forum modules.
    if ($formwrapper->get_current()->modulename !== 'forum') {
        return;
    }

    $cm = $formwrapper->get_current();
    $forumid = $cm->instance ?? null;
    $context = context_course::instance($cm->course);

    // Show configuration fields only if the user has permission.
    if (!has_capability('local/forum_ai:approveresponses', $context, $USER)) {
        return;
    }

    // Default values.
    $defaults = (object)[
        'enabled' => 0,
        'require_approval' => 1,
        'reply_message' => get_string('default_reply_message', 'local_forum_ai'),
    ];

    // Load existing configuration if present.
    if ($forumid && $DB->record_exists('local_forum_ai_config', ['forumid' => $forumid])) {
        $record = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);
        $defaults->enabled = $record->enabled;
        $defaults->require_approval = $record->require_approval;
        $defaults->reply_message = $record->reply_message;
    }

    // Section header.
    $mform->addElement('header', 'local_forum_ai_header', get_string('datacurso_custom', 'local_forum_ai'));

    // Enable AI.
    $mform->addElement('select', 'local_forum_ai_enabled',
        get_string('enabled', 'local_forum_ai'),
        [0 => get_string('no'), 1 => get_string('yes')]
    );
    $mform->setDefault('local_forum_ai_enabled', $defaults->enabled);

    // Require approval.
    $mform->addElement('select', 'local_forum_ai_require_approval',
        get_string('require_approval', 'local_forum_ai'),
        [1 => get_string('yes'), 0 => get_string('no')]
    );
    $mform->setDefault('local_forum_ai_require_approval', $defaults->require_approval);

    // Default AI reply message.
    $mform->addElement('textarea', 'local_forum_ai_reply_message',
        get_string('reply_message', 'local_forum_ai'),
        'wrap="virtual" rows="3" cols="50"'
    );
    $mform->setType('local_forum_ai_reply_message', PARAM_TEXT);
    $mform->setDefault('local_forum_ai_reply_message', $defaults->reply_message);
}

/**
 * Saves or updates forum AI configuration when editing a forum.
 *
 * @param stdClass $data
 * @param stdClass $course
 * @return stdClass
 */
function local_forum_ai_coursemodule_edit_post_actions($data, $course) {
    global $DB;

    if ($data->modulename !== 'forum') {
        return $data;
    }

    $record = $DB->get_record('local_forum_ai_config', ['forumid' => $data->instance]);

    $config = new stdClass();
    $config->forumid = $data->instance;
    $config->enabled = $data->local_forum_ai_enabled ?? 0;
    $config->require_approval = $data->local_forum_ai_require_approval ?? 1;
    $config->reply_message = $data->local_forum_ai_reply_message ?? '';
    $config->timemodified = time();

    if ($record) {
        $config->id = $record->id;
        $DB->update_record('local_forum_ai_config', $config);
    } else {
        $config->timecreated = time();
        $DB->insert_record('local_forum_ai_config', $config);
    }

    return $data;
}
