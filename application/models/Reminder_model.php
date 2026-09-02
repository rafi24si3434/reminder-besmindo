<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reminder_model extends CI_Model
{
    protected $table = 'reminders_log';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_logs($meeting_id = NULL, $limit = 100)
    {
        $this->db->select('reminders_log.*, meetings.title as meeting_title, meetings.meeting_date, meetings.start_time, crews.name as crew_name, crews.position, rigs.name as rig_name');
        $this->db->from('reminders_log');
        $this->db->join('meetings', 'meetings.id = reminders_log.meeting_id', 'left');
        $this->db->join('crews', 'crews.id = reminders_log.crew_id', 'left');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');

        if (!empty($meeting_id)) {
            $this->db->where('reminders_log.meeting_id', $meeting_id);
        }

        $this->db->order_by('reminders_log.sent_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function is_already_sent($meeting_id, $crew_id, $reminder_type)
    {
        $res = $this->db->get_where($this->table, array(
            'meeting_id'    => $meeting_id,
            'crew_id'       => $crew_id,
            'reminder_type' => $reminder_type,
            'status'        => 'sent'
        ))->row_array();
        return !empty($res);
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }
}
