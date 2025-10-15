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
 * Servicio externo para obtener detalles de una discusión con respuesta AI.
 *
 * Define la función webservice `local_forum_ai_get_details`
 * que devuelve curso, foro, discusión, posts y estado de la respuesta AI.
 *
 * @package    local_forum_ai
 * @category   external
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use context_module;
use moodle_exception;

/**
 * External API class to get details of AI responses in forum discussions.
 */
class get_details extends external_api {

    /**
     * Define los parámetros de entrada de la función webservice.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'token' => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación'),
        ]);
    }

    /**
     * Ejecuta la obtención de detalles de discusión y respuesta AI.
     *
     * @param string $token Token de aprobación
     * @return array Datos del curso, foro, discusión y posts
     * @throws moodle_exception Si no se encuentra el registro o falta permiso
     */
    public static function execute($token) {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), ['token' => $token]);

        $pending = $DB->get_record('local_forum_ai_pending', ['approval_token' => $params['token']], '*', MUST_EXIST);
        $discussion = $DB->get_record('forum_discussions', ['id' => $pending->discussionid], '*', MUST_EXIST);
        $forum = $DB->get_record('forum', ['id' => $pending->forumid], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/forum:viewdiscussion', $context);

        $posts = $DB->get_records('forum_posts', ['discussion' => $discussion->id], 'created ASC');

        $data = [
            'course' => format_string($course->fullname),
            'forum' => format_string($forum->name),
            'discussion' => format_string($discussion->name),
            'posts' => self::buildhierarchicalposts($posts),
            'airesponse' => format_text($pending->message, FORMAT_HTML),
            'token' => $pending->approval_token,
            'status' => $pending->status,
        ];

        return $data;
    }

    /**
     * Construye la estructura jerárquica de posts en orden correcto.
     *
     * @param array $posts Lista de posts
     * @return array Posts en estructura plana jerárquica
     */
    private static function buildhierarchicalposts($posts) {
        global $DB;

        $postsbyid = [];
        $hierarchical = [];

        // Formatear todos los posts y indexarlos por ID.
        foreach ($posts as $post) {
            $user = $DB->get_record('user', ['id' => $post->userid], 'id,firstname,lastname');
            $formattedpost = [
                'id' => $post->id,
                'parent' => $post->parent,
                'subject' => format_string($post->subject),
                'message' => format_text($post->message, $post->messageformat),
                'author' => fullname($user),
                'created' => userdate($post->created),
                'created_timestamp' => $post->created,
                'children' => [],
                'level' => 0,
            ];
            $postsbyid[$post->id] = $formattedpost;
        }

        // Construir la estructura jerárquica.
        foreach ($postsbyid as &$post) {
            if ($post['parent'] == 0) {
                // Es un post principal.
                $hierarchical[] = &$post;
            } else {
                // Es una respuesta, agregarlo a su padre.
                if (isset($postsbyid[$post['parent']])) {
                    $postsbyid[$post['parent']]['children'][] = &$post;
                }
            }
        }

        // Ordenar hijos por fecha de creación.
        self::sortchildrenrecursive($hierarchical);

        // Convertir a estructura plana manteniendo el orden jerárquico.
        return self::flattenhierarchical($hierarchical, 0);
    }

    /**
     * Ordena recursivamente los hijos por fecha de creación.
     *
     * @param array $posts
     */
    private static function sortchildrenrecursive(&$posts) {
        foreach ($posts as &$post) {
            if (!empty($post['children'])) {
                // Ordenar hijos por timestamp de creación.
                usort($post['children'], function($a, $b) {
                    return $a['created_timestamp'] - $b['created_timestamp'];
                });

                // Recursivamente ordenar los hijos de los hijos.
                self::sortchildrenrecursive($post['children']);
            }
        }
    }

    /**
     * Convierte la estructura jerárquica a plana manteniendo el orden.
     *
     * @param array $posts
     * @param int $level Nivel actual
     * @return array Posts aplanados con niveles
     */
    private static function flattenhierarchical($posts, $level) {
        $result = [];

        foreach ($posts as $post) {
            // Establecer el nivel.
            $post['level'] = $level;

            // Guardar los hijos antes de eliminarlos.
            $children = $post['children'];
            unset($post['children']);

            // Agregar el post actual.
            $result[] = $post;

            // Agregar los hijos recursivamente.
            if (!empty($children)) {
                $childposts = self::flattenhierarchical($children, $level + 1);
                $result = array_merge($result, $childposts);
            }
        }

        return $result;
    }

    /**
     * Define la estructura de retorno de la función webservice.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'course' => new external_value(PARAM_TEXT, 'Nombre del curso'),
            'forum' => new external_value(PARAM_TEXT, 'Nombre del foro'),
            'discussion' => new external_value(PARAM_TEXT, 'Título del debate'),
            'posts' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'ID del post'),
                    'parent' => new external_value(PARAM_INT, 'ID del post padre'),
                    'subject' => new external_value(PARAM_TEXT, 'Asunto'),
                    'message' => new external_value(PARAM_RAW, 'Mensaje'),
                    'author' => new external_value(PARAM_TEXT, 'Autor'),
                    'created' => new external_value(PARAM_TEXT, 'Fecha de creación'),
                    'level' => new external_value(PARAM_INT, 'Nivel de anidación'),
                ])
            ),
            'airesponse' => new external_value(PARAM_RAW, 'Respuesta AI propuesta'),
            'token' => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación'),
            'status' => new external_value(PARAM_ALPHA, 'Estado del mensaje (pending, approved, rejected)'),
        ]);
    }
}
