<?php

namespace App\Controllers;

use App\Models\MeetingModel;
use App\Models\MeetingParticipantModel;
use App\Models\CrewModel;
use App\Models\ReminderModel;
use App\Models\AttendanceModel;
use CodeIgniter\Controller;

class Api extends Controller
{
    protected $meetingModel;
    protected $participantModel;
    protected $crewModel;
    protected $reminderModel;
    protected $attendanceModel;

    public function __construct()
    {
        $this->meetingModel = new MeetingModel();
        $this->participantModel = new MeetingParticipantModel();
        $this->crewModel = new CrewModel();
        $this->reminderModel = new ReminderModel();
        $this->attendanceModel = new AttendanceModel();
    }

    public function checkReminders()
    {
        $now = time();
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $meetings = $this->meetingModel->getMeetingsDetailed();
        $processed = 0;
        $logs = [];

        foreach ($meetings as $m) {
            $meetingDateTimeStr = $m['meeting_date'] . ' ' . $m['start_time'];
            $meetingTime = strtotime($meetingDateTimeStr);
            $diffSeconds = $meetingTime - $now;
            $diffMinutes = round($diffSeconds / 60);

            // Get participants
            $participants = $this->participantModel->getParticipantsByMeeting($m['id']);

            // 1. Check H-1 (Meeting tomorrow)
            if ($m['meeting_date'] === $tomorrow && $m['status'] === 'scheduled') {
                foreach ($participants as $p) {
                    if (!$this->reminderModel->isAlreadySent($m['id'], $p['crew_id'], 'H-1')) {
                        $msg = "*[REMINDER H-1 MEETING]*\nHalo {$p['crew_name']}, besok Anda ada meeting: {$m['title']} ({$m['rig_name']}) jam " . substr($m['start_time'], 0, 5) . " WIB.\nLink: {$m['teams_link']}";
                        $this->reminderModel->insert([
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-1',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ]);
                        $processed++;
                        $logs[] = "Sent H-1 to {$p['crew_name']} for meeting #{$m['id']}";
                    }
                }
            }

            // 2. Check 1 Jam (between 45 and 75 minutes before start)
            if ($m['meeting_date'] === $today && $diffMinutes >= 45 && $diffMinutes <= 75 && $m['status'] === 'scheduled') {
                foreach ($participants as $p) {
                    if (!$this->reminderModel->isAlreadySent($m['id'], $p['crew_id'], 'H-1_JAM')) {
                        $msg = "*[REMINDER 1 JAM]*\nHalo {$p['crew_name']}, meeting {$m['title']} ({$m['rig_name']}) akan dimulai dalam 1 jam.\nLink: {$m['teams_link']}";
                        $this->reminderModel->insert([
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-1_JAM',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ]);
                        $processed++;
                        $logs[] = "Sent 1-Hour reminder to {$p['crew_name']} for meeting #{$m['id']}";
                    }
                }
            }

            // 3. Check 15 Menit (between 0 and 20 minutes before start)
            if ($m['meeting_date'] === $today && $diffMinutes >= 0 && $diffMinutes <= 20 && ($m['status'] === 'scheduled' || $m['status'] === 'in_progress')) {
                foreach ($participants as $p) {
                    if (!$this->reminderModel->isAlreadySent($m['id'], $p['crew_id'], 'H-15_MIN')) {
                        $msg = "*[REMINDER 15 MENIT]*\nHalo {$p['crew_name']}, ruang meeting {$m['title']} telah dibuka. Segera masuk Teams sekarang:\nLink: {$m['teams_link']}";
                        $this->reminderModel->insert([
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-15_MIN',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ]);
                        $processed++;
                        $logs[] = "Sent 15-Min reminder to {$p['crew_name']} for meeting #{$m['id']}";
                    }
                }
            }
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => "Pemeriksaan reminder selesai. {$processed} notifikasi diproses.",
            'processed' => $processed,
            'logs'      => $logs,
            'timestamp' => date('d M Y H:i:s') . ' WIB'
        ]);
    }
}
