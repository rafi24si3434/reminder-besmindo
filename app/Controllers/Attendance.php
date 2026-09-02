<?php

namespace App\Controllers;

use App\Models\MeetingModel;
use App\Models\MeetingParticipantModel;
use App\Models\CrewModel;
use App\Models\AttendanceModel;
use App\Models\ReminderModel;
use App\Models\SettingModel;
use CodeIgniter\Controller;

class Attendance extends Controller
{
    protected $meetingModel;
    protected $participantModel;
    protected $crewModel;
    protected $attendanceModel;
    protected $reminderModel;
    protected $settingModel;

    public function __construct()
    {
        $this->meetingModel = new MeetingModel();
        $this->participantModel = new MeetingParticipantModel();
        $this->crewModel = new CrewModel();
        $this->attendanceModel = new AttendanceModel();
        $this->reminderModel = new ReminderModel();
        $this->settingModel = new SettingModel();
    }

    public function live(?int $meetingId = null)
    {
        $meetings = $this->meetingModel->getMeetingsDetailed();
        if (empty($meetings)) {
            return redirect()->to(base_url('meeting'))->with('error', 'Belum ada jadwal meeting.');
        }

        if (!$meetingId) {
            // Find in_progress first, or first meeting today, or first meeting
            $today = date('Y-m-d');
            $inProgress = array_filter($meetings, fn($m) => $m['status'] === 'in_progress');
            if (!empty($inProgress)) {
                $meetingId = reset($inProgress)['id'];
            } else {
                $todayM = array_filter($meetings, fn($m) => $m['meeting_date'] === $today);
                $meetingId = !empty($todayM) ? reset($todayM)['id'] : $meetings[0]['id'];
            }
        }

        $meeting = $this->meetingModel->getMeetingDetail($meetingId);
        if (!$meeting) {
            return redirect()->to(base_url('meeting'))->with('error', 'Meeting tidak ditemukan.');
        }

        // Make sure attendances initialized
        $this->attendanceModel->initMeetingAttendances($meetingId);

        $attendances = $this->attendanceModel->getMeetingAttendances($meetingId);
        $stats = $this->attendanceModel->getAttendanceStats($meetingId);

        return view('attendance/live', [
            'title'       => 'Live Attendance Monitor: ' . $meeting['title'],
            'meetings'    => $meetings,
            'meeting'     => $meeting,
            'attendances' => $attendances,
            'stats'       => $stats
        ]);
    }

    public function simulator()
    {
        $meetings = $this->meetingModel->getMeetingsDetailed();
        $selectedMeetingId = $this->request->getGet('meeting_id');
        if (!$selectedMeetingId && !empty($meetings)) {
            $selectedMeetingId = $meetings[0]['id'];
        }

        $meeting = null;
        $attendances = [];
        if ($selectedMeetingId) {
            $meeting = $this->meetingModel->getMeetingDetail((int)$selectedMeetingId);
            $this->attendanceModel->initMeetingAttendances((int)$selectedMeetingId);
            $attendances = $this->attendanceModel->getMeetingAttendances((int)$selectedMeetingId);
        }

        return view('attendance/simulator', [
            'title'       => 'Microsoft Teams Attendance Simulator & CSV Import',
            'meetings'    => $meetings,
            'meeting'     => $meeting,
            'attendances' => $attendances
        ]);
    }

    public function simulateJoin()
    {
        $attendanceId = (int)$this->request->getPost('attendance_id');
        $joinType = $this->request->getPost('join_type') ?? 'on_time'; // 'on_time' or 'late'

        $attendance = $this->attendanceModel->find($attendanceId);
        if (!$attendance) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data kehadiran tidak ditemukan.']);
        }

        $meeting = $this->meetingModel->find($attendance['meeting_id']);
        $meetingDate = $meeting['meeting_date'];
        $startTime = $meeting['start_time'];

        if ($joinType === 'late') {
            // Join 20 minutes after start
            $joinTime = date('Y-m-d H:i:s', strtotime("{$meetingDate} {$startTime} +20 minutes"));
            $status = 'TERLAMBAT';
            $duration = 40;
        } else {
            // Join 2 minutes before start
            $joinTime = date('Y-m-d H:i:s', strtotime("{$meetingDate} {$startTime} -2 minutes"));
            $status = 'HADIR';
            $duration = 60;
        }

        $this->attendanceModel->update($attendanceId, [
            'status'           => $status,
            'join_time'        => $joinTime,
            'duration_minutes' => $duration,
            'source'           => 'TEAMS_SIMULATOR'
        ]);

