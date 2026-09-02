<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\CrewModel;
use App\Models\RigModel;
use App\Models\MeetingModel;
use CodeIgniter\Controller;

class Report extends Controller
{
    protected $attendanceModel;
    protected $crewModel;
    protected $rigModel;
    protected $meetingModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->crewModel = new CrewModel();
        $this->rigModel = new RigModel();
        $this->meetingModel = new MeetingModel();
    }

    public function index()
    {
        $rigId = $this->request->getGet('rig_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $rigs = $this->rigModel->getActiveRigs();
        
        // 1. Overall stats
        $stats = $this->attendanceModel->getAttendanceStats();

        // 2. Ranking crew paling sering terlambat / tidak hadir (PRD Fase 5: Crew paling sering bolos)
        $mostAbsentCrews = $this->attendanceModel->getMostAbsentOrLateCrew(10);

        // 3. Rekap per Rig
        $rigRecaps = $this->attendanceModel->getRecapByRig();

        // 4. Detail Records query with filters
        $db = \Config\Database::connect();
        $builder = $db->table('attendances')
                      ->select('attendances.*, crews.nik, crews.name as crew_name, crews.position, rigs.name as rig_name, rigs.code as rig_code, meetings.title as meeting_title, meetings.meeting_date, meetings.start_time')
                      ->join('crews', 'crews.id = attendances.crew_id')
                      ->join('rigs', 'rigs.id = crews.rig_id', 'left')
                      ->join('meetings', 'meetings.id = attendances.meeting_id', 'left');

        if (!empty($rigId)) {
            $builder->where('crews.rig_id', (int)$rigId);
        }

        if (!empty($startDate)) {
            $builder->where('meetings.meeting_date >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('meetings.meeting_date <=', $endDate);
        }

        $records = $builder->orderBy('meetings.meeting_date', 'DESC')
                           ->orderBy('crews.name', 'ASC')
                           ->get()
                           ->getResultArray();

        return view('report/index', [
            'title'           => 'Rekapitulasi Kehadiran Meeting - Besmindo Reminder',
            'rigs'            => $rigs,
            'stats'           => $stats,
            'mostAbsentCrews' => $mostAbsentCrews,
            'rigRecaps'       => $rigRecaps,
            'records'         => $records,
            'selectedRig'     => $rigId,
            'startDate'       => $startDate,
            'endDate'         => $endDate
        ]);
    }

    public function print()
    {
        $rigId = $this->request->getGet('rig_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $db = \Config\Database::connect();
        $builder = $db->table('attendances')
                      ->select('attendances.*, crews.nik, crews.name as crew_name, crews.position, rigs.name as rig_name, rigs.code as rig_code, meetings.title as meeting_title, meetings.meeting_date, meetings.start_time')
                      ->join('crews', 'crews.id = attendances.crew_id')
                      ->join('rigs', 'rigs.id = crews.rig_id', 'left')
                      ->join('meetings', 'meetings.id = attendances.meeting_id', 'left');

        if (!empty($rigId)) {
            $builder->where('crews.rig_id', (int)$rigId);
        }
        if (!empty($startDate)) {
            $builder->where('meetings.meeting_date >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('meetings.meeting_date <=', $endDate);
        }

        $records = $builder->orderBy('meetings.meeting_date', 'DESC')->get()->getResultArray();
        $stats = $this->attendanceModel->getAttendanceStats();

        return view('report/print', [
            'title'     => 'Laporan Kehadiran Meeting Crew Rig - PT Besmindo',
            'records'   => $records,
            'stats'     => $stats,
            'startDate' => $startDate,
            'endDate'   => $endDate
        ]);
    }

    public function exportExcel()
    {
        $rigId = $this->request->getGet('rig_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $db = \Config\Database::connect();
        $builder = $db->table('attendances')
                      ->select('attendances.*, crews.nik, crews.name as crew_name, crews.position, rigs.name as rig_name, meetings.title as meeting_title, meetings.meeting_date, meetings.start_time')
                      ->join('crews', 'crews.id = attendances.crew_id')
                      ->join('rigs', 'rigs.id = crews.rig_id', 'left')
                      ->join('meetings', 'meetings.id = attendances.meeting_id', 'left');

        if (!empty($rigId)) {
            $builder->where('crews.rig_id', (int)$rigId);
        }
        if (!empty($startDate)) {
            $builder->where('meetings.meeting_date >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('meetings.meeting_date <=', $endDate);
        }

        $records = $builder->orderBy('meetings.meeting_date', 'DESC')->get()->getResultArray();

        $filename = 'Rekap_Kehadiran_Crew_Rig_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        // Add UTF-8 BOM for Excel
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        fputcsv($output, ['No', 'Tanggal', 'Jam', 'Nama Meeting', 'Unit Rig', 'NIK', 'Nama Crew', 'Jabatan', 'Status Kehadiran', 'Waktu Join', 'Durasi (Menit)', 'Sumber Data', 'Catatan']);

        $no = 1;
        foreach ($records as $r) {
            fputcsv($output, [
                $no++,
                $r['meeting_date'],
                substr($r['start_time'], 0, 5) . ' WIB',
                $r['meeting_title'],
                $r['rig_name'],
                $r['nik'],
                $r['crew_name'],
                $r['position'],
                $r['status'],
                $r['join_time'] ?: '-',
                $r['duration_minutes'] ?: '0',
                $r['source'],
                $r['notes'] ?: '-'
            ]);
        }

        fclose($output);
        exit;
    }
}
