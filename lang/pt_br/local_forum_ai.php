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

$string['action_failed'] = 'A ação não pôde ser processada.';
$string['actions'] = 'Ações';
$string['ai_response'] = 'Resposta de IA';
$string['ai_response_approved'] = 'Resposta de IA aprovada';
$string['ai_response_proposed'] = 'Resposta de IA proposta';
$string['ai_response_rejected'] = 'Resposta de IA rejeitada';
$string['aiproposed'] = 'Resposta de IA proposta';
$string['alreadysubmitted'] = 'Esta solicitação já foi aprovada, rejeitada ou não existe.';
$string['approve'] = 'Aprovar';
$string['backtodiscussion'] = 'Voltar à discussão';
$string['backup:includeai'] = 'Incluir dados do fórum de IA nos backups';
$string['cancel'] = 'Cancelar';
$string['col_message'] = 'Mensagem';
$string['config_created'] = 'Configuração criada com sucesso.';
$string['config_updated'] = 'Configuração atualizada com sucesso.';
$string['course'] = 'Curso';
$string['coursename'] = 'Curso';
$string['created'] = 'Criado';
$string['datacurso_custom'] = 'Fórum de IA Datacurso';
$string['default_reply_message'] = 'Responda com um tom empático e motivador';
$string['discussion'] = 'Discussão';
$string['discussion_label'] = 'Discussão: {$a}';
$string['discussioninfo'] = 'Informações da discussão';
$string['discussionmsg'] = 'Mensagem gerada por IA';
$string['discussionname'] = 'Assunto';
$string['enabled'] = 'Ativar IA';
$string['err_table_missing'] = 'A tabela de configuração do Fórum de IA não existe. Atualize o plugin em Administração do site > Notificações.';
$string['error_airequest'] = 'Erro ao comunicar-se com o serviço de IA: {$a}';
$string['error_saving'] = 'Erro ao salvar a configuração: {$a}';
$string['forum'] = 'Fórum';
$string['forumname'] = 'Fórum';
$string['goto_notifications'] = 'Ir para Notificações';
$string['historyresponses'] = 'Histórico de respostas do Fórum de IA';
$string['invalidaction'] = 'A ação especificada é inválida.';
$string['level'] = 'Nível: {$a}';
$string['messageprovider:ai_approval_request'] = 'Solicitação de aprovação de IA';
$string['modal_title'] = 'Detalhes do histórico da discussão';
$string['modal_title_pending'] = 'Detalhes da discussão';
$string['no'] = 'Não';
$string['no_posts'] = 'Nenhuma postagem encontrada nesta discussão.';
$string['nohistory'] = 'Nenhum histórico de respostas de IA aprovadas ou rejeitadas.';
$string['nopermission'] = 'Você não tem permissão para aprovar ou rejeitar respostas de IA.';
$string['noresponses'] = 'Nenhuma resposta pendente de aprovação.';
$string['noteachersfound'] = 'Nenhum professor encontrado para este curso.';
$string['notification_approve_link'] = 'Aprovar diretamente: {$a->url}';
$string['notification_course_label'] = 'Curso';
$string['notification_greeting'] = 'Olá {$a->firstname},';
$string['notification_intro'] = 'Uma resposta automática foi gerada para a discussão "{$a->discussion}" no fórum "{$a->forum}" do curso "{$a->course}".';
$string['notification_preview'] = 'Pré-visualização:';
$string['notification_reject_link'] = 'Rejeitar: {$a->url}';
$string['notification_review_button'] = 'Revisar resposta';
$string['notification_review_link'] = 'Revise e aprove a resposta em: {$a->url}';
$string['notification_smallmessage'] = 'Nova resposta de IA pendente em "{$a->discussion}"';
$string['notification_subject'] = 'Aprovação necessária: Resposta de IA';
$string['originalmessage'] = 'Mensagem original';
$string['pendingresponses'] = 'Respostas pendentes do Fórum de IA';
$string['pluginname'] = 'Fórum de IA';
$string['preview'] = 'Mensagem de IA';
$string['privacy:metadata:local_forum_ai_config'] = 'Armazena configurações de IA por fórum.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Indica se a IA está habilitada para este fórum.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'ID do fórum ao qual esta configuração pertence.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Modelo de resposta gerado pela IA.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Indica se as respostas de IA requerem aprovação antes de serem publicadas.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Data de criação da configuração.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Data da última modificação da configuração.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Dados armazenados pelo plugin Fórum de IA.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Token de aprovação vinculado à publicação.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Data em que a resposta foi aprovada.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID do usuário que criou a publicação.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID da discussão relacionada.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID do fórum onde a resposta foi gerada.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Mensagem gerada pela inteligência artificial.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Status da publicação (aprovada, pendente ou rejeitada).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Assunto ou tema da mensagem.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Data em que o registro foi criado.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Data em que o registro foi atualizado.';
$string['reject'] = 'Rejeitar';
$string['reply_message'] = 'Dê instruções à IA';
$string['replylevel'] = 'Nível de resposta {$a}';
$string['require_approval'] = 'Revisar resposta de IA';
$string['response_approved'] = 'Resposta de IA aprovada e publicada com sucesso.';
$string['response_rejected'] = 'Resposta de IA rejeitada.';
$string['response_update_failed'] = 'Não foi possível atualizar a resposta.';
$string['response_updated'] = 'Resposta atualizada com sucesso.';
$string['reviewtitle'] = 'Revisar resposta de IA';
$string['save'] = 'Salvar';
$string['saveapprove'] = 'Salvar e aprovar';
$string['settings'] = 'Configurações para: ';
$string['settings_forum'] = 'Configurações para {$a}';
$string['status'] = 'Status';
$string['statusapproved'] = 'Aprovado';
$string['statuspending'] = 'Pendente';
$string['statusrejected'] = 'Rejeitado';
$string['username'] = 'Criador';
$string['viewdetails'] = 'Detalhes';
$string['yes'] = 'Sim';
