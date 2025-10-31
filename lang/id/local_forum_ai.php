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

$string['action_failed'] = 'Tindakan tidak dapat diproses.';
$string['actions'] = 'Tindakan';
$string['ai_response'] = 'Respon AI';
$string['ai_response_approved'] = 'Respon AI Disetujui';
$string['ai_response_proposed'] = 'Respon AI yang Diajukan';
$string['ai_response_rejected'] = 'Respon AI Ditolak';
$string['aiproposed'] = 'Respon AI yang Diajukan';
$string['alreadysubmitted'] = 'Permintaan ini sudah disetujui, ditolak, atau tidak ada.';
$string['approve'] = 'Setujui';
$string['backtodiscussion'] = 'Kembali ke diskusi';
$string['backup:includeai'] = 'Sertakan data forum AI dalam cadangan';
$string['cancel'] = 'Batal';
$string['col_message'] = 'Pesan';
$string['config_created'] = 'Konfigurasi berhasil dibuat.';
$string['config_updated'] = 'Konfigurasi berhasil diperbarui.';
$string['course'] = 'Kursus';
$string['coursename'] = 'Kursus';
$string['created'] = 'Dibuat';
$string['datacurso_custom'] = 'Forum AI Datacurso';
$string['default_reply_message'] = 'Balas dengan nada empatik dan memotivasi';
$string['discussion'] = 'Diskusi';
$string['discussion_label'] = 'Diskusi: {$a}';
$string['discussioninfo'] = 'Informasi diskusi';
$string['discussionmsg'] = 'Pesan yang dihasilkan AI';
$string['discussionname'] = 'Subjek';
$string['enabled'] = 'Aktifkan AI';
$string['err_table_missing'] = 'Tabel konfigurasi untuk Forum AI tidak ada. Silakan perbarui plugin dari Administrasi Situs > Notifikasi.';
$string['error_airequest'] = 'Kesalahan komunikasi dengan layanan AI: {$a}';
$string['error_saving'] = 'Kesalahan menyimpan konfigurasi: {$a}';
$string['forum'] = 'Forum';
$string['forumname'] = 'Forum';
$string['goto_notifications'] = 'Buka Notifikasi';
$string['historyresponses'] = 'Riwayat Respon Forum AI';
$string['invalidaction'] = 'Tindakan yang ditentukan tidak valid.';
$string['level'] = 'Tingkat: {$a}';
$string['messageprovider:ai_approval_request'] = 'Permintaan Persetujuan AI';
$string['modal_title'] = 'Detail Riwayat Diskusi';
$string['modal_title_pending'] = 'Detail Diskusi';
$string['no'] = 'Tidak';
$string['no_posts'] = 'Tidak ada posting dalam diskusi ini.';
$string['nohistory'] = 'Tidak ada riwayat respon AI yang disetujui atau ditolak.';
$string['nopermission'] = 'Anda tidak memiliki izin untuk menyetujui atau menolak respon AI.';
$string['noresponses'] = 'Tidak ada respon yang menunggu persetujuan.';
$string['noteachersfound'] = 'Tidak ada pengajar ditemukan untuk kursus ini.';
$string['notification_approve_link'] = 'Setujui langsung: {$a->url}';
$string['notification_course_label'] = 'Kursus';
$string['notification_greeting'] = 'Halo {$a->firstname},';
$string['notification_intro'] = 'Respon otomatis telah dihasilkan untuk diskusi "{$a->discussion}" di forum "{$a->forum}" dalam kursus "{$a->course}".';
$string['notification_preview'] = 'Pratinjau:';
$string['notification_reject_link'] = 'Tolak: {$a->url}';
$string['notification_review_button'] = 'Tinjau Respon';
$string['notification_review_link'] = 'Tinjau dan setujui respon di: {$a->url}';
$string['notification_smallmessage'] = 'Respon AI baru menunggu di "{$a->discussion}"';
$string['notification_subject'] = 'Persetujuan Diperlukan: Respon AI';
$string['originalmessage'] = 'Pesan asli';
$string['pendingresponses'] = 'Respon Forum AI Menunggu';
$string['pluginname'] = 'Forum AI';
$string['preview'] = 'Pesan AI';
$string['privacy:metadata:local_forum_ai_config'] = 'Menyimpan konfigurasi AI per forum.';
$string['privacy:metadata:local_forum_ai_config:enabled'] = 'Menunjukkan apakah AI diaktifkan untuk forum ini.';
$string['privacy:metadata:local_forum_ai_config:forumid'] = 'ID forum tempat konfigurasi ini berlaku.';
$string['privacy:metadata:local_forum_ai_config:reply_message'] = 'Template balasan yang dihasilkan AI.';
$string['privacy:metadata:local_forum_ai_config:require_approval'] = 'Menunjukkan apakah respon AI memerlukan persetujuan sebelum dipublikasikan.';
$string['privacy:metadata:local_forum_ai_config:timecreated'] = 'Tanggal pembuatan konfigurasi.';
$string['privacy:metadata:local_forum_ai_config:timemodified'] = 'Tanggal terakhir modifikasi konfigurasi.';
$string['privacy:metadata:local_forum_ai_pending'] = 'Data yang disimpan oleh plugin Forum AI.';
$string['privacy:metadata:local_forum_ai_pending:approval_token'] = 'Token persetujuan yang terkait dengan posting.';
$string['privacy:metadata:local_forum_ai_pending:approved_at'] = 'Tanggal respon disetujui.';
$string['privacy:metadata:local_forum_ai_pending:creator_userid'] = 'ID pengguna yang membuat posting.';
$string['privacy:metadata:local_forum_ai_pending:discussionid'] = 'ID diskusi terkait.';
$string['privacy:metadata:local_forum_ai_pending:forumid'] = 'ID forum tempat respon dihasilkan.';
$string['privacy:metadata:local_forum_ai_pending:message'] = 'Pesan yang dihasilkan oleh kecerdasan buatan.';
$string['privacy:metadata:local_forum_ai_pending:status'] = 'Status posting (disetujui, menunggu, atau ditolak).';
$string['privacy:metadata:local_forum_ai_pending:subject'] = 'Subjek pesan.';
$string['privacy:metadata:local_forum_ai_pending:timecreated'] = 'Tanggal pembuatan catatan.';
$string['privacy:metadata:local_forum_ai_pending:timemodified'] = 'Tanggal pembaruan catatan.';
$string['reject'] = 'Tolak';
$string['reply_message'] = 'Berikan instruksi kepada AI';
$string['replylevel'] = 'Tingkat balasan {$a}';
$string['require_approval'] = 'Tinjau Respon AI';
$string['response_approved'] = 'Respon AI berhasil disetujui dan dipublikasikan.';
$string['response_rejected'] = 'Respon AI ditolak.';
$string['response_update_failed'] = 'Gagal memperbarui respon.';
$string['response_updated'] = 'Respon berhasil diperbarui.';
$string['reviewtitle'] = 'Tinjau Respon AI';
$string['save'] = 'Simpan';
$string['saveapprove'] = 'Simpan dan Setujui';
$string['settings'] = 'Pengaturan untuk: ';
$string['settings_forum'] = 'Pengaturan untuk {$a}';
$string['status'] = 'Status';
$string['statusapproved'] = 'Disetujui';
$string['statuspending'] = 'Menunggu';
$string['statusrejected'] = 'Ditolak';
$string['username'] = 'Pembuat';
$string['viewdetails'] = 'Detail';
$string['yes'] = 'Ya';
