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

$functions = [
    'local_forum_ai_get_details' => [
        'classname'   => 'local_forum_ai\\external\\get_details',
        'methodname'  => 'execute',
        'classpath'   => '',
        'description' => 'Obtiene detalles del debate y respuesta AI',
        'type'        => 'read',
        'ajax'        => true,
    ],

    'local_forum_ai_approve_response' => [
        'classname'   => 'local_forum_ai\\external\\approve_response',
        'methodname'  => 'execute',
        'classpath'   => '',
        'description' => 'Aprueba o rechaza respuesta AI',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'local_forum_ai_update_response' => [
        'classname'   => 'local_forum_ai\\external\\update_response',
        'methodname'  => 'execute',
        'classpath'   => '',
        'description' => 'Actualiza el mensaje generado por la IA en un pending',
        'type'        => 'write',
        'ajax'        => true,
    ],
];
