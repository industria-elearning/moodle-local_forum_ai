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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$forumid = required_param('forumid', PARAM_INT);
$action = optional_param('action', 'view', PARAM_ALPHA);

try {
    // Verificar foro existe.
    $forum = $DB->get_record('forum', ['id' => $forumid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $forum->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
} catch (Exception $e) {
    throw new \moodle_exception('invalidforumid', 'forum');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/forum:addquestion', $context);

$PAGE->set_url('/local/forum_ai/config.php', ['forumid' => $forumid]);
$PAGE->set_title(get_string('pluginname', 'local_forum_ai'));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);
$PAGE->navbar->add($forum->name, new moodle_url('/mod/forum/view.php', ['id' => $cm->id]));
$PAGE->navbar->add(get_string('pluginname', 'local_forum_ai'));

// Verificar si la tabla existe.
$tableexists = $DB->get_manager()->table_exists('local_forum_ai_config');

if (!$tableexists) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(
        'La tabla de configuración no existe. Por favor, actualiza el plugin desde ' .
        'Administración del sitio > Notificaciones.',
        'error'
    );
    echo '<p><a href="' . new moodle_url('/admin/index.php') .
        '" class="btn btn-primary">Ir a Notificaciones</a></p>';
    echo $OUTPUT->footer();
    exit;
}

// Procesar formulario.
if ($action === 'save' && confirm_sesskey()) {
    $enabled = optional_param('enabled', 0, PARAM_INT);
    $botuserid = optional_param('bot_userid', null, PARAM_INT);
    $replymessage = optional_param('reply_message', '', PARAM_TEXT);
    $aimodel = optional_param('ai_model', 'gpt-3.5', PARAM_TEXT);
    $requireapproval = optional_param('require_approval', 1, PARAM_INT);

    try {
        // Verificar si ya existe configuración para este foro.
        $existing = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);

        $record = new stdClass();
        $record->forumid = $forumid;
        $record->enabled = $enabled;
        $record->bot_userid = empty($botuserid) ? null : $botuserid;
        $record->reply_message = $replymessage;
        $record->ai_model = $aimodel;
        $record->require_approval = $requireapproval;
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
        redirect(
            $PAGE->url,
            'Error al guardar: ' . $e->getMessage(),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

// Obtener configuración actual con manejo de errores.
$config = new stdClass();
$config->enabled = 0;
$config->bot_userid = '';
$config->reply_message = 'Gracias por tu participación. Un moderador revisará tu mensaje.';
$config->ai_model = 'gpt-3.5';
$config->require_approval = 1;

try {
    $existingconfig = $DB->get_record('local_forum_ai_config', ['forumid' => $forumid]);
    if ($existingconfig) {
        $config = $existingconfig;
        if (!isset($config->require_approval)) {
            $config->require_approval = 1; // Valor por defecto si falta.
        }
    }
} catch (Exception $e) {
    debugging('Error al obtener configuración: ' . $e->getMessage(), DEBUG_DEVELOPER);
    // Usar valores por defecto si hay error.
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Configuración Forum AI para: ' . format_string($forum->name));

// Resto del formulario.
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
echo '<label for="require_approval">Revisar respuesta IA</label>';
echo '<select name="require_approval" id="require_approval" class="form-control">';
echo '<option value="1"' . ($config->require_approval == 1 ? ' selected' : '') . '>Sí</option>';
echo '<option value="0"' . ($config->require_approval == 0 ? ' selected' : '') . '>No</option>';
echo '</select>';
echo '</div>';

echo '<div class="form-group">';
echo '<label for="bot_userid">ID Usuario Bot</label>';
echo '<input type="number" name="bot_userid" id="bot_userid" value="' .
    s($config->bot_userid) . '" class="form-control">';
echo '</div>';

echo '<div class="form-group">';
echo '<label for="reply_message">Dale indicaciones a la IA</label>';
echo '<textarea name="reply_message" id="reply_message" rows="4" class="form-control">' .
    s($config->reply_message) . '</textarea>';
echo '</div>';

echo '<div class="form-group">';
echo '<button type="submit" class="btn btn-primary">Guardar</button> ';
echo '<a href="' . new moodle_url('/mod/forum/view.php', ['id' => $cm->id]) .
    '" class="btn btn-secondary">Cancelar</a>';
echo '</div>';

echo '</form>';
echo '</div>';

echo $OUTPUT->footer();
