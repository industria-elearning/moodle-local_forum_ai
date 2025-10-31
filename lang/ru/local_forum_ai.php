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

$string['action_failed'] = 'Не удалось обработать действие.';
$string['actions'] = 'Действия';
$string['ai_response'] = 'Ответ ИИ';
$string['ai_response_approved'] = 'Ответ ИИ одобрен';
$string['ai_response_proposed'] = 'Предложенный ответ ИИ';
$string['ai_response_rejected'] = 'Ответ ИИ отклонён';
$string['aiproposed'] = 'Предложенный ответ ИИ';
$string['alreadysubmitted'] = 'Этот запрос уже был одобрен, отклонён или не существует.';
$string['approve'] = 'Одобрить';
$string['backtodiscussion'] = 'Назад к обсуждению';
$string['backup:includeai'] = 'Включить данные форума ИИ в резервные копии';
$string['cancel'] = 'Отмена';
$string['col_message'] = 'Сообщение';
$string['config_created'] = 'Конфигурация успешно создана.';
$string['config_updated'] = 'Конфигурация успешно обновлена.';
$string['course'] = 'Курс';
$string['coursename'] = 'Курс';
$string['created'] = 'Создано';
$string['datacurso_custom'] = 'Форум ИИ Datacurso';
$string['default_reply_message'] = 'Отвечайте с эмпатией и мотивацией';
$string['discussion'] = 'Обсуждение';
$string['discussion_label'] = 'Обсуждение: {$a}';
$string['discussioninfo'] = 'Информация об обсуждении';
$string['discussionmsg'] = 'Сообщение, сгенерированное ИИ';
$string['discussionname'] = 'Тема';
$string['enabled'] = 'Включить ИИ';
$string['err_table_missing'] = 'Таблица конфигурации для форума ИИ не существует. Пожалуйста, обновите плагин через Администрирование сайта > Уведомления.';
$string['error_airequest'] = 'Ошибка при связи со службой ИИ: {$a}';
$string['error_saving'] = 'Ошибка при сохранении конфигурации: {$a}';
$string['forum'] = 'Форум';
$string['forumname'] = 'Форум';
$string['goto_notifications'] = 'Перейти к уведомлениям';
$string['historyresponses'] = 'История ответов форума ИИ';
$string['invalidaction'] = 'Указанное действие недопустимо.';
$string['level'] = 'Уровень: {$a}';
$string['messageprovider:ai_approval_request'] = 'Запрос на одобрение ИИ';
$string['modal_title'] = 'Подробности истории обсуждения';
$string['modal_title_pending'] = 'Подробности обсуждения';
$string['no'] = 'Нет';
$string['no_posts'] = 'В этом обсуждении сообщений не найдено.';
$string['nohistory'] = 'Нет истории одобренных или отклонённых ответов ИИ.';
$string['nopermission'] = 'У вас нет прав для одобрения или отклонения ответов ИИ.';
$string['noresponses'] = 'Нет ожидающих одобрения ответов.';
$string['noteachersfound'] = 'Для этого курса преподаватели не найдены.';
$string['notification_approve_link'] = 'Одобрить напрямую: {$a->url}';
$string['notification_course_label'] = 'Курс';
$string['notification_greeting'] = 'Здравствуйте, {$a->firstname},';
$string['notification_intro'] = 'Автоматический ответ был сгенерирован для обсуждения "{$a->discussion}" на форуме "{$a->forum}" курса "{$a->course}".';
$string['notification_preview'] = 'Предпросмотр:';
$string['notification_reject_link'] = 'Отклонить: {$a->url}';
$string['notification_review_button'] = 'Проверить ответ';
$string['notification_review_link'] = 'Проверьте и одобрите ответ по ссылке: {$a->url}';
$string['notification_smallmessage'] = 'Новый ответ ИИ ожидает в "{$a->discussion}"';
$string['notification_subject'] = 'Требуется одобрение: ответ ИИ';
$string['originalmessage'] = 'Оригинальное сообщение';
$string['pendingresponses'] = 'Ожидающие ответы форума ИИ';
$string['pluginname'] = 'Форум ИИ';
$string['preview'] = 'Сообщение ИИ';
$string['privacy:metadata:local_forum_ai_config'] = 'Хранит настройки ИИ по каждому форуму.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Указывает, активирован ли ИИ для этого форума.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'ID форума, к которому относится эта настройка.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Шаблон ответа, сгенерированный ИИ.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Указывает, требуют ли ответы ИИ одобрения перед публикацией.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Дата создания конфигурации.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Дата последнего изменения конфигурации.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Данные, сохранённые плагином форума ИИ.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Токен одобрения, связанный с публикацией.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Дата, когда ответ был одобрен.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID пользователя, создавшего публикацию.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID связанного обсуждения.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID форума, где был создан ответ.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Сообщение, созданное искусственным интеллектом.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Статус публикации (одобрена, ожидает, отклонена).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Тема сообщения.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Дата создания записи.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Дата обновления записи.';
$string['reject'] = 'Отклонить';
$string['reply_message'] = 'Дайте указания ИИ';
$string['replylevel'] = 'Уровень ответа {$a}';
$string['require_approval'] = 'Проверить ответ ИИ';
$string['response_approved'] = 'Ответ ИИ успешно одобрен и опубликован.';
$string['response_rejected'] = 'Ответ ИИ отклонён.';
$string['response_update_failed'] = 'Не удалось обновить ответ.';
$string['response_updated'] = 'Ответ успешно обновлён.';
$string['reviewtitle'] = 'Проверка ответа ИИ';
$string['save'] = 'Сохранить';
$string['saveapprove'] = 'Сохранить и одобрить';
$string['settings'] = 'Настройки для: ';
$string['settings_forum'] = 'Настройки для {$a}';
$string['status'] = 'Статус';
$string['statusapproved'] = 'Одобрено';
$string['statuspending'] = 'Ожидает';
$string['statusrejected'] = 'Отклонено';
$string['username'] = 'Автор';
$string['viewdetails'] = 'Подробнее';
$string['yes'] = 'Да';
