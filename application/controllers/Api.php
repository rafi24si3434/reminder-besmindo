<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Meeting_model');
        $this->load->model('Meeting_participant_model');
        $this->load->model('Crew_model');
        $this->load->model('Reminder_model');
        $this->load->model('Attendance_model');
    }

    public function check_reminders()
    {
        $now = time();
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $meetings = $this->Meeting_model->get_meetings_detailed();
        $processed = 0;
        $logs = array();

        foreach ($meetings as $m) {
            $meeting_time = strtotime($m['meeting_date'] . ' ' . $m['start_time']);
            $diff_seconds = $meeting_time - $now;
            $diff_minutes = round($diff_seconds / 60);

            $participants = $this->Meeting_participant_model->get_participants_by_meeting($m['id']);

            // 1. H-1
            if ($m['meeting_date'] === $tomorrow && $m['status'] === 'scheduled') {
                foreach ($participants as $p) {
                    if (!$this->Reminder_model->is_already_sent($m['id'], $p['crew_id'], 'H-1')) {
                        $msg = "*[REMINDER H-1 MEETING]*\nHalo {$p['crew_name']}, besok Anda ada meeting: {$m['title']} ({$m['rig_name']}) jam " . substr($m['start_time'], 0, 5) . " WIB.\nLink: {$m['teams_link']}";
                        $this->Reminder_model->insert(array(
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-1',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ));
                        $processed++;
                        $logs[] = "Sent H-1 to {$p['crew_name']}";
                    }
                }
            }

            // 2. 1 Jam
            if ($m['meeting_date'] === $today && $diff_minutes >= 45 && $diff_minutes <= 75 && $m['status'] === 'scheduled') {
                foreach ($participants as $p) {
                    if (!$this->Reminder_model->is_already_sent($m['id'], $p['crew_id'], 'H-1_JAM')) {
                        $msg = "*[REMINDER 1 JAM]*\nHalo {$p['crew_name']}, meeting {$m['title']} ({$m['rig_name']}) akan dimulai dalam 1 jam.\nLink: {$m['teams_link']}";
                        $this->Reminder_model->insert(array(
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-1_JAM',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ));
                        $processed++;
                        $logs[] = "Sent 1-Hour reminder to {$p['crew_name']}";
                    }
                }
            }

            // 3. 15 Menit
            if ($m['meeting_date'] === $today && $diff_minutes >= 0 && $diff_minutes <= 20 && ($m['status'] === 'scheduled' || $m['status'] === 'in_progress')) {
                foreach ($participants as $p) {
                    if (!$this->Reminder_model->is_already_sent($m['id'], $p['crew_id'], 'H-15_MIN')) {
                        $msg = "*[REMINDER 15 MENIT]*\nHalo {$p['crew_name']}, ruang meeting {$m['title']} telah dibuka. Segera masuk Teams sekarang:\nLink: {$m['teams_link']}";
                        $this->Reminder_model->insert(array(
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-15_MIN',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ));
                        $processed++;
                        $logs[] = "Sent 15-Min reminder to {$p['crew_name']}";
                    }
                }
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'   => true,
                'message'   => "Pemeriksaan reminder selesai. {$processed} notifikasi diproses.",
                'processed' => $processed,
                'logs'      => $logs,
                'timestamp' => date('d M Y H:i:s') . ' WIB'
            )));
    }
}
