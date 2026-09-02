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
        if ($selected_meeting_id) {
            $selected_meeting = $this->Meeting_model->get_meeting_detail((int)$selected_meeting_id);
            $participants = $this->Meeting_participant_model->get_participants_by_meeting((int)$selected_meeting_id);
        }

        $sender_number = $this->Setting_model->get_val('wa_sender_number', '085148410891');
        $provider = $this->Setting_model->get_val('wa_gateway_provider', 'FONNTE');
        $api_token = $this->Setting_model->get_val('wa_api_token', '');

        $data = array(
            'title'           => 'Undangan & Broadcast WhatsApp - Besmindo Reminder',
            'meetings'        => $meetings,
            'selectedMeeting' => $selected_meeting,
            'participants'    => $participants,
            'senderNumber'    => $sender_number,
            'provider'        => $provider,
            'apiToken'        => $api_token
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
        $sender_number = $this->Setting_model->get_val('wa_sender_number', '085148410891');
        $provider = $this->Setting_model->get_val('wa_gateway_provider', 'FONNTE');
        $api_token = $this->Setting_model->get_val('wa_api_token', '');

        $data = array(
            'title'           => 'Broadcast Undangan WA: ' . $meeting['title'],
            'meetings'        => $meetings,
            'selectedMeeting' => $meeting,
            'participants'    => $participants,
            'senderNumber'    => $sender_number,
            'provider'        => $provider,
            'apiToken'        => $api_token
        );

        $this->render_template('reminder/index', $data);
    }

    public function send()
    {
        $meeting_id        = (int)$this->input->post('meeting_id', TRUE);
        $reminder_type     = $this->input->post('reminder_type', TRUE) ?: 'UNDANGAN';
        $selected_crew_ids = $this->input->post('crew_ids') ?: array();
        $custom_template   = $this->input->post('message_template', TRUE);

        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Data meeting tidak valid.');
            redirect('reminder');
        }

        if (empty($selected_crew_ids)) {
            $participants      = $this->Meeting_participant_model->get_participants_by_meeting($meeting_id);
            $selected_crew_ids = array_column($participants, 'crew_id');
        }

        $this->db->where_in('id', $selected_crew_ids);
        $crews = $this->db->get('crews')->result_array();

        if (empty($crews)) {
            $this->session->set_flashdata('error', 'Tidak ada personil crew yang dipilih.');
            redirect('reminder');
        }

        $provider = strtoupper($this->Setting_model->get_val('wa_gateway_provider', 'FONNTE'));

        // Prepare batch items
        $batchData = array();
        $delaySec = 1;

        foreach ($crews as $c) {
            $message = $this->build_message($custom_template, $reminder_type, $meeting, $c);

            $batchData[] = array(
                'target'  => $c['phone'],
                'message' => $message,
                'delay'   => (string)$delaySec,
                'crew_id' => $c['id']
            );

            // Progressive anti-ban delay (1s, 4s, 7s, 10s...)
            $delaySec += rand(2, 4);
        }

        // Send via Whatsapp Service (Bulk Batch)
        $bulkResult = $this->whatsapp_service->send_bulk($batchData);

        // Record logs
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
            $count = count($crews);
            $this->session->set_flashdata('success', "Berhasil mengirim {$count} pesan broadcast via Fonnte dengan jeda waktu otomatis anti-ban!");
        } else {
            $errMsg = !empty($bulkResult['message']) ? $bulkResult['message'] : 'Gagal mengirim pesan via WhatsApp Gateway.';
            $this->session->set_flashdata('error', $errMsg);
        }

        redirect('reminder/log?meeting_id=' . $meeting_id);
    }

    public function gateway()
    {
        $sender_number = $this->Setting_model->get_val('wa_sender_number', '085148410891');
        $provider      = $this->Setting_model->get_val('wa_gateway_provider', 'FONNTE');
        $api_url       = $this->Setting_model->get_val('wa_api_url', 'https://api.fonnte.com/send');
        $api_token     = $this->Setting_model->get_val('wa_api_token', '');

        $data = array(
            'title'        => 'Pusat Layanan WhatsApp Gateway & Fonnte API',
            'senderNumber' => $sender_number,
            'provider'     => $provider,
            'apiUrl'       => $api_url,
            'apiToken'     => $api_token
        );

        $this->render_template('reminder/gateway', $data);
    }

    public function save_gateway_settings()
    {
        $provider      = $this->input->post('wa_gateway_provider', TRUE) ?: 'FONNTE';
        $sender_number = trim($this->input->post('wa_sender_number', TRUE)) ?: '085148410891';
        $api_url       = trim($this->input->post('wa_api_url', TRUE));
        $api_token     = trim($this->input->post('wa_api_token', TRUE));

        if ($provider === 'FONNTE' && empty($api_url)) {
            $api_url = 'https://api.fonnte.com/send';
        }

        $this->Setting_model->set_val('wa_gateway_provider', $provider);
        $this->Setting_model->set_val('wa_sender_number', $sender_number);
        $this->Setting_model->set_val('wa_api_url', $api_url);
        $this->Setting_model->set_val('wa_api_token', $api_token);

        $this->session->set_flashdata('success', 'Pengaturan WhatsApp Gateway / Fonnte berhasil disimpan!');
        redirect('reminder/gateway');
    }

    public function fonnte_status()
    {
        $token = $this->input->get('token', TRUE) ?: $this->Setting_model->get_val('wa_api_token', '');
        $status = $this->whatsapp_service->check_fonnte_status($token);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($status));
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

        $res = $this->whatsapp_service->send_message($target_phone, $message, 0);
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

    private function build_message($template, $type, $meeting, $crew)
    {
        $tanggal = date('d M Y', strtotime($meeting['meeting_date']));
        $jam     = substr($meeting['start_time'], 0, 5) . ' - ' . substr($meeting['end_time'], 0, 5) . ' WIB';
        $pj      = !empty($meeting['pj_name']) ? $meeting['pj_name'] : 'Management Besmindo';
        $topik   = !empty($meeting['topic'])   ? $meeting['topic']   : 'Operasional Rutin Rig';

        switch ($type) {
            case 'H-1':
                return "*[REMINDER H-1 MEETING CREW RIG BESMINDO]*\n\n"
                    . "Halo Rekan *{$crew['name']}* ({$crew['position']}),\n"
                    . "Mengingatkan bahwa besok akan diadakan pertemuan operasional:\n\n"
                    . "📌 *Meeting:* {$meeting['title']}\n"
                    . "🏢 *Unit Rig:* {$meeting['rig_name']}\n"
                    . "📅 *Hari/Tgl:* {$tanggal}\n"
                    . "⏰ *Waktu:* {$jam}\n"
                    . "🔗 *Link Microsoft Teams:* {$meeting['teams_link']}\n\n"
                    . "Mohon mempersiapkan laporan harian dan bergabung tepat waktu.\n"
                    . "_Management PT Besmindo Oilfield Operations_";

            case 'H-1_JAM':
                return "*[REMINDER 1 JAM SEBELUM MEETING]*\n\n"
                    . "Halo *{$crew['name']}* ({$crew['position']}),\n"
                    . "Meeting *{$meeting['title']}* – Rig *{$meeting['rig_name']}*\n"
                    . "akan dimulai dalam *1 jam* pukul " . substr($meeting['start_time'], 0, 5) . " WIB.\n\n"
                    . "Pastikan koneksi internet dan perangkat Microsoft Teams Anda siap:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "_PT Besmindo Oilfield Operations_";

            case 'H-15_MIN':
                return "*[SEGERA BERGABUNG – 15 MENIT LAGI]*\n\n"
                    . "Halo *{$crew['name']}* ({$crew['position']}),\n"
                    . "Ruang Microsoft Teams untuk meeting *{$meeting['title']}* – Rig *{$meeting['rig_name']}* telah dibuka.\n\n"
                    . "Bergabunglah sekarang:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "Kehadiran Anda dicatat otomatis oleh sistem.\n"
                    . "_Management PT Besmindo Oilfield Operations_";

            default: // UNDANGAN
                return "*[UNDANGAN RESMI MEETING CREW RIG]*\n\n"
                    . "Yth. *{$crew['name']}* ({$crew['position']})\n"
                    . "Unit Rig: *{$meeting['rig_name']}*\n\n"
                    . "Anda diundang untuk menghadiri pertemuan rutin:\n"
                    . "📋 *Agenda:* {$meeting['title']}\n"
                    . "📝 *Topik:* {$topik}\n"
                    . "📅 *Tanggal:* {$tanggal}\n"
                    . "⏰ *Waktu:* {$jam}\n"
                    . "👤 *PJ Rig:* {$pj}\n\n"
                    . "🔗 *Tautan Microsoft Teams:* \n{$meeting['teams_link']}\n\n"
                    . "Harap konfirmasi kehadiran dan hadir 5 menit sebelum meeting dimulai.\n"
                    . "_Management PT Besmindo Oilfield Operations_";
        }
    }
}
