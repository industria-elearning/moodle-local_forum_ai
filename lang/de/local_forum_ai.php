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
 * @copyright   2025 Datacurso
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['action_failed'] = 'Aktion konnte nicht verarbeitet werden.';
$string['actions'] = 'Aktionen';
$string['ai_response'] = 'KI-Antwort';
$string['ai_response_approved'] = 'KI-Antwort genehmigt';
$string['ai_response_proposed'] = 'Vorgeschlagene KI-Antwort';
$string['ai_response_rejected'] = 'KI-Antwort abgelehnt';
$string['aiproposed'] = 'Vorgeschlagene KI-Antwort';
$string['alreadysubmitted'] = 'Diese Anfrage wurde bereits genehmigt, abgelehnt oder existiert nicht.';
$string['approve'] = 'Genehmigen';
$string['backtodiscussion'] = 'Zurück zur Diskussion';
$string['backup:includeai'] = 'KI-Forendaten in Sicherungen einschließen';
$string['cancel'] = 'Abbrechen';
$string['col_message'] = 'Nachricht';
$string['config_created'] = 'Konfiguration erfolgreich erstellt.';
$string['config_updated'] = 'Konfiguration erfolgreich aktualisiert.';
$string['course'] = 'Kurs';
$string['coursename'] = 'Kurs';
$string['created'] = 'Erstellt';
$string['datacurso_custom'] = 'Datacurso Forum KI';
$string['default_reply_message'] = 'Antworte mit einem empathischen und motivierenden Ton';
$string['discussion'] = 'Diskussion';
$string['discussion_label'] = 'Diskussion: {$a}';
$string['discussioninfo'] = 'Diskussionsinformationen';
$string['discussionmsg'] = 'KI-generierte Nachricht';
$string['discussionname'] = 'Thema';
$string['enabled'] = 'KI aktivieren';
$string['err_table_missing'] = 'Die Konfigurationstabelle für Forum KI existiert nicht. Bitte aktualisiere das Plugin unter Website-Administration > Mitteilungen.';
$string['error_airequest'] = 'Fehler bei der Kommunikation mit dem KI-Dienst: {$a}';
$string['error_saving'] = 'Fehler beim Speichern der Konfiguration: {$a}';
$string['forum'] = 'Forum';
$string['forumname'] = 'Forum';
$string['goto_notifications'] = 'Zu Benachrichtigungen gehen';
$string['historyresponses'] = 'KI-Forum Antwortverlauf';
$string['invalidaction'] = 'Die angegebene Aktion ist ungültig.';
$string['level'] = 'Stufe: {$a}';
$string['messageprovider:ai_approval_request'] = 'KI-Genehmigungsanfrage';
$string['modal_title'] = 'Details zum Diskussionsverlauf';
$string['modal_title_pending'] = 'Diskussionsdetails';
$string['no'] = 'Nein';
$string['no_posts'] = 'Keine Beiträge in dieser Diskussion gefunden.';
$string['nohistory'] = 'Kein Verlauf genehmigter oder abgelehnter KI-Antworten.';
$string['nopermission'] = 'Du hast keine Berechtigung, KI-Antworten zu genehmigen oder abzulehnen.';
$string['noresponses'] = 'Keine Antworten zur Genehmigung ausstehend.';
$string['noteachersfound'] = 'Keine Lehrer für diesen Kurs gefunden.';
$string['notification_approve_link'] = 'Direkt genehmigen: {$a->url}';
$string['notification_course_label'] = 'Kurs';
$string['notification_greeting'] = 'Hallo {$a->firstname},';
$string['notification_intro'] = 'Eine automatische Antwort wurde für die Diskussion "{$a->discussion}" im Forum "{$a->forum}" des Kurses "{$a->course}" generiert.';
$string['notification_preview'] = 'Vorschau:';
$string['notification_reject_link'] = 'Ablehnen: {$a->url}';
$string['notification_review_button'] = 'Antwort überprüfen';
$string['notification_review_link'] = 'Überprüfe und genehmige die Antwort unter: {$a->url}';
$string['notification_smallmessage'] = 'Neue KI-Antwort ausstehend in "{$a->discussion}"';
$string['notification_subject'] = 'Genehmigung erforderlich: KI-Antwort';
$string['originalmessage'] = 'Ursprüngliche Nachricht';
$string['pendingresponses'] = 'Ausstehende KI-Forum-Antworten';
$string['pluginname'] = 'Forum KI';
$string['preview'] = 'KI-Nachricht';
$string['privacy:metadata:local_forum_ai_config'] = 'Speichert KI-Konfigurationen pro Forum.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Gibt an, ob KI für dieses Forum aktiviert ist.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'Die ID des Forums, zu dem diese Konfiguration gehört.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Antwortvorlage, die von der KI generiert wurde.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Gibt an, ob KI-Antworten vor der Veröffentlichung genehmigt werden müssen.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Erstellungsdatum der Konfiguration.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Datum der letzten Änderung der Konfiguration.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Von der Forum-KI gespeicherte Daten.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Genehmigungstoken für die Veröffentlichung.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Datum, an dem die Antwort genehmigt wurde.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID des Benutzers, der den Beitrag erstellt hat.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID der zugehörigen Diskussion.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID des Forums, in dem die Antwort generiert wurde.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Von der KI generierte Nachricht.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Status des Beitrags (genehmigt, ausstehend oder abgelehnt).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Betreff oder Thema der Nachricht.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Datum, an dem der Datensatz erstellt wurde.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Datum, an dem der Datensatz aktualisiert wurde.';
$string['reject'] = 'Ablehnen';
$string['reply_message'] = 'Anweisungen an die KI geben';
$string['replylevel'] = 'Antwortstufe {$a}';
$string['require_approval'] = 'KI-Antwort überprüfen';
$string['response_approved'] = 'KI-Antwort erfolgreich genehmigt und veröffentlicht.';
$string['response_rejected'] = 'KI-Antwort abgelehnt.';
$string['response_update_failed'] = 'Antwort konnte nicht aktualisiert werden.';
$string['response_updated'] = 'Antwort erfolgreich aktualisiert.';
$string['reviewtitle'] = 'KI-Antwort überprüfen';
$string['save'] = 'Speichern';
$string['saveapprove'] = 'Speichern und genehmigen';
$string['settings'] = 'Einstellungen für: ';
$string['settings_forum'] = 'Einstellungen für {$a}';
$string['status'] = 'Status';
$string['statusapproved'] = 'Genehmigt';
$string['statuspending'] = 'Ausstehend';
$string['statusrejected'] = 'Abgelehnt';
$string['username'] = 'Ersteller';
$string['viewdetails'] = 'Details';
$string['yes'] = 'Ja';
