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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$forumid = required_param('forumid', PARAM_INT);
$action = optional_param('action', 'view', PARAM_ALPHA);

try {
    // Verify forum exists.
    $forum = $DB->get_record('forum', ['id' => $forumid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
} catch (Exception $e) {
    throw new \moodle_exception('invalidforumid', 'forum');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/forum:addquestion', $context);

$PAGE->set_url('/local/forum_ai/config.php', ['forumid' => $forumid]);
$PAGE->set_title(get_string('pluginname', 'local_forum_ai'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->navbar->add($forum->name, new moodle_url('/mod/forum/view.php', ['id' => $cm->id]));
$PAGE->navbar->add(get_string('pluginname', 'local_forum_ai'));

// Check if the table exists.
$tableexists = $DB->get_manager()->table_exists('local_forum_ai_config');

if (!$tableexists) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(
        get_string('err_table_missing', 'local_forum_ai'),
        \core\output\notification::NOTIFY_ERROR
    );
    echo html_writer::link(
        new moodle_url('/admin/index.php'),
        get_string('goto_notifications', 'local_forum_ai'),
        ['class' => 'btn btn-primary']
    );
    echo $OUTPUT->footer();
    exit;
}

// Process form submission.
if ($action === 'save' && confirm_sesskey()) {
    $enabled = optional_param('enabled', 0, PARAM_INT);
    $replymessage = optional_param('reply_message', '', PARAM_TEXT);
    $requireapproval = optional_param('require_approval', 1, PARAM_INT);

    try {
        // Check if configuration already exists for this forum.
        $existing = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);

        $record = new stdClass();
        $record->forumid = $forumid;
        $record->enabled = $enabled;
        $record->reply_message = $replymessage;
        $record->require_approval = $requireapproval;
        $record->timemodified = time();

        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('local_forum_ai_config', $record);
            $message = get_string('config_updated', 'local_forum_ai');
        } else {
            $record->timecreated = time();
            $DB->insert_record('local_forum_ai_config', $record);
            $message = get_string('config_created', 'local_forum_ai');
        }

        redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
    } catch (Exception $e) {
        redirect(
            $PAGE->url,
            get_string('error_saving', 'local_forum_ai', $e->getMessage()),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

// Get current configuration with fallback defaults.
$config = (object)[
    'enabled' => 0,
    'reply_message' => get_string('default_reply_message', 'local_forum_ai'),
    'require_approval' => 1,
];

try {
    $existingconfig = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);
    if ($existingconfig) {
        $config = $existingconfig;
        if (!isset($config->require_approval)) {
            $config->require_approval = 1;
        }
    }
} catch (Exception $e) {
    debugging('Error retrieving configuration: ' . $e->getMessage(), DEBUG_DEVELOPER);
}

$templatedata = [
    'forumname' => format_string($forum->name),
    'actionurl' => $PAGE->url->out(false),
    'sesskey' => sesskey(),
    'enabled' => $config->enabled,
    'enabledoptions' => [
        [
            'value' => 0,
            'label' => get_string('no', 'local_forum_ai'),
            'selected' => $config->enabled == 0,
        ],
        [
            'value' => 1,
            'label' => get_string('yes', 'local_forum_ai'),
            'selected' => $config->enabled == 1,
        ],
    ],
    'requireapproval' => $config->require_approval,
    'requireapprovaloptions' => [
        [
            'value' => 1,
            'label' => get_string('yes', 'local_forum_ai'),
            'selected' => $config->require_approval == 1,
        ],
        [
            'value' => 0,
            'label' => get_string('no', 'local_forum_ai'),
            'selected' => $config->require_approval == 0,
        ],
    ],
    'replymessage' => $config->reply_message,
    'cancelurl' => (new moodle_url('/mod/forum/view.php', ['id' => $cm->id]))->out(false),
    'strings' => [
        'enabled' => get_string('enabled', 'local_forum_ai'),
        'requireapproval' => get_string('require_approval', 'local_forum_ai'),
        'replymessage' => get_string('reply_message', 'local_forum_ai'),
        'save' => get_string('save', 'local_forum_ai'),
        'cancel' => get_string('cancel', 'local_forum_ai'),
    ],
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('settings_forum', 'local_forum_ai', format_string($forum->name)));
echo $OUTPUT->render_from_template('local_forum_ai/config_form', $templatedata);
echo $OUTPUT->footer();