        return $this->response->setJSON([
            'success'   => true,
            'status'    => $status,
            'join_time' => $joinTime,
            'message'   => "Status kehadiran berhasil diupdate: {$status}"
        ]);
    }

    public function updateStatusManual()
    {
        $attendanceId = (int)$this->request->getPost('attendance_id');
        $status = $this->request->getPost('status');
        $notes = trim($this->request->getPost('notes') ?? '');
        $joinTime = $this->request->getPost('join_time');

        $valid = ['HADIR', 'TERLAMBAT', 'BELUM_HADIR', 'TIDAK_HADIR', 'IZIN'];
        if (!in_array($status, $valid)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $data = [
            'status' => $status,
            'notes'  => $notes,
            'source' => 'MANUAL'
        ];

        if (!empty($joinTime)) {
            $data['join_time'] = $joinTime;
        }

        $this->attendanceModel->update($attendanceId, $data);

        return redirect()->back()->with('success', 'Status kehadiran berhasil disesuaikan secara manual.');
    }

    public function remindNotPresent(int $meetingId)
    {
        $meeting = $this->meetingModel->getMeetingDetail($meetingId);
        if (!$meeting) {
            return redirect()->back()->with('error', 'Meeting tidak ditemukan.');
        }

        // Get all crew who are BELUM_HADIR or TIDAK_HADIR
        $attendances = $this->attendanceModel->select('attendances.*, crews.name, crews.phone, crews.position')
                                            ->join('crews', 'crews.id = attendances.crew_id')
                                            ->where('attendances.meeting_id', $meetingId)
                                            ->whereIn('attendances.status', ['BELUM_HADIR', 'TIDAK_HADIR'])
                                            ->findAll();

        if (empty($attendances)) {
            return redirect()->back()->with('success', 'Semua crew sudah hadir di ruang meeting!');
        }

        $count = 0;
        foreach ($attendances as $att) {
            $msg = "*[PENTING: REMINDER KEHADIRAN MEETING]*\n\n"
                 . "Halo *{$att['name']}* ({$att['position']}),\n"
                 . "Meeting rutin *{$meeting['title']}* telah dimulai saat ini.\n"
                 . "Anda tercatat *BELUM HADIR* di Microsoft Teams.\n\n"
                 . "Mohon segera bergabung sekarang melalui tautan:\n"
                 . "👉 {$meeting['teams_link']}\n\n"
                 . "Terima kasih.";

            $this->reminderModel->insert([
                'meeting_id'    => $meetingId,
                'crew_id'       => $att['crew_id'],
                'reminder_type' => 'NOT_PRESENT_REMINDER',
                'target_phone'  => $att['phone'],
                'message_body'  => $msg,
                'status'        => 'sent',
                'sent_at'       => date('Y-m-d H:i:s')
            ]);
            $count++;
        }

        return redirect()->back()->with('success', "Berhasil mengirim reminder WhatsApp ke {$count} crew yang belum hadir!");
    }

    public function importCsv()
    {
        $meetingId = (int)$this->request->getPost('meeting_id');
        $file = $this->request->getFile('teams_csv');

        if (!$meetingId || !$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Silakan pilih meeting dan unggah file CSV Microsoft Teams Attendance yang valid.');
        }

        $meeting = $this->meetingModel->find($meetingId);
        if (!$meeting) {
            return redirect()->back()->with('error', 'Meeting tidak ditemukan.');
        }

        $toleranceMin = (int)$this->settingModel->getVal('late_tolerance_minutes', '10');
        $startTime = strtotime($meeting['meeting_date'] . ' ' . $meeting['start_time']);

        $handle = fopen($file->getTempName(), 'r');
        $importedCount = 0;

        if ($handle !== false) {
            // Read header
            $header = fgetcsv($handle, 1000, ',');
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Expected format: Name, Email/ID, Join Time, Leave Time, Duration
                if (count($data) >= 1) {
                    $nameOrEmail = trim($data[0] ?? '');
                    if (empty($nameOrEmail)) continue;

                    // Match crew by name or email
                    $crew = $this->crewModel->like('name', $nameOrEmail, 'both')
                                           ->orLike('email', $nameOrEmail, 'both')
                                           ->first();

                    if ($crew) {
                        $joinTimeStr = $data[1] ?? date('Y-m-d H:i:s');
                        $joinTimestamp = strtotime($joinTimeStr) ?: time();

                        // Determine status
                        $diffMinutes = ($joinTimestamp - $startTime) / 60;
                        $status = ($diffMinutes > $toleranceMin) ? 'TERLAMBAT' : 'HADIR';

                        $existing = $this->attendanceModel->where('meeting_id', $meetingId)->where('crew_id', $crew['id'])->first();
                        if ($existing) {
                            $this->attendanceModel->update($existing['id'], [
                                'status'           => $status,
                                'join_time'        => date('Y-m-d H:i:s', $joinTimestamp),
                                'duration_minutes' => 50,
                                'source'           => 'TEAMS_SYNC'
                            ]);
                        } else {
                            $this->attendanceModel->insert([
                                'meeting_id'       => $meetingId,
                                'crew_id'          => $crew['id'],
                                'status'           => $status,
                                'join_time'        => date('Y-m-d H:i:s', $joinTimestamp),
                                'duration_minutes' => 50,
                                'source'           => 'TEAMS_SYNC'
                            ]);
                        }
                        $importedCount++;
                    }
                }
            }
            fclose($handle);
        }

        return redirect()->to(base_url('attendance/live/' . $meetingId))->with('success', "Berhasil mengimpor & mensinkronisasi data kehadiran {$importedCount} crew dari file Microsoft Teams!");
    }
}
