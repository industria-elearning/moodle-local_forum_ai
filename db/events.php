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
 * Configuración del plugin Forum AI.
 *
 * @package    local_forum_ai
 * @category   admin
 * @copyright  2025 Piero Llanos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\mod_forum\event\discussion_created',
        'callback'    => '\local_forum_ai\observer::discussion_created',
        'includefile' => '/local/forum_ai/classes/observer.php',
        'internal'    => false,
        'priority'    => 9999,
    ],
    [
        'eventname'   => '\core\event\course_module_created',
        'callback'    => '\local_forum_ai\observer::course_module_created',
        'includefile' => '/local/forum_ai/classes/observer.php',
        'internal'    => false,
        'priority'    => 9999,
    ],
];
