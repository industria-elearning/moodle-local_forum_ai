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
