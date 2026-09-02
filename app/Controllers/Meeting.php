<?php

namespace App\Controllers;

use App\Models\MeetingModel;
use App\Models\MeetingParticipantModel;
use App\Models\RigModel;
use App\Models\CrewModel;
use App\Models\AttendanceModel;
use CodeIgniter\Controller;

class Meeting extends Controller
{
    protected $meetingModel;
    protected $participantModel;
    protected $rigModel;
    protected $crewModel;
    protected $attendanceModel;

    public function __construct()
    {
        $this->meetingModel = new MeetingModel();
        $this->participantModel = new MeetingParticipantModel();
        $this->rigModel = new RigModel();
        $this->crewModel = new CrewModel();
        $this->attendanceModel = new AttendanceModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        $rigId = $this->request->getGet('rig_id');
        $date = $this->request->getGet('date');

        $meetings = $this->meetingModel->getMeetingsDetailed($status, $rigId ? (int)$rigId : null, $date);
        $rigs = $this->rigModel->getActiveRigs();

        return view('meeting/index', [
            'title'          => 'Jadwal Meeting Rig - Besmindo Reminder',
            'meetings'       => $meetings,
            'rigs'           => $rigs,
            'selectedStatus' => $status,
            'selectedRig'    => $rigId,
            'selectedDate'   => $date
        ]);
    }

    public function create()
    {
        $rigs = $this->rigModel->getActiveRigs();
        $crews = $this->crewModel->getCrewsWithRig(null, 1);

        return view('meeting/create', [
            'title' => 'Buat Jadwal Meeting Baru - Besmindo Reminder',
            'rigs'  => $rigs,
            'crews' => $crews
        ]);
    }

    public function edit(int $id)
    {
        $meeting = $this->meetingModel->getMeetingDetail($id);
        if (!$meeting) {
            return redirect()->to(base_url('meeting'))->with('error', 'Meeting tidak ditemukan.');
        }

        $rigs = $this->rigModel->getActiveRigs();
        $crews = $this->crewModel->getCrewsWithRig($meeting['rig_id'], 1);
        $participants = $this->participantModel->where('meeting_id', $id)->findAll();
        $participantIds = array_column($participants, 'crew_id');

        return view('meeting/edit', [
            'title'          => 'Ubah / Reschedule Jadwal Meeting - Besmindo Reminder',
            'meeting'        => $meeting,
            'rigs'           => $rigs,
            'crews'          => $crews,
            'participantIds' => $participantIds
        ]);
    }

    public function detail(int $id)
    {
        $meeting = $this->meetingModel->getMeetingDetail($id);
        if (!$meeting) {
            return redirect()->to(base_url('meeting'))->with('error', 'Meeting tidak ditemukan.');
        }

        $participants = $this->participantModel->getParticipantsByMeeting($id);
        $attendances = $this->attendanceModel->getMeetingAttendances($id);
        $attendanceStats = $this->attendanceModel->getAttendanceStats($id);

        return view('meeting/detail', [
            'title'           => 'Detail Meeting: ' . $meeting['title'],
            'meeting'         => $meeting,
            'participants'    => $participants,
            'attendances'     => $attendances,
            'attendanceStats' => $attendanceStats
        ]);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        $title = trim($this->request->getPost('title') ?? '');
        $topic = trim($this->request->getPost('topic') ?? '');
        $rigId = (int)$this->request->getPost('rig_id');
        $pjCrewId = (int)$this->request->getPost('pj_crew_id');
        $meetingDate = $this->request->getPost('meeting_date');
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        $teamsLink = trim($this->request->getPost('teams_link') ?? '');
        $isRecurring = $this->request->getPost('is_recurring') ? 1 : 0;
        $recurringDay = $isRecurring ? date('l', strtotime($meetingDate)) : null;
        $status = $this->request->getPost('status') ?? 'scheduled';
        $selectedCrews = $this->request->getPost('crew_ids') ?? [];

        if (empty($title) || empty($rigId) || empty($meetingDate) || empty($startTime) || empty($endTime)) {
            return redirect()->back()->withInput()->with('error', 'Judul Meeting, Rig, Tanggal, Jam Mulai dan Jam Selesai wajib diisi.');
        }

        // Default Teams Link if empty
        if (empty($teamsLink)) {
            $teamsLink = 'https://teams.microsoft.com/l/meetup-join/19%3ameeting_' . url_title($title, '_', true) . '%40thread.v2/0';
        }

        $data = [
            'title'         => $title,
            'topic'         => $topic,
            'rig_id'        => $rigId,
            'pj_crew_id'    => $pjCrewId ?: null,
            'meeting_date'  => $meetingDate,
            'start_time'    => $startTime,
            'end_time'      => $endTime,
            'teams_link'    => $teamsLink,
            'is_recurring'  => $isRecurring,
            'recurring_day' => $recurringDay,
            'status'        => $status
        ];

        if (!empty($id)) {
            $this->meetingModel->update($id, $data);
            $meetingId = $id;
            $msg = 'Jadwal meeting berhasil diperbarui / di-reschedule.';
        } else {
            $meetingId = $this->meetingModel->insert($data);
            $msg = 'Jadwal meeting baru berhasil dibuat.';
        }

        // If no crews specifically checked, auto-assign all active crews of this rig
        if (empty($selectedCrews)) {
            $rigCrews = $this->crewModel->getCrewsByRig($rigId);
            $selectedCrews = array_column($rigCrews, 'id');
        }

        // Sync participants
        $this->participantModel->syncParticipants($meetingId, $selectedCrews);

        // Initialize attendance rows for these participants
        $this->attendanceModel->initMeetingAttendances($meetingId);

        return redirect()->to(base_url('meeting/detail/' . $meetingId))->with('success', $msg);
    }

    public function quickReschedule()
    {
        $id = (int)$this->request->getPost('meeting_id');
        $meetingDate = $this->request->getPost('meeting_date');
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');

        if (!$id || !$meetingDate || !$startTime || !$endTime) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parameter tanggal dan waktu tidak lengkap.']);
        }

        $this->meetingModel->update($id, [
            'meeting_date' => $meetingDate,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
            'status'       => 'scheduled'
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Jadwal meeting berhasil diubah secara instan!']);
    }

    public function updateStatus(int $id, string $newStatus)
    {
        $valid = ['scheduled', 'in_progress', 'completed', 'cancelled'];
        if (in_array($newStatus, $valid)) {
            $this->meetingModel->update($id, ['status' => $newStatus]);
            return redirect()->back()->with('success', "Status meeting diubah menjadi {$newStatus}.");
        }
        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    public function delete(int $id)
    {
        $meeting = $this->meetingModel->find($id);
        if ($meeting) {
            $this->meetingModel->delete($id);
            return redirect()->to(base_url('meeting'))->with('success', "Meeting '{$meeting['title']}' berhasil dihapus.");
        }
        return redirect()->to(base_url('meeting'))->with('error', 'Meeting tidak ditemukan.');
    }

    public function getCrewsByRigJson(int $rigId)
    {
        $crews = $this->crewModel->getCrewsByRig($rigId);
        return $this->response->setJSON($crews);
    }
}
