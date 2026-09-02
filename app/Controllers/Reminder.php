<?php

namespace App\Controllers;

use App\Models\MeetingModel;
use App\Models\MeetingParticipantModel;
use App\Models\CrewModel;
use App\Models\RigModel;
use App\Models\ReminderModel;
use App\Models\SettingModel;
use CodeIgniter\Controller;

class Reminder extends Controller
{
    protected $meetingModel;
    protected $participantModel;
    protected $crewModel;
    protected $rigModel;
    protected $reminderModel;
    protected $settingModel;

    public function __construct()
    {
        $this->meetingModel = new MeetingModel();
        $this->participantModel = new MeetingParticipantModel();
        $this->crewModel = new CrewModel();
        $this->rigModel = new RigModel();
        $this->reminderModel = new ReminderModel();
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $meetings = $this->meetingModel->getMeetingsDetailed('scheduled');
        if (empty($meetings)) {
            $meetings = $this->meetingModel->getMeetingsDetailed();
        }

        $selectedMeetingId = $this->request->getGet('meeting_id');
        if (!$selectedMeetingId && !empty($meetings)) {
            $selectedMeetingId = $meetings[0]['id'];
        }

        $selectedMeeting = null;
        $participants = [];
        if ($selectedMeetingId) {
            $selectedMeeting = $this->meetingModel->getMeetingDetail((int)$selectedMeetingId);
            $participants = $this->participantModel->getParticipantsByMeeting((int)$selectedMeetingId);
        }

        return view('reminder/index', [
            'title'           => 'Undangan & Broadcast WhatsApp - Besmindo Reminder',
            'meetings'        => $meetings,
            'selectedMeeting' => $selectedMeeting,
            'participants'    => $participants
        ]);
    }

    public function broadcast(int $meetingId)
    {
        $meeting = $this->meetingModel->getMeetingDetail($meetingId);
        if (!$meeting) {
            return redirect()->to(base_url('reminder'))->with('error', 'Meeting tidak ditemukan.');
        }

        $participants = $this->participantModel->getParticipantsByMeeting($meetingId);
        $meetings = $this->meetingModel->getMeetingsDetailed();

        return view('reminder/index', [
            'title'           => 'Broadcast Undangan WA: ' . $meeting['title'],
            'meetings'        => $meetings,
            'selectedMeeting' => $meeting,
            'participants'    => $participants
        ]);
    }

    public function send()
    {
        $meetingId = (int)$this->request->getPost('meeting_id');
        $reminderType = $this->request->getPost('reminder_type') ?? 'UNDANGAN';
        $selectedCrewIds = $this->request->getPost('crew_ids') ?? [];
        $customMessageTemplate = $this->request->getPost('message_template');

        $meeting = $this->meetingModel->getMeetingDetail($meetingId);
        if (!$meeting) {
            return redirect()->back()->with('error', 'Data meeting tidak valid.');
        }

        if (empty($selectedCrewIds)) {
            $participants = $this->participantModel->getParticipantsByMeeting($meetingId);
            $selectedCrewIds = array_column($participants, 'crew_id');
        }

        $successCount = 0;
        $crews = $this->crewModel->whereIn('id', $selectedCrewIds)->findAll();

        foreach ($crews as $c) {
            $message = $this->buildMessage($customMessageTemplate, $reminderType, $meeting, $c);

            $this->reminderModel->insert([
                'meeting_id'    => $meetingId,
                'crew_id'       => $c['id'],
                'reminder_type' => $reminderType,
                'target_phone'  => $c['phone'],
                'message_body'  => $message,
                'status'        => 'sent',
                'sent_at'       => date('Y-m-d H:i:s')
            ]);

            $successCount++;
        }

        return redirect()->to(base_url('reminder/log?meeting_id=' . $meetingId))
                         ->with('success', "Berhasil mengirim {$successCount} pesan reminder WhatsApp ke personil crew!");
    }

