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

$string['actions'] = 'Tindakan';
$string['ai_response'] = 'Respons AI';
$string['ai_response_approved'] = 'Respons AI Disetujui';
$string['ai_response_proposed'] = 'Respons AI yang Diusulkan';
$string['ai_response_rejected'] = 'Respons AI Ditolak';
$string['aiproposed'] = 'Respons AI yang Diusulkan';
$string['alreadysubmitted'] = 'Permintaan ini sudah disetujui, ditolak atau tidak ada.';
$string['approve'] = 'Setujui';
$string['backtodiscussion'] = 'Kembali ke diskusi';
$string['cancel'] = 'Batal';
$string['col_message'] = 'Pesan';
$string['course'] = 'Kursus';
$string['coursename'] = 'Kursus';
$string['created'] = 'Dibuat';
$string['datacurso_custom'] = 'Datacurso Forum AI';
$string['default_reply_message'] = 'Balas dengan nada empatik dan memotivasi';
$string['discussion'] = 'Diskusi';
$string['discussion_label'] = 'Diskusi: {$a}';
$string['discussioninfo'] = 'Informasi diskusi';
$string['discussionmsg'] = 'Pesan yang dibuat oleh AI';
$string['discussionname'] = 'Subjek';
$string['enabled'] = 'Aktifkan AI';
$string['forum'] = 'Forum';
$string['forumname'] = 'Forum';
$string['historyresponses'] = 'Riwayat respons Forum AI';
$string['level'] = 'Tingkat: {$a}';
$string['messageprovider:ai_approval_request'] = 'Permintaan persetujuan AI';
$string['modal_title'] = 'Detail riwayat diskusi';
$string['modal_title_pending'] = 'Detail diskusi';
$string['no'] = 'Tidak';
$string['no_posts'] = 'Tidak ditemukan postingan dalam diskusi ini.';
$string['nohistory'] = 'Tidak ada riwayat respons AI yang disetujui atau ditolak.';
$string['nopermission'] = 'Anda tidak memiliki izin untuk menyetujui/menolak respons AI.';
$string['noresponses'] = 'Tidak ada respons yang menunggu persetujuan.';
$string['notification_approve_link'] = 'Setujui langsung: {$a->url}';
$string['notification_course_label'] = 'Kursus';
$string['notification_greeting'] = 'Halo {$a->firstname},';
$string['notification_intro'] = 'Sebuah respons otomatis telah dibuat untuk diskusi “{$a->discussion}” di forum “{$a->forum}” dari kursus “{$a->course}”.';
$string['notification_preview'] = 'Pratinjau:';
$string['notification_reject_link'] = 'Tolak: {$a->url}';
$string['notification_review_button'] = 'Tinjau respons';
$string['notification_review_link'] = 'Tinjau dan setujui respons di: {$a->url}';
$string['notification_smallmessage'] = 'Respons AI baru menunggu di “{$a->discussion}”';
$string['notification_subject'] = 'Persetujuan diperlukan: Respons AI';
$string['originalmessage'] = 'Pesan asli';
$string['pendingresponses'] = 'Respons Forum AI Menunggu';
$string['pluginname'] = 'Forum AI';
$string['preview'] = 'Pesan AI';
$string['privacy:metadata:local_forum_ai_config'] = 'Menyimpan konfigurasi AI per forum.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Menunjukkan apakah AI diaktifkan untuk forum ini.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'ID forum yang terkait dengan konfigurasi ini.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Templat respons yang dihasilkan oleh AI.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Menunjukkan apakah respons AI memerlukan persetujuan sebelum dipublikasikan.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Tanggal konfigurasi dibuat.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Tanggal terakhir konfigurasi diubah.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Data yang disimpan oleh plugin Forum AI.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Token persetujuan yang terkait dengan publikasi.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Tanggal ketika respons disetujui.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID pengguna yang membuat publikasi.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID diskusi terkait.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID forum tempat respons dibuat.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Pesan yang dibuat oleh AI.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Status publikasi (disetujui, menunggu atau ditolak).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Subjek atau tema pesan.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Tanggal entri dibuat.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Tanggal entri terakhir diubah.';
$string['reject'] = 'Tolak';
$string['reply_message'] = 'Berikan instruksi ke AI';
$string['replylevel'] = 'Respons tingkat {$a}';
$string['require_approval'] = 'Periksa respons AI';
$string['reviewtitle'] = 'Tinjau respons AI';
$string['save'] = 'Simpan';
$string['saveapprove'] = 'Simpan dan setujui';
$string['settings'] = 'Pengaturan untuk: ';
$string['status'] = 'Status';
$string['statusapproved'] = 'Disetujui';
$string['statuspending'] = 'Menunggu';
$string['statusrejected'] = 'Ditolak';
$string['username'] = 'Pembuat';
$string['viewdetails'] = 'Detail';
$string['yes'] = 'Ya';
