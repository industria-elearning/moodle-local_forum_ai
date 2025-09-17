<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$forumid = required_param('forumid', PARAM_INT);
$action = optional_param('action', 'view', PARAM_ALPHA);

try {
    // Verificar foro existe
    $forum = $DB->get_record('forum', array('id' => $forumid), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
} catch (Exception $e) {
    print_error('invalidforumid', 'forum');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/forum:addquestion', $context);

$PAGE->set_url('/local/forum_ai/config.php', array('forumid' => $forumid));
$PAGE->set_title(get_string('pluginname', 'local_forum_ai'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->navbar->add($forum->name, new moodle_url('/mod/forum/view.php', array('id' => $cm->id)));
$PAGE->navbar->add(get_string('pluginname', 'local_forum_ai'));

// Verificar si la tabla existe
$table_exists = $DB->get_manager()->table_exists('local_forum_ai_config');

if (!$table_exists) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification('La tabla de configuración no existe. Por favor, actualiza el plugin desde Administración del sitio > Notificaciones.', 'error');
    echo '<p><a href="' . new moodle_url('/admin/index.php') . '" class="btn btn-primary">Ir a Notificaciones</a></p>';
    echo $OUTPUT->footer();
    exit;
}

// Procesar formulario
if ($action === 'save' && confirm_sesskey()) {
    $enabled = optional_param('enabled', 0, PARAM_INT);
    $bot_userid = optional_param('bot_userid', null, PARAM_INT);
    $reply_message = optional_param('reply_message', '', PARAM_TEXT);
    $ai_model = optional_param('ai_model', 'gpt-3.5', PARAM_TEXT);

    try {
        // Verificar si ya existe configuración para este foro
        $existing = $DB->get_record('local_forum_ai_config', array('forumid' => $forumid));

        $record = new stdClass();
        $record->forumid = $forumid;
        $record->enabled = $enabled;
        $record->bot_userid = empty($bot_userid) ? null : $bot_userid;
        $record->reply_message = $reply_message;
        $record->ai_model = $ai_model;
        $record->timemodified = time();

        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('local_forum_ai_config', $record);
            $message = 'Configuración actualizada correctamente';
        } else {
            $record->timecreated = time();
            $DB->insert_record('local_forum_ai_config', $record);
            $message = 'Configuración creada correctamente';
        }

        redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);

    } catch (Exception $e) {
        redirect($PAGE->url, 'Error al guardar: ' . $e->getMessage(),
                null, \core\output\notification::NOTIFY_ERROR);
    }
}

// Obtener configuración actual con manejo de errores
$config = new stdClass();
$config->enabled = 0;
$config->bot_userid = '';
$config->reply_message = 'Gracias por tu participación. Un moderador revisará tu mensaje.';
$config->ai_model = 'gpt-3.5';

try {
    $existing_config = $DB->get_record('local_forum_ai_config', array('forumid' => $forumid));
    if ($existing_config) {
        $config = $existing_config;
    }
} catch (Exception $e) {
    // Usar valores por defecto si hay error
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Configuración Forum AI para: ' . format_string($forum->name));

// Resto del formulario igual...
echo '<div class="container-fluid">';
echo '<form method="post" action="' . $PAGE->url . '" class="form">';
echo '<input type="hidden" name="action" value="save">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

echo '<div class="form-group">';
echo '<label for="enabled">Habilitar AI</label>';
echo '<select name="enabled" id="enabled" class="form-control">';
echo '<option value="0"' . ($config->enabled == 0 ? ' selected' : '') . '>No</option>';
echo '<option value="1"' . ($config->enabled == 1 ? ' selected' : '') . '>Sí</option>';
echo '</select>';
echo '</div>';

echo '<div class="form-group">';
echo '<label for="bot_userid">ID Usuario Bot</label>';
echo '<input type="number" name="bot_userid" id="bot_userid" value="' . s($config->bot_userid) . '" class="form-control">';
echo '</div>';

echo '<div class="form-group">';
echo '<label for="reply_message">Mensaje base</label>';
echo '<textarea name="reply_message" id="reply_message" rows="4" class="form-control">' . s($config->reply_message) . '</textarea>';
echo '</div>';

echo '<div class="form-group">';
echo '<button type="submit" class="btn btn-primary">Guardar</button> ';
echo '<a href="' . new moodle_url('/mod/forum/view.php', array('id' => $cm->id)) . '" class="btn btn-secondary">Cancelar</a>';
echo '</div>';

echo '</form>';
echo '</div>';

echo $OUTPUT->footer();
