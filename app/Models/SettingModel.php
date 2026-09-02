<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'app_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['setting_key', 'setting_value', 'updated_at'];

    public function getVal(string $key, string $default = '')
    {
        $res = $this->where('setting_key', $key)->first();
        return $res ? $res['setting_value'] : $default;
    }

    public function setVal(string $key, string $value)
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            return $this->update($existing['id'], ['setting_value' => $value]);
        }
        return $this->insert(['setting_key' => $key, 'setting_value' => $value]);
    }
}
