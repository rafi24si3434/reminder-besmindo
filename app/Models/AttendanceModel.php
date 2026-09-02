<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendances';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'meeting_id', 'crew_id', 'status', 
        'join_time', 'leave_time', 'duration_minutes', 
        'notes', 'source', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getMeetingAttendances(int $meetingId)
    {
        return $this->select('attendances.*, crews.nik, crews.name as crew_name, crews.position, crews.phone, rigs.name as rig_name')
                    ->join('crews', 'crews.id = attendances.crew_id')
                    ->join('rigs', 'rigs.id = crews.rig_id', 'left')
                    ->where('attendances.meeting_id', $meetingId)
                    ->orderBy('crews.name', 'ASC')
                    ->findAll();
    }

    public function initMeetingAttendances(int $meetingId)
    {
        // Get all participants of this meeting
        $db = \Config\Database::connect();
        $participants = $db->table('meeting_participants')
                           ->where('meeting_id', $meetingId)
                           ->get()
                           ->getResultArray();

        foreach ($participants as $p) {
            $existing = $this->where('meeting_id', $meetingId)->where('crew_id', $p['crew_id'])->first();
            if (!$existing) {
                $this->insert([
                    'meeting_id' => $meetingId,
                    'crew_id'    => $p['crew_id'],
                    'status'     => 'BELUM_HADIR',
                    'source'     => 'TEAMS_SIMULATOR'
                ]);
            }
        }
    }

    public function getAttendanceStats(?int $meetingId = null)
    {
        $builder = $this->builder();
        if ($meetingId) {
            $builder->where('meeting_id', $meetingId);
        }

        $all = $builder->get()->getResultArray();
        $total = count($all);
        $hadir = 0;
        $terlambat = 0;
        $belumHadir = 0;
        $tidakHadir = 0;
        $izin = 0;

        foreach ($all as $row) {
            if ($row['status'] === 'HADIR') $hadir++;
            elseif ($row['status'] === 'TERLAMBAT') $terlambat++;
            elseif ($row['status'] === 'BELUM_HADIR') $belumHadir++;
            elseif ($row['status'] === 'TIDAK_HADIR') $tidakHadir++;
            elseif ($row['status'] === 'IZIN') $izin++;
        }

        $presentCount = $hadir + $terlambat;
        $percentage = $total > 0 ? round(($presentCount / $total) * 100, 1) : 0;

        return [
            'total'       => $total,
            'hadir'       => $hadir,
            'terlambat'   => $terlambat,
            'belum_hadir' => $belumHadir,
            'tidak_hadir' => $tidakHadir,
            'izin'        => $izin,
            'present'     => $presentCount,
            'percentage'  => $percentage
        ];
    }

    public function getMostAbsentOrLateCrew(int $limit = 10)
    {
        $db = \Config\Database::connect();
        return $db->table('crews')
                  ->select('crews.id, crews.nik, crews.name, crews.position, rigs.name as rig_name, 
                            COUNT(attendances.id) as total_invited,
                            SUM(CASE WHEN attendances.status = "HADIR" THEN 1 ELSE 0 END) as count_hadir,
                            SUM(CASE WHEN attendances.status = "TERLAMBAT" THEN 1 ELSE 0 END) as count_terlambat,
                            SUM(CASE WHEN attendances.status = "TIDAK_HADIR" THEN 1 ELSE 0 END) as count_tidak_hadir,
                            SUM(CASE WHEN attendances.status = "BELUM_HADIR" THEN 1 ELSE 0 END) as count_belum_hadir,
                            SUM(CASE WHEN attendances.status = "IZIN" THEN 1 ELSE 0 END) as count_izin')
                  ->join('rigs', 'rigs.id = crews.rig_id', 'left')
                  ->join('attendances', 'attendances.crew_id = crews.id', 'left')
                  ->where('crews.is_active', 1)
                  ->groupBy('crews.id')
                  ->orderBy('count_tidak_hadir', 'DESC')
                  ->orderBy('count_terlambat', 'DESC')
                  ->limit($limit)
                  ->get()
                  ->getResultArray();
    }

    public function getRecapByRig()
    {
        $db = \Config\Database::connect();
        return $db->table('rigs')
                  ->select('rigs.id, rigs.name, rigs.code, rigs.pj_name,
                            COUNT(attendances.id) as total_meeting_records,
                            SUM(CASE WHEN attendances.status = "HADIR" THEN 1 ELSE 0 END) as count_hadir,
                            SUM(CASE WHEN attendances.status = "TERLAMBAT" THEN 1 ELSE 0 END) as count_terlambat,
                            SUM(CASE WHEN attendances.status = "TIDAK_HADIR" THEN 1 ELSE 0 END) as count_tidak_hadir')
                  ->join('crews', 'crews.rig_id = rigs.id', 'left')
                  ->join('attendances', 'attendances.crew_id = crews.id', 'left')
                  ->where('rigs.is_active', 1)
                  ->groupBy('rigs.id')
                  ->get()
                  ->getResultArray();
    }
}
