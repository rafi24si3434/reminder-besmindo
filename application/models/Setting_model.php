<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model
{
    protected $table = 'app_settings';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_val($key, $default = '')
    {
        $res = $this->db->get_where($this->table, array('setting_key' => $key))->row_array();
        return $res ? $res['setting_value'] : $default;
    }

    public function set_val($key, $value)
    {
        $existing = $this->db->get_where($this->table, array('setting_key' => $key))->row_array();
        if ($existing) {
            $this->db->where('id', $existing['id']);
            return $this->db->update($this->table, array('setting_value' => $value));
        }
        return $this->db->insert($this->table, array('setting_key' => $key, 'setting_value' => $value));
    }
}
