<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     local_forum_ai
 * @category    admin
 * @copyright   2025 Piero Llanos <piero@datacurso.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_forum_ai', get_string('pluginname', 'local_forum_ai'));

    // Endpoint IA.
    $settings->add(new admin_setting_configtext(
        'local_forum_ai/endpoint',
        get_string('endpoint', 'local_forum_ai'),
        get_string('endpoint_desc', 'local_forum_ai'),
        'https://plugins-ai-dev.datacurso.com/forum/chat',
        PARAM_URL
    ));

    // Token IA (se guarda enmascarado).
    $settings->add(new admin_setting_configpasswordunmask(
        'local_forum_ai/token',
        get_string('apitoken', 'local_forum_ai'),
        get_string('apitoken_desc', 'local_forum_ai'),
        ''
    ));

    $ADMIN->add('localplugins', $settings);
}