    public function log()
    {
        $meetingId = $this->request->getGet('meeting_id');
        $logs = $this->reminderModel->getLogs($meetingId ? (int)$meetingId : null, 150);
        $meetings = $this->meetingModel->getMeetingsDetailed();

        return view('reminder/log', [
            'title'             => 'Log Outbox WhatsApp Reminder - Besmindo Reminder',
            'logs'              => $logs,
            'meetings'          => $meetings,
            'selectedMeetingId' => $meetingId
        ]);
    }

    private function buildMessage(?string $template, string $type, array $meeting, array $crew): string
    {
        if (!empty($template)) {
            $tags = [
                '{NAMA}'       => $crew['name'],
                '{JABATAN}'    => $crew['position'],
                '{RIG}'        => $meeting['rig_name'],
                '{JUDUL}'      => $meeting['title'],
                '{TOPIK}'      => $meeting['topic'],
                '{TANGGAL}'    => date('d M Y', strtotime($meeting['meeting_date'])),
                '{JAM}'        => substr($meeting['start_time'], 0, 5) . ' WIB',
                '{TEAMS_LINK}' => $meeting['teams_link']
            ];
            return str_replace(array_keys($tags), array_values($tags), $template);
        }

        // Default template based on type
        $tanggal = date('d M Y', strtotime($meeting['meeting_date']));
        $jam = substr($meeting['start_time'], 0, 5) . ' - ' . substr($meeting['end_time'], 0, 5) . ' WIB';

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
                    . "Mohon mempersiapkan laporan harian dan bergabung tepat waktu. Terima kasih.";

            case 'H-1_JAM':
                return "*[REMINDER 1 JAM SEBELUM MEETING]*\n\n"
                    . "Halo *{$crew['name']}*,\n"
                    . "Meeting *{$meeting['title']}* ({$meeting['rig_name']}) akan dimulai dalam *1 jam* (Pukul " . substr($meeting['start_time'], 0, 5) . " WIB).\n\n"
                    . "Silakan pastikan koneksi internet dan perangkat Teams Anda siap:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "_PT Besmindo Oilfield Operations_";

            case 'H-15_MIN':
                return "*[PANGGILAN SEGERA BERGABUNG - 15 MENIT LAGI]*\n\n"
                    . "Halo *{$crew['name']}*,\n"
                    . "Ruang meeting Microsoft Teams untuk *{$meeting['title']}* telah dibuka.\n\n"
                    . "Segera bergabung sekarang melalui link:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "Kehadiran Anda akan dicatat otomatis oleh sistem.";

            case 'NOT_PRESENT_REMINDER':
                return "*[PENTING: REMINDER KEHADIRAN MEETING SEDANG BERLANGSUNG]*\n\n"
                    . "Yth. *{$crew['name']}* ({$crew['position']}),\n"
                    . "Meeting rutin *{$meeting['title']}* ({$meeting['rig_name']}) telah dimulai saat ini.\n"
                    . "Status kehadiran Anda tercatat *BELUM HADIR* di ruang Microsoft Teams.\n\n"
                    . "Mohon segera bergabung sekarang melalui tautan berikut:\n"
                    . "👉 {$meeting['teams_link']}\n\n"
                    . "Terima kasih atas kedisiplinan Anda.";

            default: // UNDANGAN
                return "*[UNDANGAN RESMI MEETING CREW RIG]*\n\n"
                    . "Yth. *{$crew['name']}* ({$crew['position']})\n"
                    . "Unit Rig: *{$meeting['rig_name']}*\n\n"
                    . "Anda diundang untuk menghadiri pertemuan rutin:\n"
                    . "📋 *Agenda:* {$meeting['title']}\n"
                    . "📝 *Topik:* {$meeting['topic']}\n"
                    . "📅 *Tanggal:* {$tanggal}\n"
                    . "⏰ *Waktu:* {$jam}\n"
                    . "👤 *PJ Rig:* {$meeting['pj_name']}\n\n"
                    . "🔗 *Tautan Microsoft Teams:* \n{$meeting['teams_link']}\n\n"
                    . "Harap konfirmasi kehadiran dan hadir 5 menit sebelum meeting dimulai.\n"
                    . "_Management PT Besmindo Oilfield Operations_";
        }
    }
}
