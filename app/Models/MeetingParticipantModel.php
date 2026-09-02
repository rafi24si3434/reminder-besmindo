<?php

namespace App\Models;

use CodeIgniter\Model;

class MeetingParticipantModel extends Model
{
    protected $table            = 'meeting_participants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['meeting_id', 'crew_id', 'created_at'];

    public function getParticipantsByMeeting(int $meetingId)
    {
        return $this->select('meeting_participants.*, crews.nik, crews.name as crew_name, crews.position, crews.phone, crews.is_active, rigs.name as rig_name')
                    ->join('crews', 'crews.id = meeting_participants.crew_id')
                    ->join('rigs', 'rigs.id = crews.rig_id', 'left')
                    ->where('meeting_participants.meeting_id', $meetingId)
                    ->orderBy('crews.name', 'ASC')
                    ->findAll();
    }

    public function syncParticipants(int $meetingId, array $crewIds)
    {
        $this->where('meeting_id', $meetingId)->delete();
        $data = [];
        foreach ($crewIds as $cid) {
            $data[] = [
                'meeting_id' => $meetingId,
                'crew_id'    => (int)$cid
            ];
        }
        if (!empty($data)) {
            $this->insertBatch($data);
        }
    }
}
