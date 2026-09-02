<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crew_model extends CI_Model
{
    protected $table = 'crews';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row_array();
    }

    public function get_crews_with_rig($rig_id = NULL, $active_only = NULL)
    {
        $this->db->select('crews.*, rigs.name as rig_name, rigs.code as rig_code');
        $this->db->from('crews');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');

        if (!empty($rig_id)) {
            $this->db->where('crews.rig_id', $rig_id);
        }

        if ($active_only !== NULL && $active_only !== '') {
            $this->db->where('crews.is_active', (int)$active_only);
        }

        $this->db->order_by('crews.rig_id', 'ASC');
        $this->db->order_by('crews.name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_by_rig($rig_id, $active_only = TRUE)
    {
        $this->db->where('rig_id', $rig_id);
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        return $this->db->order_by('name', 'ASC')->get($this->table)->result_array();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
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
