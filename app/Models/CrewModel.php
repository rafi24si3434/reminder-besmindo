<?php

namespace App\Models;

use CodeIgniter\Model;

class CrewModel extends Model
{
    protected $table            = 'crews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nik', 'name', 'position', 'rig_id', 'phone', 'email', 'is_active'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getCrewsWithRig(?int $rigId = null, ?int $activeOnly = null)
    {
        $builder = $this->select('crews.*, rigs.name as rig_name, rigs.code as rig_code')
                        ->join('rigs', 'rigs.id = crews.rig_id', 'left');

        if ($rigId !== null && $rigId > 0) {
            $builder->where('crews.rig_id', $rigId);
        }

        if ($activeOnly !== null) {
            $builder->where('crews.is_active', $activeOnly);
        }

        return $builder->orderBy('crews.rig_id', 'ASC')
                       ->orderBy('crews.name', 'ASC')
                       ->findAll();
    }

    public function getCrewsByRig(int $rigId)
    {
        return $this->where('rig_id', $rigId)->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
    }
}
