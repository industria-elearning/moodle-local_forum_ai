<?php
// File: /local/forum_ai/lib.php - VERSIÓN CORRECTA
defined('MOODLE_INTERNAL') || die();

/**
 * Extiende la navegación de configuración
 * Este es el hook correcto para agregar opciones al menú "Más" de actividades
 *
 * @param settings_navigation $nav El objeto de navegación de configuración
 * @param context $context El contexto actual
 */
function local_forum_ai_extend_settings_navigation(settings_navigation $nav, context $context) {
    global $PAGE, $DB;

    // Verificar que estemos en un contexto de módulo (actividad)
    if ($context->contextlevel != CONTEXT_MODULE) {
        return;
    }

    // Verificar que sea un foro
    if ($PAGE->cm->modname !== 'forum') {
        return;
    }

    // Verificar permisos
    if (!has_capability('mod/forum:addquestion', $context) &&
        !has_capability('moodle/site:config', $context)) {
        return;
    }

    // Buscar el nodo de configuraciones del módulo
    $modulesettings = $nav->find('modulesettings', navigation_node::TYPE_SETTING);

    if ($modulesettings) {
        $url = new moodle_url('/local/forum_ai/config.php', array('forumid' => $PAGE->cm->instance));

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
 * @param MoodleQuickForm $mform
 * @param mod_forum_mod_form $formwrapper
 */
function local_forum_ai_coursemodule_standard_elements($formwrapper, $mform) {
    // Solo aplicamos a foros.
    if ($formwrapper->get_current()->modulename !== 'forum') {
        return;
    }

    // Nueva sección (collapsible) "Datacurso Custom".
    $mform->addElement('header', 'local_forum_ai_header', get_string('datacurso_custom', 'local_forum_ai'));

    // Habilitar AI.
    $mform->addElement('select', 'local_forum_ai_enabled',
        get_string('enabled', 'local_forum_ai'), [0 => get_string('no'), 1 => get_string('yes')]);
    $mform->setDefault('local_forum_ai_enabled', 0);

    // Usuario Bot.
    $mform->addElement('text', 'local_forum_ai_bot_userid', get_string('bot_userid', 'local_forum_ai'));
    $mform->setType('local_forum_ai_bot_userid', PARAM_INT);

    // Mensaje base.
    $mform->addElement('textarea', 'local_forum_ai_reply_message',
        get_string('reply_message', 'local_forum_ai'), 'wrap="virtual" rows="3" cols="50"');
    $mform->setType('local_forum_ai_reply_message', PARAM_TEXT);
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
    $config->bot_userid = $data->local_forum_ai_bot_userid ?? null;
    $config->reply_message = $data->local_forum_ai_reply_message ?? '';
    $config->ai_model = $data->local_forum_ai_ai_model ?? 'gpt-3.5';
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

    // URL de nuestro reporte.
    // $url = new moodle_url('/local/forum_ai/pending.php', ['courseid' => $course->id]);
    $pendingurl = new moodle_url('/local/forum_ai/pending.php');
    $historyurl = new moodle_url('/local/forum_ai/history.php');

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
