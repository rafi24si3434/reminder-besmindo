<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rig_model extends CI_Model
{
    protected $table = 'rigs';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($active_only = TRUE)
    {
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        return $this->db->order_by('name', 'ASC')->get($this->table)->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row_array();
    }

    public function get_rig_with_crew_count()
    {
        $this->db->select('rigs.*, COUNT(crews.id) as total_crew');
        $this->db->from('rigs');
        $this->db->join('crews', 'crews.rig_id = rigs.id AND crews.is_active = 1', 'left');
        $this->db->group_by('rigs.id');
        $this->db->order_by('rigs.name', 'ASC');
        return $this->db->get()->result_array();
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
