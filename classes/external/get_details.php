<?php
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

class get_details extends external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'token' => new external_value(PARAM_ALPHANUMEXT, 'Token de aprobación')
        ]);
    }

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
            'posts' => self::build_hierarchical_posts($posts),
            'airesponse' => format_text($pending->message, FORMAT_HTML),
            'token' => $pending->approval_token,
            'status' => $pending->status,
        ];

        return $data;
    }

    /**
     * Construye la estructura jerárquica de posts en orden correcto
     */
    private static function build_hierarchical_posts($posts) {
        global $DB;

        $posts_by_id = [];
        $hierarchical = [];

        // Formatear todos los posts y indexarlos por ID
        foreach ($posts as $post) {
            $user = $DB->get_record('user', ['id' => $post->userid], 'id,firstname,lastname');
            $formatted_post = [
                'id' => $post->id,
                'parent' => $post->parent,
                'subject' => format_string($post->subject),
                'message' => format_text($post->message, $post->messageformat),
                'author' => fullname($user),
                'created' => userdate($post->created),
                'created_timestamp' => $post->created,
                'children' => [],
                'level' => 0
            ];
            $posts_by_id[$post->id] = $formatted_post;
        }

        // Construir la estructura jerárquica
        foreach ($posts_by_id as &$post) {
            if ($post['parent'] == 0) {
                // Es un post principal
                $hierarchical[] = &$post;
            } else {
                // Es una respuesta, agregarlo a su padre
                if (isset($posts_by_id[$post['parent']])) {
                    $posts_by_id[$post['parent']]['children'][] = &$post;
                }
            }
        }

        // Ordenar hijos por fecha de creación
        self::sort_children_recursive($hierarchical);

        // Convertir a estructura plana manteniendo el orden jerárquico
        return self::flatten_hierarchical($hierarchical, 0);
    }

    /**
     * Ordena recursivamente los hijos por fecha de creación
     */
    private static function sort_children_recursive(&$posts) {
        foreach ($posts as &$post) {
            if (!empty($post['children'])) {
                // Ordenar hijos por timestamp de creación
                usort($post['children'], function($a, $b) {
                    return $a['created_timestamp'] - $b['created_timestamp'];
                });

                // Recursivamente ordenar los hijos de los hijos
                self::sort_children_recursive($post['children']);
            }
        }
    }

    /**
     * Convierte la estructura jerárquica a plana manteniendo el orden
     */
    private static function flatten_hierarchical($posts, $level) {
        $result = [];

        foreach ($posts as $post) {
            // Establecer el nivel
            $post['level'] = $level;

            // Guardar los hijos antes de eliminarlos
            $children = $post['children'];
            unset($post['children']);

            // Agregar el post actual
            $result[] = $post;

            // Agregar los hijos recursivamente
            if (!empty($children)) {
                $child_posts = self::flatten_hierarchical($children, $level + 1);
                $result = array_merge($result, $child_posts);
            }
        }

        return $result;
    }



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
