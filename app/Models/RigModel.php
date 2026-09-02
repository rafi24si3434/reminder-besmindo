<?php

namespace App\Models;

use CodeIgniter\Model;

class RigModel extends Model
{
    protected $table            = 'rigs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'code', 'location', 'description', 'pj_name', 'pj_phone', 'is_active'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveRigs()
    {
        return $this->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
    }

    public function getRigWithCrewCount()
    {
        return $this->select('rigs.*, COUNT(crews.id) as total_crew')
                    ->join('crews', 'crews.rig_id = rigs.id AND crews.is_active = 1', 'left')
                    ->groupBy('rigs.id')
                    ->orderBy('rigs.name', 'ASC')
                    ->findAll();
    }
}
