<?php

namespace App\Controllers;

use App\Models\CrewModel;
use App\Models\RigModel;
use CodeIgniter\Controller;

class Crew extends Controller
{
    protected $crewModel;
    protected $rigModel;

    public function __construct()
    {
        $this->crewModel = new CrewModel();
        $this->rigModel = new RigModel();
    }

    public function index()
    {
        $rigId = $this->request->getGet('rig_id');
        $activeOnly = $this->request->getGet('active');

        $crews = $this->crewModel->getCrewsWithRig(
            $rigId ? (int)$rigId : null,
            $activeOnly !== null && $activeOnly !== '' ? (int)$activeOnly : null
        );

        $rigs = $this->rigModel->getActiveRigs();

        return view('crew/index', [
            'title'      => 'Manajemen Crew Rig - Besmindo Reminder',
            'crews'      => $crews,
            'rigs'       => $rigs,
            'selectedRig'=> $rigId,
            'activeFilter' => $activeOnly
        ]);
    }

    public function create()
    {
        $rigs = $this->rigModel->getActiveRigs();

        return view('crew/form', [
            'title' => 'Tambah Crew Baru - Besmindo Reminder',
            'rigs'  => $rigs,
            'crew'  => null
        ]);
    }

    public function edit(int $id)
    {
        $crew = $this->crewModel->find($id);
        if (!$crew) {
            return redirect()->to(base_url('crew'))->with('error', 'Data Crew tidak ditemukan.');
        }

        $rigs = $this->rigModel->getActiveRigs();

        return view('crew/form', [
            'title' => 'Ubah Data Crew - Besmindo Reminder',
            'rigs'  => $rigs,
            'crew'  => $crew
        ]);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        $nik = trim($this->request->getPost('nik') ?? '');
        $name = trim($this->request->getPost('name') ?? '');
        $position = trim($this->request->getPost('position') ?? '');
        $rigId = (int)$this->request->getPost('rig_id');
        $phone = trim($this->request->getPost('phone') ?? '');
        $email = trim($this->request->getPost('email') ?? '');
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($nik) || empty($name) || empty($position) || empty($rigId) || empty($phone)) {
            return redirect()->back()->withInput()->with('error', 'NIK, Nama, Jabatan, Rig, dan No. WhatsApp wajib diisi.');
        }

        // Format phone to standard 62xxx
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $data = [
            'nik'       => $nik,
            'name'      => $name,
            'position'  => $position,
            'rig_id'    => $rigId,
            'phone'     => $phone,
            'email'     => $email,
            'is_active' => $isActive
        ];

        if (!empty($id)) {
            $this->crewModel->update($id, $data);
            $msg = 'Data crew berhasil diperbarui.';
        } else {
            // Check unique NIK
            $exist = $this->crewModel->where('nik', $nik)->first();
            if ($exist) {
                return redirect()->back()->withInput()->with('error', 'NIK Crew sudah terdaftar dalam sistem.');
            }
            $this->crewModel->insert($data);
            $msg = 'Crew baru berhasil ditambahkan.';
        }

        return redirect()->to(base_url('crew'))->with('success', $msg);
    }

    public function toggleStatus(int $id)
    {
        $crew = $this->crewModel->find($id);
        if ($crew) {
            $newStatus = $crew['is_active'] ? 0 : 1;
            $this->crewModel->update($id, ['is_active' => $newStatus]);
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->to(base_url('crew'))->with('success', "Status Crew {$crew['name']} berhasil {$statusText}.");
        }
        return redirect()->to(base_url('crew'))->with('error', 'Data Crew tidak ditemukan.');
    }

    public function delete(int $id)
    {
        $crew = $this->crewModel->find($id);
        if ($crew) {
            $this->crewModel->delete($id);
            return redirect()->to(base_url('crew'))->with('success', "Data Crew {$crew['name']} berhasil dihapus.");
        }
        return redirect()->to(base_url('crew'))->with('error', 'Data Crew tidak ditemukan.');
    }
}
