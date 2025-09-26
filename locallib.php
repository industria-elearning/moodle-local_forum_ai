<?php

/**
 * Limpia registros inválidos en la tabla de pendientes.
 */
function local_forum_ai_cleanup_pending()
{
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
 * @param int $courseid ID del curso
 * @return array lista de objetos con datos de pendientes
 */
function local_forum_ai_get_pending(int $courseid)
{
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
           AND cm.deletioninprogress = 0   -- solo foros activos
           AND cm.visible = 1              -- opcional: no mostrar foros ocultos
      ORDER BY p.timecreated DESC";

    return $DB->get_records_sql($sql, ['status' => 'pending', 'courseid' => $courseid]);
}


/**
 * Obtiene la lista de historial de respuestas.
 *
 * @param int $courseid ID del curso
 * @return array lista de objetos de respuestas
 */
function local_forum_ai_get_history(int $courseid)
{
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
           AND cm.deletioninprogress = 0   -- solo foros activos
           AND cm.visible = 1              -- opcional: no mostrar foros ocultos
      ORDER BY p.timecreated DESC";

    return $DB->get_records_sql($sql);
}
