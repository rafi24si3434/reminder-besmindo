<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_model extends CI_Model
{
    protected $table = 'meetings';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row_array();
    }

    public function get_meetings_detailed($status = NULL, $rig_id = NULL, $date = NULL)
    {
        $this->db->select('meetings.*, rigs.name as rig_name, rigs.code as rig_code, crews.name as pj_name, crews.phone as pj_phone');
        $this->db->from('meetings');
        $this->db->join('rigs', 'rigs.id = meetings.rig_id', 'left');
        $this->db->join('crews', 'crews.id = meetings.pj_crew_id', 'left');

        if (!empty($status)) {
            $this->db->where('meetings.status', $status);
        }
        if (!empty($rig_id)) {
            $this->db->where('meetings.rig_id', $rig_id);
        }
        if (!empty($date)) {
            $this->db->where('meetings.meeting_date', $date);
        }

        $this->db->order_by('meetings.meeting_date', 'DESC');
        $this->db->order_by('meetings.start_time', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_meeting_detail($id)
    {
        $this->db->select('meetings.*, rigs.name as rig_name, rigs.code as rig_code, rigs.location as rig_location, crews.name as pj_name, crews.phone as pj_phone, crews.position as pj_position');
        $this->db->from('meetings');
        $this->db->join('rigs', 'rigs.id = meetings.rig_id', 'left');
        $this->db->join('crews', 'crews.id = meetings.pj_crew_id', 'left');
        $this->db->where('meetings.id', $id);
        return $this->db->get()->row_array();
    }

    public function get_today_meetings()
    {
        $today = date('Y-m-d');
        $this->db->select('meetings.*, rigs.name as rig_name, rigs.code as rig_code, crews.name as pj_name');
        $this->db->from('meetings');
        $this->db->join('rigs', 'rigs.id = meetings.rig_id', 'left');
        $this->db->join('crews', 'crews.id = meetings.pj_crew_id', 'left');
        $this->db->where('meetings.meeting_date', $today);
        $this->db->order_by('meetings.start_time', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_upcoming_meetings($limit = 5)
    {
        $today = date('Y-m-d');
        $this->db->select('meetings.*, rigs.name as rig_name, rigs.code as rig_code, crews.name as pj_name');
        $this->db->from('meetings');
        $this->db->join('rigs', 'rigs.id = meetings.rig_id', 'left');
        $this->db->join('crews', 'crews.id = meetings.pj_crew_id', 'left');
        $this->db->where('meetings.meeting_date >=', $today);
        $this->db->order_by('meetings.meeting_date', 'ASC');
        $this->db->order_by('meetings.start_time', 'ASC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}
