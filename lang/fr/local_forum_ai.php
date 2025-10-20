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

$string['actions'] = 'Actions';
$string['ai_response'] = 'Réponse IA';
$string['ai_response_approved'] = 'Réponse IA approuvée';
$string['ai_response_proposed'] = 'Réponse IA proposée';
$string['ai_response_rejected'] = 'Réponse IA rejetée';
$string['aiproposed'] = 'Réponse IA proposée';
$string['alreadysubmitted'] = 'Cette demande a déjà été approuvée, rejetée ou n’existe pas.';
$string['approve'] = 'Approuver';
$string['backtodiscussion'] = 'Retour à la discussion';
$string['cancel'] = 'Annuler';
$string['col_message'] = 'Message';
$string['course'] = 'Cours';
$string['coursename'] = 'Cours';
$string['created'] = 'Créé';
$string['datacurso_custom'] = 'Datacurso Forum IA';
$string['default_reply_message'] = 'Répondre avec un ton empathique et motivant';
$string['discussion'] = 'Discussion';
$string['discussion_label'] = 'Discussion : {$a}';
$string['discussioninfo'] = 'Informations sur la discussion';
$string['discussionmsg'] = 'Message généré par IA';
$string['discussionname'] = 'Sujet';
$string['enabled'] = 'Activer IA';
$string['forum'] = 'Forum';
$string['forumname'] = 'Forum';
$string['historyresponses'] = 'Historique des réponses Forum IA';
$string['level'] = 'Niveau : {$a}';
$string['messageprovider:ai_approval_request'] = 'Demande d’approbation IA';
$string['modal_title'] = 'Détails de l’historique de discussion';
$string['modal_title_pending'] = 'Détails de la discussion';
$string['no'] = 'Non';
$string['no_posts'] = 'Aucun message trouvé dans cette discussion.';
$string['nohistory'] = 'Aucun historique de réponses IA approuvées ou rejetées.';
$string['nopermission'] = 'Vous n’avez pas l’autorisation d’approuver/rejeter les réponses IA.';
$string['noresponses'] = 'Aucune réponse en attente d’approbation.';
$string['notification_approve_link'] = 'Approuver directement : {$a->url}';
$string['notification_course_label'] = 'Cours';
$string['notification_greeting'] = 'Bonjour {$a->firstname},';
$string['notification_intro'] = 'Une réponse automatique a été générée pour la discussion « {$a->discussion} » sur le forum « {$a->forum} » du cours « {$a->course} ».';
$string['notification_preview'] = 'Aperçu :';
$string['notification_reject_link'] = 'Rejeter : {$a->url}';
$string['notification_review_button'] = 'Vérifier la réponse';
$string['notification_review_link'] = 'Vérifiez et approuvez la réponse sur : {$a->url}';
$string['notification_smallmessage'] = 'Nouvelle réponse IA en attente dans « {$a->discussion} »';
$string['notification_subject'] = 'Approbation requise : Réponse IA';
$string['originalmessage'] = 'Message original';
$string['pendingresponses'] = 'Réponses Forum IA en attente';
$string['pluginname'] = 'Forum IA';
$string['preview'] = 'Message IA';
$string['privacy:metadata:local_forum_ai_config'] = 'Stocke les configurations IA par forum.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Indique si l’IA est activée pour ce forum.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'L’ID du forum auquel cette configuration appartient.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Modèle de réponse généré par l’IA.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Indique si les réponses IA nécessitent une approbation avant publication.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Date de création de la configuration.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Date de dernière modification de la configuration.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Données stockées par l’extension Forum IA.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Jeton d’approbation lié à la publication.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Date à laquelle la réponse a été approuvée.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID de l’utilisateur ayant créé la publication.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID de la discussion concernée.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID du forum dans lequel la réponse a été générée.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Message généré par l’IA.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'État de la publication (approuvée, en attente ou rejetée).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Sujet ou thème du message.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Date à laquelle l’enregistrement a été créé.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Date à laquelle l’enregistrement a été mis à jour.';
$string['reject'] = 'Rejeter';
$string['reply_message'] = 'Donnez des indications à l’IA';
$string['replylevel'] = 'Réponse niveau {$a}';
$string['require_approval'] = 'Vérifier la réponse IA';
$string['reviewtitle'] = 'Vérifier la réponse IA';
$string['save'] = 'Enregistrer';
$string['saveapprove'] = 'Enregistrer et approuver';
$string['settings'] = 'Configuration pour : ';
$string['status'] = 'Statut';
$string['statusapproved'] = 'Approuvé';
$string['statuspending'] = 'En attente';
$string['statusrejected'] = 'Rejeté';
$string['username'] = 'Créateur';
$string['viewdetails'] = 'Détails';
$string['yes'] = 'Oui';
