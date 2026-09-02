<?php

namespace App\Models;

use CodeIgniter\Model;

class MeetingModel extends Model
{
    protected $table            = 'meetings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 'topic', 'rig_id', 'pj_crew_id', 
        'meeting_date', 'start_time', 'end_time', 
        'teams_link', 'is_recurring', 'recurring_day', 'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getMeetingsDetailed(?string $status = null, ?int $rigId = null, ?string $date = null)
    {
        $builder = $this->select('meetings.*, rigs.name as rig_name, rigs.code as rig_code, crews.name as pj_name, crews.phone as pj_phone')
                        ->join('rigs', 'rigs.id = meetings.rig_id', 'left')
                        ->join('crews', 'crews.id = meetings.pj_crew_id', 'left');

        if (!empty($status)) {
            $builder->where('meetings.status', $status);
        }

        if (!empty($rigId)) {
            $builder->where('meetings.rig_id', $rigId);
        }

        if (!empty($date)) {
            $builder->where('meetings.meeting_date', $date);
        }

        return $builder->orderBy('meetings.meeting_date', 'DESC')
                       ->orderBy('meetings.start_time', 'DESC')
                       ->findAll();
    }

    public function getMeetingDetail(int $id)
    {
        return $this->select('meetings.*, rigs.name as rig_name, rigs.code as rig_code, rigs.location as rig_location, crews.name as pj_name, crews.phone as pj_phone, crews.position as pj_position')
                    ->join('rigs', 'rigs.id = meetings.rig_id', 'left')
                    ->join('crews', 'crews.id = meetings.pj_crew_id', 'left')
                    ->where('meetings.id', $id)
                    ->first();
    }

    public function getTodayMeetings()
    {
        $today = date('Y-m-d');
        return $this->select('meetings.*, rigs.name as rig_name, rigs.code as rig_code, crews.name as pj_name')
                    ->join('rigs', 'rigs.id = meetings.rig_id', 'left')
                    ->join('crews', 'crews.id = meetings.pj_crew_id', 'left')
                    ->where('meetings.meeting_date', $today)
                    ->orderBy('meetings.start_time', 'ASC')
                    ->findAll();
    }

    public function getUpcomingMeetings(int $limit = 5)
    {
        $today = date('Y-m-d');
        return $this->select('meetings.*, rigs.name as rig_name, rigs.code as rig_code, crews.name as pj_name')
                    ->join('rigs', 'rigs.id = meetings.rig_id', 'left')
                    ->join('crews', 'crews.id = meetings.pj_crew_id', 'left')
                    ->where('meetings.meeting_date >=', $today)
                    ->orderBy('meetings.meeting_date', 'ASC')
                    ->orderBy('meetings.start_time', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }
}
