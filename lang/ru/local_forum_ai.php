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
$string['ai_response_approved'] = 'Ответ ИИ одобрен';
$string['ai_response_proposed'] = 'Предложенный ответ ИИ';
$string['ai_response_rejected'] = 'Ответ ИИ отклонён';
$string['aiproposed'] = 'Предложенный ответ ИИ';
$string['alreadysubmitted'] = 'Этот запрос уже был одобрен, отклонён или не существует.';
$string['approve'] = 'Одобрить';
$string['backtodiscussion'] = 'Вернуться к обсуждению';
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
$string['forum'] = 'Форум';
$string['forumname'] = 'Форум';
$string['historyresponses'] = 'История ответов Форум ИИ';
$string['level'] = 'Уровень: {$a}';
$string['modal_title'] = 'Детали истории обсуждения';
$string['modal_title_pending'] = 'Детали обсуждения';
$string['no'] = 'Нет';
$string['no_posts'] = 'В этом обсуждении сообщения не найдены.';
$string['nohistory'] = 'Нет истории одобренных или отклонённых ответов ИИ.';
$string['nopermission'] = 'У вас нет прав на одобрение/отклонение ответов ИИ.';
$string['noresponses'] = 'Нет ответов, ожидающих одобрения.';
$string['originalmessage'] = 'Оригинальное сообщение';
$string['pendingresponses'] = 'Ожидающие ответы Форум ИИ';
$string['pluginname'] = 'Форум ИИ';
$string['preview'] = 'Сообщение ИИ';
$string['privacy:metadata:local_forum_ai_config'] = 'Хранит настройки ИИ для каждого форума.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Указывает, включён ли ИИ для этого форума.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'ID форума, к которому относится эта конфигурация.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Шаблон ответа, создаваемого ИИ.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Указывает, требуется ли одобрение ИИ-ответов перед публикацией.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Дата создания конфигурации.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Дата последнего изменения конфигурации.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Данные, хранимые плагином Форум ИИ.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Токен одобрения, связанный с публикацией.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Дата, когда ответ был одобрен.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID пользователя, создавшего публикацию.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID связанного обсуждения.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID форума, где был сгенерирован ответ.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Сообщение, сгенерированное искусственным интеллектом.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Статус публикации (одобрен, в ожидании или отклонён).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Тема или предмет сообщения.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Дата создания записи.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Дата обновления записи.';
$string['reject'] = 'Отклонить';
$string['reply_message'] = 'Дайте указания ИИ';
$string['replylevel'] = 'Уровень ответа {$a}';
$string['require_approval'] = 'Требовать проверки ИИ';
$string['reviewtitle'] = 'Проверить ответ ИИ';
$string['save'] = 'Сохранить';
$string['saveapprove'] = 'Сохранить и одобрить';
$string['settings'] = 'Настройки для: ';
$string['status'] = 'Статус';
$string['statusapproved'] = 'Одобрен';
$string['statuspending'] = 'В ожидании';
$string['statusrejected'] = 'Отклонён';
$string['username'] = 'Создатель';
$string['viewdetails'] = 'Подробнее';
$string['yes'] = 'Да';
