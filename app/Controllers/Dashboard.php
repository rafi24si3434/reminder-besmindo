<?php

namespace App\Controllers;

use App\Models\MeetingModel;
use App\Models\CrewModel;
use App\Models\RigModel;
use App\Models\AttendanceModel;
use App\Models\ReminderModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $meetingModel = new MeetingModel();
        $crewModel = new CrewModel();
        $rigModel = new RigModel();
        $attendanceModel = new AttendanceModel();
        $reminderModel = new ReminderModel();

        // 1. Today's meetings
        $todayMeetings = $meetingModel->getTodayMeetings();
        $upcomingMeetings = $meetingModel->getUpcomingMeetings(6);

        // 2. Statistics
        $totalCrews = $crewModel->where('is_active', 1)->countAllResults();
        $totalRigs = $rigModel->where('is_active', 1)->countAllResults();
        $totalMeetings = $meetingModel->countAllResults();

        // 3. Attendance Stats
        $attendanceStats = $attendanceModel->getAttendanceStats();

        // 4. Rig summary
        $rigSummaries = $rigModel->getRigWithCrewCount();

        // 5. Recent reminder logs
        $recentReminders = $reminderModel->getLogs(null, 5);

        return view('dashboard/index', [
            'title'            => 'Dashboard Manager - Besmindo Reminder',
            'todayMeetings'    => $todayMeetings,
            'upcomingMeetings' => $upcomingMeetings,
            'totalCrews'       => $totalCrews,
            'totalRigs'        => $totalRigs,
            'totalMeetings'    => $totalMeetings,
            'attendanceStats'  => $attendanceStats,
            'rigSummaries'     => $rigSummaries,
            'recentReminders'  => $recentReminders
        ]);
    }
}
