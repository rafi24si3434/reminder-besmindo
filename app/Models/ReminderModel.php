<?php

namespace App\Models;

use CodeIgniter\Model;

class ReminderModel extends Model
{
    protected $table            = 'reminders_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'meeting_id', 'crew_id', 'reminder_type', 
        'target_phone', 'message_body', 'status', 'sent_at', 'created_at'
    ];

    public function getLogs(?int $meetingId = null, int $limit = 100)
    {
        $builder = $this->select('reminders_log.*, meetings.title as meeting_title, meetings.meeting_date, meetings.start_time, crews.name as crew_name, crews.position, rigs.name as rig_name')
                        ->join('meetings', 'meetings.id = reminders_log.meeting_id', 'left')
                        ->join('crews', 'crews.id = reminders_log.crew_id', 'left')
                        ->join('rigs', 'rigs.id = crews.rig_id', 'left');

        if ($meetingId) {
            $builder->where('reminders_log.meeting_id', $meetingId);
        }

        return $builder->orderBy('reminders_log.sent_at', 'DESC')
                       ->limit($limit)
                       ->findAll();
    }

    public function isAlreadySent(int $meetingId, int $crewId, string $reminderType)
    {
        return $this->where('meeting_id', $meetingId)
                    ->where('crew_id', $crewId)
                    ->where('reminder_type', $reminderType)
                    ->where('status', 'sent')
                    ->first() !== null;
    }
}
