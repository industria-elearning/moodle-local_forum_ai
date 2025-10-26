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

$string['actions'] = 'Действия';
$string['ai_response'] = 'Ответ ИИ';
$string['ai_response_approved'] = 'Ответ ИИ утверждён';
$string['ai_response_proposed'] = 'Предложенный ответ ИИ';
$string['ai_response_rejected'] = 'Ответ ИИ отклонён';
$string['aiproposed'] = 'Предложенный ответ ИИ';
$string['alreadysubmitted'] = 'Этот запрос уже был утверждён, отклонён или не существует.';
$string['approve'] = 'Утвердить';
$string['backtodiscussion'] = 'Назад к обсуждению';
$string['cancel'] = 'Отмена';
$string['col_message'] = 'Сообщение';
$string['course'] = 'Курс';
$string['coursename'] = 'Курс';
$string['created'] = 'Создано';
$string['datacurso_custom'] = 'Datacurso Форум ИИ';
$string['default_reply_message'] = 'Ответьте с эмпатичным и мотивирующим тоном';
$string['discussion'] = 'Обсуждение';
$string['discussion_label'] = 'Обсуждение: {$a}';
$string['discussioninfo'] = 'Информация об обсуждении';
$string['discussionmsg'] = 'Сообщение, созданное ИИ';
$string['discussionname'] = 'Тема';
$string['enabled'] = 'Включить ИИ';
$string['error_airequest'] = 'Ошибка при обращении к сервису ИИ: {$a}';
$string['forum'] = 'Форум';
$string['forumname'] = 'Форум';
$string['historyresponses'] = 'История ответов ИИ на форуме';
$string['level'] = 'Уровень: {$a}';
$string['messageprovider:ai_approval_request'] = 'Запрос на утверждение ИИ';
$string['modal_title'] = 'Детали истории обсуждения';
$string['modal_title_pending'] = 'Детали обсуждения';
$string['no'] = 'Нет';
$string['no_posts'] = 'В этом обсуждении не найдено сообщений.';
$string['nohistory'] = 'Нет истории утверждённых или отклонённых ответов ИИ.';
$string['nopermission'] = 'У вас нет прав утверждать/отклонять ответы ИИ.';
$string['noresponses'] = 'Нет ответов, ожидающих утверждения.';
$string['notification_approve_link'] = 'Утвердить сразу: {$a->url}';
$string['notification_course_label'] = 'Курс';
$string['notification_greeting'] = 'Здравствуйте, {$a->firstname},';
$string['notification_intro'] = 'Для обсуждения «{$a->discussion}» на форуме «{$a->forum}» курса «{$a->course}» сгенерирован автоматический ответ.';
$string['notification_preview'] = 'Предварительный просмотр:';
$string['notification_reject_link'] = 'Отклонить: {$a->url}';
$string['notification_review_button'] = 'Проверить ответ';
$string['notification_review_link'] = 'Проверьте и утвердите ответ по адресу: {$a->url}';
$string['notification_smallmessage'] = 'Новый ответ ИИ ожидает в «{$a->discussion}»';
$string['notification_subject'] = 'Требуется утверждение: ответ ИИ';
$string['originalmessage'] = 'Оригинальное сообщение';
$string['pendingresponses'] = 'Ожидающие ответы Форум ИИ';
$string['pluginname'] = 'Форум ИИ';
$string['preview'] = 'Сообщение ИИ';
$string['privacy:metadata:local_forum_ai_config'] = 'Хранит настройки ИИ для каждого форума.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Указывает, включена ли ИИ для данного форума.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'ID форума, к которому относится эта настройка.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Шаблон ответа, сгенерированный ИИ.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Указывает, требуют ли ответы ИИ утверждения перед публикацией.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Дата создания настройки.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Дата последнего изменения настройки.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Данные, сохраняемые плагином Форум ИИ.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Токен утверждения, связанный с публикацией.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Дата утверждения ответа.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID пользователя, создавшего публикацию.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID связанной дискуссии.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID форума, на котором создан ответ.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Сообщение, созданное ИИ.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Статус публикации (утверждён, ожидает или отклонён).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Тема или заголовок сообщения.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Дата создания записи.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Дата последнего изменения записи.';
$string['reject'] = 'Отклонить';
$string['reply_message'] = 'Дайте указания ИИ';
$string['replylevel'] = 'Ответ уровень {$a}';
$string['require_approval'] = 'Проверка ответа ИИ';
$string['reviewtitle'] = 'Проверка ответа ИИ';
$string['save'] = 'Сохранить';
$string['saveapprove'] = 'Сохранить и утвердить';
$string['settings'] = 'Настройки для: ';
$string['status'] = 'Статус';
$string['statusapproved'] = 'Утверждён';
$string['statuspending'] = 'Ожидает';
$string['statusrejected'] = 'Отклонён';
$string['username'] = 'Создатель';
$string['viewdetails'] = 'Подробности';
$string['yes'] = 'Да';
