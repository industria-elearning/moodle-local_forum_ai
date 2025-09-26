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

/**
 * Extiende la navegación de configuración.
 * Este es el hook correcto para agregar opciones al menú "Más" de actividades.
 *
 * @param settings_navigation $nav El objeto de navegación de configuración
 * @param context $context El contexto actual
 */
function local_forum_ai_extend_settings_navigation(settings_navigation $nav, context $context) {
    global $PAGE, $DB;

    // Verificar que estemos en un contexto de módulo (actividad).
    if ($context->contextlevel != CONTEXT_MODULE) {
        return;
    }

    // Verificar que sea un foro.
    if ($PAGE->cm->modname !== 'forum') {
        return;
    }

    // Verificar permisos.
    if (!has_capability('mod/forum:addquestion', $context) &&
        !has_capability('moodle/site:config', $context)) {
        return;
    }

    // Buscar el nodo de configuraciones del módulo.
    $modulesettings = $nav->find('modulesettings', navigation_node::TYPE_SETTING);

    if ($modulesettings) {
        $url = new moodle_url('/local/forum_ai/config.php', ['forumid' => $PAGE->cm->instance]);

        $modulesettings->add(
            get_string('pluginname', 'local_forum_ai'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'forum_ai_config',
            new pix_icon('i/settings', '')
        );
    }
}

/**
 * Extiende el formulario de configuración de un foro.
 *
 * @param mod_forum_mod_form $formwrapper
 * @param MoodleQuickForm $mform
 */
function local_forum_ai_coursemodule_standard_elements($formwrapper, $mform) {
    global $DB;

    // Solo aplicamos a foros.
    if ($formwrapper->get_current()->modulename !== 'forum') {
        return;
    }

    $cm = $formwrapper->get_current();
    $forumid = $cm->instance ?? null;

    // Valores por defecto.
    $defaults = (object)[
        'enabled' => 0,
        'require_approval' => 1,
        'reply_message' => get_string('default_reply_message', 'local_forum_ai'),
    ];

    // Si ya hay configuración guardada en nuestra tabla, la usamos.
    if ($forumid && $DB->record_exists('local_forum_ai_config', ['forumid' => $forumid])) {
        $record = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);
        $defaults->enabled = $record->enabled;
        $defaults->require_approval = $record->require_approval;
        $defaults->reply_message = $record->reply_message;
    }

    // Nueva sección.
    $mform->addElement('header', 'local_forum_ai_header', get_string('datacurso_custom', 'local_forum_ai'));

    // Habilitar AI.
    $mform->addElement('select', 'local_forum_ai_enabled',
        get_string('enabled', 'local_forum_ai'), [
            0 => get_string('no'),
            1 => get_string('yes'),
        ]
    );
    $mform->setDefault('local_forum_ai_enabled', $defaults->enabled);

    // Requiere aprobación.
    $mform->addElement('select', 'local_forum_ai_require_approval',
        get_string('require_approval', 'local_forum_ai'), [
            1 => get_string('yes'),
            0 => get_string('no'),
        ]
    );
    $mform->setDefault('local_forum_ai_require_approval', $defaults->require_approval);

    // Mensaje base.
    $mform->addElement('textarea', 'local_forum_ai_reply_message',
        get_string('reply_message', 'local_forum_ai'), 'wrap="virtual" rows="3" cols="50"');
    $mform->setType('local_forum_ai_reply_message', PARAM_TEXT);
    $mform->setDefault('local_forum_ai_reply_message', $defaults->reply_message);
}

/**
 * Se ejecuta al guardar/actualizar un módulo (foro).
 *
 * @param stdClass $data
 * @param stdClass $course
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

/**
 * Extiende la navegación de un curso para agregar enlace en Informes.
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 */
function local_forum_ai_extend_navigation_course($navigation, $course, $context) {
    global $PAGE;

    // URL de reportes del plugin.
    $pendingurl = new moodle_url('/local/forum_ai/pending.php', ['courseid' => $course->id]);
    $historyurl = new moodle_url('/local/forum_ai/history.php', ['courseid' => $course->id]);

    // Buscar nodo "Informes".
    $reportsnode = $navigation->find('coursereports', navigation_node::TYPE_CONTAINER);
    if (!$reportsnode) {
        $reportsnode = $navigation->find('courseadminreports', navigation_node::TYPE_CONTAINER);
    }

    if ($reportsnode) {
        // Agregar enlaces dentro de Informes.
        $reportsnode->add(
            get_string('pendingresponses', 'local_forum_ai'),
            $pendingurl,
            navigation_node::TYPE_SETTING,
            null,
            'local_forum_ai_pending',
            new pix_icon('i/report', '')
        );

        $reportsnode->add(
            get_string('historyresponses', 'local_forum_ai'),
            $historyurl,
            navigation_node::TYPE_SETTING,
            null,
            'local_forum_ai_history',
            new pix_icon('i/report', '')
        );
    } else {
        // Si no existe "Informes", los agregamos en la raíz.
        $navigation->add(
            get_string('pendingresponses', 'local_forum_ai'),
            $pendingurl,
            navigation_node::TYPE_SETTING,
            null,
            'local_forum_ai_pending',
            new pix_icon('i/report', '')
        );

        $navigation->add(
            get_string('historyresponses', 'local_forum_ai'),
            $historyurl,
            navigation_node::TYPE_SETTING,
            null,
            'local_forum_ai_history',
            new pix_icon('i/report', '')
        );
    }
}
