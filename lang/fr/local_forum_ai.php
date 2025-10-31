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

$string['action_failed'] = 'L’action n’a pas pu être traitée.';
$string['actions'] = 'Actions';
$string['ai_response'] = 'Réponse IA';
$string['ai_response_approved'] = 'Réponse IA approuvée';
$string['ai_response_proposed'] = 'Réponse IA proposée';
$string['ai_response_rejected'] = 'Réponse IA rejetée';
$string['aiproposed'] = 'Réponse IA proposée';
$string['alreadysubmitted'] = 'Cette demande a déjà été approuvée, rejetée ou n’existe pas.';
$string['approve'] = 'Approuver';
$string['backtodiscussion'] = 'Retour à la discussion';
$string['backup:includeai'] = 'Inclure les données du forum IA dans les sauvegardes';
$string['cancel'] = 'Annuler';
$string['col_message'] = 'Message';
$string['config_created'] = 'Configuration créée avec succès.';
$string['config_updated'] = 'Configuration mise à jour avec succès.';
$string['course'] = 'Cours';
$string['coursename'] = 'Cours';
$string['created'] = 'Créé';
$string['datacurso_custom'] = 'Forum IA Datacurso';
$string['default_reply_message'] = 'Réponds avec un ton empathique et motivant';
$string['discussion'] = 'Discussion';
$string['discussion_label'] = 'Discussion : {$a}';
$string['discussioninfo'] = 'Informations sur la discussion';
$string['discussionmsg'] = 'Message généré par l’IA';
$string['discussionname'] = 'Sujet';
$string['enabled'] = 'Activer l’IA';
$string['err_table_missing'] = 'La table de configuration du Forum IA n’existe pas. Veuillez mettre à jour le plugin via Administration du site > Notifications.';
$string['error_airequest'] = 'Erreur de communication avec le service IA : {$a}';
$string['error_saving'] = 'Erreur lors de l’enregistrement de la configuration : {$a}';
$string['forum'] = 'Forum';
$string['forumname'] = 'Forum';
$string['goto_notifications'] = 'Aller aux notifications';
$string['historyresponses'] = 'Historique des réponses Forum IA';
$string['invalidaction'] = 'L’action indiquée n’est pas valide.';
$string['level'] = 'Niveau : {$a}';
$string['messageprovider:ai_approval_request'] = 'Demande d’approbation IA';
$string['modal_title'] = 'Détails de l’historique de la discussion';
$string['modal_title_pending'] = 'Détails de la discussion';
$string['no'] = 'Non';
$string['no_posts'] = 'Aucun message trouvé dans cette discussion.';
$string['nohistory'] = 'Aucun historique de réponses IA approuvées ou rejetées.';
$string['nopermission'] = 'Vous n’avez pas la permission d’approuver ou de rejeter les réponses IA.';
$string['noresponses'] = 'Aucune réponse en attente d’approbation.';
$string['noteachersfound'] = 'Aucun enseignant trouvé pour ce cours.';
$string['notification_approve_link'] = 'Approuver directement : {$a->url}';
$string['notification_course_label'] = 'Cours';
$string['notification_greeting'] = 'Bonjour {$a->firstname},';
$string['notification_intro'] = 'Une réponse automatique a été générée pour la discussion "{$a->discussion}" dans le forum "{$a->forum}" du cours "{$a->course}".';
$string['notification_preview'] = 'Aperçu :';
$string['notification_reject_link'] = 'Rejeter : {$a->url}';
$string['notification_review_button'] = 'Examiner la réponse';
$string['notification_review_link'] = 'Examinez et approuvez la réponse sur : {$a->url}';
$string['notification_smallmessage'] = 'Nouvelle réponse IA en attente dans "{$a->discussion}"';
$string['notification_subject'] = 'Approbation requise : Réponse IA';
$string['originalmessage'] = 'Message original';
$string['pendingresponses'] = 'Réponses Forum IA en attente';
$string['pluginname'] = 'Forum IA';
$string['preview'] = 'Message IA';
$string['privacy:metadata:local_forum_ai_config'] = 'Stocke les configurations IA par forum.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Indique si l’IA est activée pour ce forum.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'ID du forum correspondant à cette configuration.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Modèle de réponse généré par l’IA.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Indique si les réponses IA nécessitent une approbation avant publication.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Date de création de la configuration.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Date de dernière modification de la configuration.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Données stockées par le plugin Forum IA.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Jeton d’approbation lié à la publication.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Date d’approbation de la réponse.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID de l’utilisateur ayant créé la publication.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID de la discussion liée.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID du forum où la réponse a été générée.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Message généré par l’intelligence artificielle.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Statut de la publication (approuvée, en attente ou rejetée).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Sujet du message.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Date de création de l’enregistrement.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Date de mise à jour de l’enregistrement.';
$string['reject'] = 'Rejeter';
$string['reply_message'] = 'Donner des instructions à l’IA';
$string['replylevel'] = 'Niveau de réponse {$a}';
$string['require_approval'] = 'Examiner la réponse IA';
$string['response_approved'] = 'Réponse IA approuvée et publiée avec succès.';
$string['response_rejected'] = 'Réponse IA rejetée.';
$string['response_update_failed'] = 'La réponse n’a pas pu être mise à jour.';
$string['response_updated'] = 'Réponse mise à jour avec succès.';
$string['reviewtitle'] = 'Examiner la réponse IA';
$string['save'] = 'Enregistrer';
$string['saveapprove'] = 'Enregistrer et approuver';
$string['settings'] = 'Paramètres pour : ';
$string['settings_forum'] = 'Paramètres pour {$a}';
$string['status'] = 'Statut';
$string['statusapproved'] = 'Approuvé';
$string['statuspending'] = 'En attente';
$string['statusrejected'] = 'Rejeté';
$string['username'] = 'Créateur';
$string['viewdetails'] = 'Détails';
$string['yes'] = 'Oui';
