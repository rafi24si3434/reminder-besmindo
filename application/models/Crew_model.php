<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crew_model extends CI_Model
{
    protected $table = 'crews';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($active_only = NULL)
    {
        if ($active_only !== NULL && $active_only !== '') {
            $this->db->where('is_active', (int)$active_only);
        }
        return $this->db->order_by('name', 'ASC')->get($this->table)->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))->row_array();
    }


    public function get_crews_with_rig($rig_id = NULL, $group_code = NULL, $active_only = NULL)
    {
        $this->db->select('crews.*, rigs.name as rig_name, rigs.code as rig_code');
        $this->db->from('crews');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');

        if (!empty($rig_id)) {
            $this->db->where('crews.rig_id', $rig_id);
        }

        if (!empty($group_code) && in_array($group_code, array('A', 'B', 'C'))) {
            $this->db->where('crews.group_code', $group_code);
        }

        if ($active_only !== NULL && $active_only !== '') {
            $this->db->where('crews.is_active', (int)$active_only);
        }

        $this->db->order_by('crews.rig_id', 'ASC');
        $this->db->order_by('crews.group_code', 'ASC');
        $this->db->order_by('crews.name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_by_rig($rig_id, $active_only = TRUE, $group_code = NULL)
    {
        $this->db->where('rig_id', $rig_id);
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        if (!empty($group_code) && in_array($group_code, array('A', 'B', 'C'))) {
            $this->db->where('group_code', $group_code);
        }
        return $this->db->order_by('group_code', 'ASC')->order_by('name', 'ASC')->get($this->table)->result_array();
    }

    public function get_by_multiple_targets($targets = array(), $active_only = TRUE)
    {
        // $targets e.g. [ ['rig_id' => 1, 'group' => 'A'], ['rig_id' => 2, 'group' => 'A'] ]
        if (empty($targets)) {
            return array();
        }

        $this->db->select('crews.*, rigs.name as rig_name, rigs.code as rig_code');
        $this->db->from('crews');
        $this->db->join('rigs', 'rigs.id = crews.rig_id', 'left');
        if ($active_only) {
            $this->db->where('crews.is_active', 1);
        }

        $this->db->group_start();
        foreach ($targets as $t) {
            $rig_id = (int)$t['rig_id'];
            $grp = isset($t['group']) ? $t['group'] : 'ALL';
            $this->db->or_group_start();
            $this->db->where('crews.rig_id', $rig_id);
            if ($grp !== 'ALL' && in_array($grp, array('A', 'B', 'C'))) {
                $this->db->where('crews.group_code', $grp);
            }
            $this->db->group_end();
        }
        $this->db->group_end();

        $this->db->order_by('crews.rig_id', 'ASC');
        $this->db->order_by('crews.group_code', 'ASC');
        $this->db->order_by('crews.name', 'ASC');
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
