<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_forum_ai
 * @category    string
 * @copyright   2025 Piero Llanos <piero@datacurso.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Acciones';
$string['ai_response'] = 'Respuesta IA';
$string['ai_response_approved'] = 'Respuesta IA Aprobada';
$string['ai_response_proposed'] = 'Respuesta IA propuesta';
$string['ai_response_rejected'] = 'Respuesta IA Rechazada';
$string['aiproposed'] = 'Respuesta AI propuesta';
$string['alreadysubmitted'] = 'Esta solicitud ya fue aprobada, rechazada o no existe.';
$string['approve'] = 'Aprobar';
$string['backtodiscussion'] = 'Volver al debate';
$string['cancel'] = 'Cancelar';
$string['col_message'] = 'Mensaje';
$string['course'] = 'Curso';
$string['coursename'] = 'Curso';
$string['created'] = 'Creado';
$string['datacurso_custom'] = 'Datacurso Forum AI';
$string['default_reply_message'] = 'Responde con tono empático y motivador';
$string['discussion'] = 'Debate';
$string['discussion_label'] = 'Debate: {$a}';
$string['discussioninfo'] = 'Información del debate';
$string['discussionmsg'] = 'Mensaje hecho por IA';
$string['discussionname'] = 'Asunto';
$string['enabled'] = 'Habilitar AI';
$string['error_airequest'] = 'Error al comunicarse con el servicio de IA: {$a}';
$string['forum'] = 'Foro';
$string['forumname'] = 'Foro';
$string['historyresponses'] = 'Historial de respuestas Foro IA';
$string['level'] = 'Nivel: {$a}';
$string['messageprovider:ai_approval_request'] = 'Solicitud de aprobación de IA';
$string['modal_title'] = 'Detalles del historial de debate';
$string['modal_title_pending'] = 'Detalles del debate';
$string['no'] = 'No';
$string['no_posts'] = 'No se encontraron posts en este debate.';
$string['nohistory'] = 'No hay historial de respuestas IA aprobadas o rechazadas.';
$string['nopermission'] = 'No tienes permisos para aprobar/rechazar respuestas AI.';
$string['noresponses'] = 'No hay respuestas pendientes de aprobación.';
$string['notification_approve_link'] = 'Aprobar directamente: {$a->url}';
$string['notification_course_label'] = 'Curso';
$string['notification_greeting'] = 'Hola {$a->firstname},';
$string['notification_intro'] = 'Se ha generado una respuesta automática para el debate "{$a->discussion}" en el foro "{$a->forum}" del curso "{$a->course}".';
$string['notification_preview'] = 'Vista previa:';
$string['notification_reject_link'] = 'Rechazar: {$a->url}';
$string['notification_review_button'] = 'Revisar respuesta';
$string['notification_review_link'] = 'Revisa y aprueba la respuesta en: {$a->url}';
$string['notification_smallmessage'] = 'Nueva respuesta AI pendiente en "{$a->discussion}"';
$string['notification_subject'] = 'Aprobación requerida: Respuesta AI';
$string['originalmessage'] = 'Mensaje original';
$string['pendingresponses'] = 'Respuestas Foro IA Pendientes';
$string['pluginname'] = 'Forum AI';
$string['preview'] = 'Mensaje IA';
$string['privacy:metadata:local_forum_ai_config'] = 'Almacena las configuraciones de IA por foro.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Indica si la IA está habilitada para este foro.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'El ID del foro al que pertenece esta configuración.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Plantilla de respuesta generada por la IA.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Indica si las respuestas de IA requieren aprobación antes de publicarse.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Fecha de creación de la configuración.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Fecha de última modificación de la configuración.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Datos almacenados por el plugin Foro IA.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Token de aprobación vinculado a la publicación.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Fecha en la que fue aprobada la respuesta.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID del usuario que creó la publicación.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID de la discusión relacionada.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID del foro en el que se generó la respuesta.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Mensaje generado por la inteligencia artificial.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Estado de la publicación (aprobada, pendiente o rechazada).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Asunto o tema del mensaje.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Fecha en que se creó el registro.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Fecha en que se actualizó el registro.';
$string['reject'] = 'Rechazar';
$string['reply_message'] = 'Dale indicaciones a la IA';
$string['replylevel'] = 'Respuesta nivel {$a}';
$string['require_approval'] = 'Revisar respuesta IA';
$string['reviewtitle'] = 'Revisar respuesta IA';
$string['save'] = 'Guardar';
$string['saveapprove'] = 'Guardar y aprobar';
$string['settings'] = 'Configuración para: ';
$string['status'] = 'Estado';
$string['statusapproved'] = 'Aprobado';
$string['statuspending'] = 'Pendiente';
$string['statusrejected'] = 'Rechazado';
$string['username'] = 'Creador';
$string['viewdetails'] = 'Detalles';
$string['yes'] = 'Si';
