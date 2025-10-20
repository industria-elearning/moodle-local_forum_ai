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

$string['actions'] = 'Aktionen';
$string['ai_response'] = 'KI-Antwort';
$string['ai_response_approved'] = 'KI-Antwort genehmigt';
$string['ai_response_proposed'] = 'Vorgeschlagene KI-Antwort';
$string['ai_response_rejected'] = 'KI-Antwort abgelehnt';
$string['aiproposed'] = 'Vorgeschlagene KI-Antwort';
$string['alreadysubmitted'] = 'Diese Anfrage wurde bereits genehmigt, abgelehnt oder existiert nicht.';
$string['approve'] = 'Genehmigen';
$string['backtodiscussion'] = 'Zur Diskussion zurück';
$string['cancel'] = 'Abbrechen';
$string['col_message'] = 'Nachricht';
$string['course'] = 'Kurs';
$string['coursename'] = 'Kurs';
$string['created'] = 'Erstellt';
$string['datacurso_custom'] = 'Datacurso Forum KI';
$string['default_reply_message'] = 'Antworte mit einem empathischen und motivierenden Ton';
$string['discussion'] = 'Diskussion';
$string['discussion_label'] = 'Diskussion: {$a}';
$string['discussioninfo'] = 'Diskussionsinformation';
$string['discussionmsg'] = 'Nachricht von KI erstellt';
$string['discussionname'] = 'Betreff';
$string['enabled'] = 'KI aktivieren';
$string['forum'] = 'Forum';
$string['forumname'] = 'Forum';
$string['historyresponses'] = 'Verlauf der KI-Antworten im Forum';
$string['level'] = 'Level: {$a}';
$string['messageprovider:ai_approval_request'] = 'Anfrage zur KI-Genehmigung';
$string['modal_title'] = 'Details zum Diskussionsverlauf';
$string['modal_title_pending'] = 'Diskussionsdetails';
$string['no'] = 'Nein';
$string['no_posts'] = 'In dieser Diskussion wurden keine Beiträge gefunden.';
$string['nohistory'] = 'Kein Verlauf genehmigter oder abgelehnter KI-Antworten vorhanden.';
$string['nopermission'] = 'Du hast keine Berechtigung, KI-Antworten zu genehmigen/abzulehnen.';
$string['noresponses'] = 'Es gibt keine wartenden Antworten zur Genehmigung.';
$string['notification_approve_link'] = 'Direkt genehmigen: {$a->url}';
$string['notification_course_label'] = 'Kurs';
$string['notification_greeting'] = 'Hallo {$a->firstname},';
$string['notification_intro'] = 'Für die Diskussion „{$a->discussion}“ im Forum „{$a->forum}“ des Kurses „{$a->course}“ wurde eine automatische Antwort generiert.';
$string['notification_preview'] = 'Vorschau:';
$string['notification_reject_link'] = 'Ablehnen: {$a->url}';
$string['notification_review_button'] = 'Antwort prüfen';
$string['notification_review_link'] = 'Antwort prüfen und genehmigen unter: {$a->url}';
$string['notification_smallmessage'] = 'Neue KI-Antwort wartet in „{$a->discussion}“';
$string['notification_subject'] = 'Genehmigung erforderlich: KI-Antwort';
$string['originalmessage'] = 'Originalnachricht';
$string['pendingresponses'] = 'Ausstehende Forum-KI-Antworten';
$string['pluginname'] = 'Forum KI';
$string['preview'] = 'KI-Nachricht';
$string['privacy:metadata:local_forum_ai_config'] = 'Speichert KI-Konfigurationen pro Forum.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Gibt an, ob KI für dieses Forum aktiviert ist.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'Die ID des Forums, zu dem diese Konfiguration gehört.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Antwortvorlage, die von der KI generiert wurde.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Gibt an, ob KI-Antworten eine Genehmigung benötigen, bevor sie veröffentlicht werden.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Datum der Konfigurationserstellung.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Datum der letzten Änderung der Konfiguration.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Von dem Plugin Forum KI gespeicherte Daten.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Genehmigungstoken, das mit der Veröffentlichung verknüpft ist.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Datum, an dem die Antwort genehmigt wurde.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID des Benutzers, der die Veröffentlichung erstellt hat.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID der zugehörigen Diskussion.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID des Forums, in dem die Antwort generiert wurde.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Von der KI generierte Nachricht.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Status der Veröffentlichung (genehmigt, ausstehend oder abgelehnt).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Betreff oder Thema der Nachricht.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Datum, an dem der Eintrag erstellt wurde.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Datum, an dem der Eintrag zuletzt geändert wurde.';
$string['reject'] = 'Ablehnen';
$string['reply_message'] = 'Gib der KI Anweisungen';
$string['replylevel'] = 'Antwort Level {$a}';
$string['require_approval'] = 'KI-Antwort prüfen';
$string['reviewtitle'] = 'KI-Antwort prüfen';
$string['save'] = 'Speichern';
$string['saveapprove'] = 'Speichern und genehmigen';
$string['settings'] = 'Konfiguration für: ';
$string['status'] = 'Status';
$string['statusapproved'] = 'Genehmigt';
$string['statuspending'] = 'Ausstehend';
$string['statusrejected'] = 'Abgelehnt';
$string['username'] = 'Ersteller';
$string['viewdetails'] = 'Details';
$string['yes'] = 'Ja';
