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
 * Funciones auxiliares para el plugin Forum AI.
 *
 * @package    local_forum_ai
 * @copyright  2025 Piero Llanos
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Limpia registros inválidos en la tabla de pendientes.
 *
 * @package local_forum_ai
 * @return void
 */
function local_forum_ai_cleanup_pending() {
    global $DB;

    // Elimina registros con foros inexistentes.
    $DB->execute("DELETE FROM {local_forum_ai_pending}
                   WHERE forumid NOT IN (SELECT id FROM {forum})");

    // Elimina registros con discusiones inexistentes.
    $DB->execute("DELETE FROM {local_forum_ai_pending}
                   WHERE discussionid NOT IN (SELECT id FROM {forum_discussions})");
}

/**
 * Obtiene la lista de respuestas pendientes.
 *
 * @package local_forum_ai
 * @param int $courseid ID del curso.
 * @return array lista de objetos con datos de pendientes.
 */
function local_forum_ai_get_pending(int $courseid) {
    global $DB;

    $sql = "SELECT p.*, d.name AS discussionname, f.name AS forumname,
                   c.fullname AS coursename, u.firstname, u.lastname,
                   fp.subject AS discussionsubject, fp.message AS discussionmessage, fp.messageformat
              FROM {local_forum_ai_pending} p
              JOIN {forum_discussions} d ON d.id = p.discussionid
              JOIN {forum} f ON f.id = p.forumid
              JOIN {course} c ON c.id = f.course
              JOIN {course_modules} cm ON cm.instance = f.id AND cm.module = (
                    SELECT id FROM {modules} WHERE name = 'forum'
              )
              JOIN {user} u ON u.id = p.creator_userid
              JOIN {forum_posts} fp ON fp.id = d.firstpost
             WHERE p.status = :status
               AND cm.deletioninprogress = 0   -- Solo foros activos.
               AND cm.visible = 1              -- Opcional: no mostrar foros ocultos.
          ORDER BY p.timecreated DESC";

    return $DB->get_records_sql($sql, ['status' => 'pending', 'courseid' => $courseid]);
}

/**
 * Obtiene la lista de historial de respuestas.
 *
 * @package local_forum_ai
 * @param int $courseid ID del curso.
 * @return array lista de objetos de respuestas.
 */
function local_forum_ai_get_history(int $courseid) {
    global $DB;

    $sql = "SELECT p.*, d.name AS discussionname, f.name AS forumname, c.fullname AS coursename,
                   u.firstname, u.lastname
              FROM {local_forum_ai_pending} p
              JOIN {forum_discussions} d ON d.id = p.discussionid
              JOIN {forum} f ON f.id = p.forumid
              JOIN {course} c ON c.id = f.course
              JOIN {course_modules} cm ON cm.instance = f.id AND cm.module = (
                    SELECT id FROM {modules} WHERE name = 'forum'
              )
              JOIN {user} u ON u.id = p.creator_userid
             WHERE p.status IN ('approved', 'rejected')
               AND cm.deletioninprogress = 0   -- Solo foros activos.
               AND cm.visible = 1              -- Opcional: no mostrar foros ocultos.
          ORDER BY p.timecreated DESC";

    return $DB->get_records_sql($sql);
}

/**
 * Devuelve los profesores (usuarios con rol editingteacher) de un curso.
 *
 * @package local_forum_ai
 * @param int $courseid ID del curso.
 * @param bool $single Si se quiere devolver solo uno.
 * @return \stdClass|array|null
 */
function get_editingteachers(int $courseid, bool $single = false) {
    global $DB;

    $context = \context_course::instance($courseid);

    $sql = "SELECT u.*
              FROM {role_assignments} ra
              JOIN {user} u ON u.id = ra.userid
              JOIN {role} r ON r.id = ra.roleid
             WHERE ra.contextid = :contextid
               AND r.shortname = :rolename
             ORDER BY ra.id ASC";

    $params = [
        'contextid' => $context->id,
        'rolename' => 'editingteacher',
    ];

    if ($single) {
        return $DB->get_record_sql($sql . " LIMIT 1", $params);
    } else {
        return $DB->get_records_sql($sql, $params);
    }
}
