<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_participant_model extends CI_Model
{
    protected $table = 'meeting_participants';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_participants_by_meeting($meeting_id)
    {
        $this->db->select('meeting_participants.*, crews.nik, crews.name as crew_name, crews.position, crews.phone, crews.is_active, rigs.name as rig_name');
        $this->db->from('meeting_participants');
        $this->db->join('crews', 'crews.id = meeting_participants.crew_id');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');
        $this->db->where('meeting_participants.meeting_id', $meeting_id);
        $this->db->order_by('crews.name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function sync_participants($meeting_id, $crew_ids)
    {
        $this->db->where('meeting_id', $meeting_id)->delete($this->table);
        $data = array();
        foreach ($crew_ids as $cid) {
            $data[] = array(
                'meeting_id' => $meeting_id,
                'crew_id'    => (int)$cid
            );
        }
        if (!empty($data)) {
            $this->db->insert_batch($this->table, $data);
        }
    }
}
