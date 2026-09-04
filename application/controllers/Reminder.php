<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reminder extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Meeting_model');
        $this->load->model('Meeting_participant_model');
        $this->load->model('Crew_model');
        $this->load->model('Rig_model');
        $this->load->model('Reminder_model');
        $this->load->model('Setting_model');
        $this->load->library('whatsapp_service');
    }

    public function index()
    {
        $meetings = $this->Meeting_model->get_meetings_detailed('scheduled');
        if (empty($meetings)) {
            $meetings = $this->Meeting_model->get_meetings_detailed();
        }

        $selected_meeting_id = $this->input->get('meeting_id', TRUE);
        if (!$selected_meeting_id && !empty($meetings)) {
            $selected_meeting_id = $meetings[0]['id'];
        }

        $selected_meeting = null;
        $participants = array();
        $recent_logs = array();
        if ($selected_meeting_id) {
            $selected_meeting = $this->Meeting_model->get_meeting_detail((int)$selected_meeting_id);
            $participants = $this->Meeting_participant_model->get_participants_by_meeting((int)$selected_meeting_id);
            $recent_logs = $this->Reminder_model->get_logs((int)$selected_meeting_id, 15);
        }

        $sender_number = $this->Setting_model->get_val('wa_sender_number', '');

        $data = array(
            'title'           => 'Undangan & Broadcast WhatsApp - Besmindo Reminder',
            'meetings'        => $meetings,
            'selectedMeeting' => $selected_meeting,
            'participants'    => $participants,
            'senderNumber'    => $sender_number,
            'recentLogs'      => $recent_logs
        );

        $this->render_template('reminder/index', $data);
    }

    public function broadcast($meeting_id)
    {
        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('reminder');
        }

        $participants = $this->Meeting_participant_model->get_participants_by_meeting($meeting_id);
        $meetings = $this->Meeting_model->get_meetings_detailed();
        $recent_logs = $this->Reminder_model->get_logs((int)$meeting_id, 15);
        $sender_number = $this->Setting_model->get_val('wa_sender_number', '');

        $data = array(
            'title'           => 'Broadcast Undangan WA: ' . $meeting['title'],
            'meetings'        => $meetings,
            'selectedMeeting' => $meeting,
            'participants'    => $participants,
            'senderNumber'    => $sender_number,
            'recentLogs'      => $recent_logs
        );

        $this->render_template('reminder/index', $data);
    }

    public function send()
    {
        $meeting_id        = (int)$this->input->post('meeting_id', TRUE);
        $reminder_type     = $this->input->post('reminder_type', TRUE) ?: 'UNDANGAN';
        $target_mode       = $this->input->post('target_mode', TRUE) ?: 'GROUP'; // GROUP, PERSONAL, BOTH
        $selected_crew_ids = $this->input->post('crew_ids') ?: array();
        $custom_template   = $this->input->post('message_template', TRUE);

        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Data meeting tidak valid.');
            redirect('reminder');
        }

        $participants = $this->Meeting_participant_model->get_participants_by_meeting($meeting_id);
        if (empty($selected_crew_ids)) {
            $selected_crew_ids = array_column($participants, 'crew_id');
        }

        $this->db->where_in('id', $selected_crew_ids);
        $crews = !empty($selected_crew_ids) ? $this->db->get('crews')->result_array() : array();

        $groupSuccess = false;
        $personalSuccessCount = 0;

        // 1. KIRIM KE WHATSAPP GROUP RIG
        if ($target_mode === 'GROUP' || $target_mode === 'BOTH') {
            if (!empty($meeting['wa_group_id'])) {
                $groupMsg = $this->build_group_message($reminder_type, $meeting, $crews);
                $resGroup = $this->whatsapp_service->send_to_group($meeting['wa_group_id'], $groupMsg);

                $this->Reminder_model->insert(array(
                    'meeting_id'    => $meeting_id,
                    'crew_id'       => NULL,
                    'reminder_type' => 'GROUP_' . $reminder_type,
                    'target_phone'  => $meeting['wa_group_id'],
                    'message_body'  => $groupMsg,
                    'status'        => (!empty($resGroup['success'])) ? 'sent' : 'failed',
                    'sent_at'       => date('Y-m-d H:i:s')
                ));

                if (!empty($resGroup['success'])) {
                    $groupSuccess = true;
                }
            } else {
                if ($target_mode === 'GROUP') {
                    $this->session->set_flashdata('error', "Unit Rig [{$meeting['rig_name']}] belum memiliki ID WhatsApp Group. Silakan atur ID Grup di menu Master Data Rig.");
                    redirect('reminder?meeting_id=' . $meeting_id);
                }
            }
        }

        // 2. KIRIM PERSONAL KE MASING-MASING CREW (ANTRIAN AMAN ANTI-BAN)
        if (($target_mode === 'PERSONAL' || $target_mode === 'BOTH') && !empty($crews)) {
            $batchData = array();
            foreach ($crews as $c) {
                $message = $this->build_message($custom_template, $reminder_type, $meeting, $c);
                $batchData[] = array(
                    'target'        => $c['phone'],
                    'message'       => $message,
                    'crew_id'       => $c['id'],
                    'recipientName' => $c['name']
                );
            }

            $bulkResult = $this->whatsapp_service->send_bulk($batchData);
            $logStatus = (!empty($bulkResult['success'])) ? 'sent' : 'failed';

            foreach ($batchData as $item) {
                $this->Reminder_model->insert(array(
                    'meeting_id'    => $meeting_id,
                    'crew_id'       => $item['crew_id'],
                    'reminder_type' => $reminder_type,
                    'target_phone'  => $item['target'],
                    'message_body'  => $item['message'],
                    'status'        => $logStatus,
                    'sent_at'       => date('Y-m-d H:i:s')
                ));
            }

            if (!empty($bulkResult['success'])) {
                $personalSuccessCount = count($crews);
            }
        }

        // Feedback
        if ($groupSuccess && $personalSuccessCount > 0) {
            $this->session->set_flashdata('success', "Sukses! Broadcast terkirim ke WhatsApp Group Rig [{$meeting['rig_name']}], dan {$personalSuccessCount} pesan personal telah masuk Antrian Aman Anti-Ban (jeda 15–35 detik/orang).");
        } elseif ($groupSuccess) {
            $this->session->set_flashdata('success', "Sukses! Pesan berhasil terkirim ke WhatsApp Group Rig [{$meeting['rig_name']}].");
        } elseif ($personalSuccessCount > 0) {
            $this->session->set_flashdata('success', "Sukses! {$personalSuccessCount} pesan personal telah masuk ke Antrian Aman Anti-Ban. Pesan dikirim bertahap dengan jeda acak 15–35 detik agar nomor aman dari pemblokiran.");
        } else {
            $this->session->set_flashdata('error', 'Gagal memproses pesan broadcast. Pastikan Service Gateway Mandiri (node server.js) aktif dan WhatsApp sudah terhubung.');
        }

        redirect('reminder?meeting_id=' . $meeting_id);
    }

    public function gateway()
    {
        $sender_number = $this->Setting_model->get_val('wa_sender_number', '085148410891');
        $api_url       = $this->Setting_model->get_val('wa_api_url', 'http://localhost:3000/send-message');

        $data = array(
            'title'        => 'Pusat Layanan WhatsApp Gateway Mandiri (085148410891)',
            'senderNumber' => $sender_number,
            'apiUrl'       => $api_url
        );

        $this->render_template('reminder/gateway', $data);
    }

    public function save_gateway_settings()
    {
        $sender_number = trim($this->input->post('wa_sender_number', TRUE)) ?: '085148410891';
        $api_url       = trim($this->input->post('wa_api_url', TRUE)) ?: 'http://localhost:3000/send-message';

        $this->Setting_model->set_val('wa_gateway_provider', 'LOCAL_NODE');
        $this->Setting_model->set_val('wa_sender_number', $sender_number);
        $this->Setting_model->set_val('wa_api_url', $api_url);

        $this->session->set_flashdata('success', 'Pengaturan Gateway Mandiri berhasil disimpan!');
        redirect('reminder/gateway');
    }

    public function test_send()
    {
        $target_phone = trim($this->input->post('target_phone', TRUE));
        $message      = trim($this->input->post('message', TRUE));

        if (empty($target_phone) || empty($message)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Nomor tujuan dan pesan wajib diisi.'
                )));
        }

        $res = $this->whatsapp_service->send_message($target_phone, $message);
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($res));
    }

    public function log()
    {
        $meeting_id = $this->input->get('meeting_id', TRUE);
        $logs = $this->Reminder_model->get_logs($meeting_id ? (int)$meeting_id : null, 150);
        $meetings = $this->Meeting_model->get_meetings_detailed();

        $data = array(
            'title'             => 'Log Outbox WhatsApp Reminder - Besmindo Reminder',
            'logs'              => $logs,
            'meetings'          => $meetings,
            'selectedMeetingId' => $meeting_id
        );

        $this->render_template('reminder/log', $data);
    }

    private function build_group_message($type, $meeting, $crews)
    {
        $tanggal = date('d M Y', strtotime($meeting['meeting_date']));
        $jam     = substr($meeting['start_time'], 0, 5) . ' - ' . substr($meeting['end_time'], 0, 5) . ' WIB';
        $pj      = !empty($meeting['pj_name']) ? $meeting['pj_name'] : 'Management Besmindo';
        $topik   = !empty($meeting['topic'])   ? $meeting['topic']   : 'Operasional Rutin Rig';

        $crewListStr = "";
        if (!empty($crews)) {
            $crewListStr = "\n👥 *Daftar Personil Diundang:*\n";
            $no = 1;
            foreach ($crews as $c) {
                $crewListStr .= "{$no}. *{$c['name']}* ({$c['position']})\n";
                $no++;
            }
        }

        switch ($type) {
            case 'H-1':
                return "*[REMINDER H-1 MEETING CREW RIG]*\n"
                    . "🏢 *Unit Rig:* {$meeting['rig_name']}\n\n"
                    . "Pemberitahuan kepada seluruh personil crew rig bahwa besok akan diselenggarakan pertemuan operasional:\n\n"
                    . "📋 *Agenda:* {$meeting['title']}\n"
                    . "📝 *Topik:* {$topik}\n"
                    . "📅 *Hari/Tgl:* {$tanggal}\n"
                    . "⏰ *Waktu:* {$jam}\n"
                    . "👤 *PJ Rig:* {$pj}\n"
                    . "{$crewListStr}\n"
                    . "🔗 *Link Microsoft Teams:* \n{$meeting['teams_link']}\n\n"
                    . "Harap seluruh personil mempersiapkan laporan harian dan hadir tepat waktu.\n"
                    . "_Management PT. Besmindo Materi Sewatama_";

            case 'H-1_JAM':
                return "*[PENGINGAT 1 JAM SEBELUM MEETING]*\n"
                    . "🏢 *Unit Rig:* {$meeting['rig_name']}\n\n"
                    . "Pertemuan operasional *{$meeting['title']}* akan dimulai dalam *1 jam* (Pukul " . substr($meeting['start_time'], 0, 5) . " WIB).\n\n"
                    . "Pastikan perangkat Microsoft Teams Anda siap:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "_PT. Besmindo Materi Sewatama_";

            case 'H-15_MIN':
                return "*[PANGGILAN BERGABUNG - 15 MENIT LAGI]*\n"
                    . "🏢 *Unit Rig:* {$meeting['rig_name']}\n\n"
                    . "Ruang meeting Microsoft Teams untuk *{$meeting['title']}* telah dibuka.\n"
                    . "Seluruh personil crew dimohon segera bergabung:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "Kehadiran Anda dicatat otomatis oleh sistem.";

            default:
                return "*[UNDANGAN RESMI OPERASIONAL MEETING]*\n"
                    . "🏢 *Unit Rig:* {$meeting['rig_name']}\n\n"
                    . "Kepada seluruh rekan crew unit {$meeting['rig_name']}, Anda diundang untuk menghadiri pertemuan rutin:\n\n"
                    . "📋 *Agenda:* {$meeting['title']}\n"
                    . "📝 *Topik:* {$topik}\n"
                    . "📅 *Tanggal:* {$tanggal}\n"
                    . "⏰ *Waktu:* {$jam}\n"
                    . "👤 *PJ Rig:* {$pj}\n"
                    . "{$crewListStr}\n"
                    . "🔗 *Tautan Microsoft Teams:* \n{$meeting['teams_link']}\n\n"
                    . "Harap seluruh personil hadir 5 menit sebelum meeting dimulai.\n"
                    . "_Management PT. Besmindo Materi Sewatama_";
        }
    }

    private function build_message($template, $type, $meeting, $crew)
    {
        $tanggal = date('d M Y', strtotime($meeting['meeting_date']));
        $jam     = substr($meeting['start_time'], 0, 5) . ' - ' . substr($meeting['end_time'], 0, 5) . ' WIB';
        $pj      = !empty($meeting['pj_name']) ? $meeting['pj_name'] : 'Management Besmindo';
        $topik   = !empty($meeting['topic'])   ? $meeting['topic']   : 'Operasional Rutin Rig';

        // Deteksi salam waktu (Pagi, Siang, Sore, Malam)
        $hour = (int)date('H');
        if ($hour >= 4 && $hour < 11) {
            $salam = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $salam = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $salam = 'Selamat Sore';
        } else {
            $salam = 'Selamat Malam';
        }

        // Variasi pembuka pesan personal (Anti-Ban Pattern)
        $crewName = !empty($crew['name']) ? $crew['name'] : 'Rekan Crew';
        $crewPos  = !empty($crew['position']) ? $crew['position'] : 'Crew Rig';

        $salutations = array(
            "{$salam} Bapak/Rekan *{$crewName}* ({$crewPos}),",
            "Halo Rekan *{$crewName}* ({$crewPos}), semoga sehat selalu,",
            "Yth. Bapak/Rekan *{$crewName}* ({$crewPos}) — Unit Rig {$meeting['rig_name']},"
        );
        $opener = $salutations[array_rand($salutations)];

        // Unique reference hash per pesan (agar hash teks berbeda 100%, mencegah flag spam WhatsApp)
        $refCode = 'BSM-' . strtoupper(substr(md5($crew['id'] . $meeting['id'] . microtime()), 0, 6));

        // Jika user memasukkan template custom di form
        if (!empty($template)) {
            $customText = str_replace(
                array('{nama}', '{posisi}', '{rig}', '{agenda}', '{topik}', '{tanggal}', '{waktu}', '{link}', '{ref}'),
                array($crewName, $crewPos, $meeting['rig_name'], $meeting['title'], $topik, $tanggal, $jam, $meeting['teams_link'], $refCode),
                $template
            );
            if (strpos($customText, $crewName) === false) {
                $customText = "{$opener}\n\n" . $customText;
            }
            if (strpos($customText, $refCode) === false) {
                $customText .= "\n\n_(Ref: #{$refCode})_";
            }
            return $customText;
        }

        switch ($type) {
            case 'H-1':
                return "*[REMINDER H-1 MEETING CREW RIG BESMINDO]*\n\n"
                    . "{$opener}\n"
                    . "Mengingatkan bahwa besok akan diselenggarakan pertemuan operasional crew:\n\n"
                    . "📌 *Agenda:* {$meeting['title']}\n"
                    . "🏢 *Unit Rig:* {$meeting['rig_name']}\n"
                    . "📝 *Topik:* {$topik}\n"
                    . "📅 *Hari/Tgl:* {$tanggal}\n"
                    . "⏰ *Waktu:* {$jam}\n"
                    . "👤 *PJ Rig:* {$pj}\n\n"
                    . "🔗 *Link Microsoft Teams:* \n{$meeting['teams_link']}\n\n"
                    . "Mohon mempersiapkan laporan harian operasional dan hadir tepat waktu.\n"
                    . "_Management PT. Besmindo Materi Sewatama_\n"
                    . "_(Ref: #{$refCode})_";

            case 'H-1_JAM':
                return "*[PENGINGAT 1 JAM SEBELUM MEETING]*\n\n"
                    . "{$opener}\n"
                    . "Meeting operasional *{$meeting['title']}* untuk Unit Rig *{$meeting['rig_name']}* akan dimulai dalam *1 jam* (Pukul " . substr($meeting['start_time'], 0, 5) . " WIB).\n\n"
                    . "Pastikan koneksi internet dan perangkat Microsoft Teams Anda siap:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "Kehadiran Bapak/Rekan *{$crewName}* sangat diharapkan tepat waktu.\n"
                    . "_PT. Besmindo Materi Sewatama_\n"
                    . "_(Ref: #{$refCode})_";

            case 'H-15_MIN':
                return "*[PANGGILAN BERGABUNG – 15 MENIT LAGI]*\n\n"
                    . "{$opener}\n"
                    . "Ruang meeting Microsoft Teams untuk *{$meeting['title']}* – Rig *{$meeting['rig_name']}* telah dibuka.\n\n"
                    . "Mohon segera bergabung:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "Kehadiran Bapak/Rekan dicatat otomatis oleh sistem.\n"
                    . "_Management PT. Besmindo Materi Sewatama_\n"
                    . "_(Ref: #{$refCode})_";

            default: // UNDANGAN
                return "*[UNDANGAN RESMI OPERASIONAL MEETING]*\n\n"
                    . "{$opener}\n\n"
                    . "Bapak/Rekan diundang untuk menghadiri pertemuan rutin operasional Rig *{$meeting['rig_name']}*:\n\n"
                    . "📋 *Agenda:* {$meeting['title']}\n"
                    . "📝 *Topik:* {$topik}\n"
                    . "📅 *Tanggal:* {$tanggal}\n"
                    . "⏰ *Waktu:* {$jam}\n"
                    . "👤 *PJ Rig:* {$pj}\n\n"
                    . "🔗 *Tautan Microsoft Teams:* \n{$meeting['teams_link']}\n\n"
                    . "Harap konfirmasi kehadiran dan hadir 5 menit sebelum meeting dimulai.\n"
                    . "_Management PT. Besmindo Materi Sewatama_\n"
                    . "_(Ref: #{$refCode})_";
        }
    }
}
