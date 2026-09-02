<?php

namespace App\Controllers;

use App\Models\RigModel;
use CodeIgniter\Controller;

class Rig extends Controller
{
    protected $rigModel;

    public function __construct()
    {
        $this->rigModel = new RigModel();
    }

    public function index()
    {
        $rigs = $this->rigModel->getRigWithCrewCount();

        return view('rig/index', [
            'title' => 'Master Data Rig - Besmindo Reminder',
            'rigs'  => $rigs
        ]);
    }

    public function create()
    {
        return view('rig/form', [
            'title' => 'Tambah Unit Rig Baru - Besmindo Reminder',
            'rig'   => null
        ]);
    }

    public function edit(int $id)
    {
        $rig = $this->rigModel->find($id);
        if (!$rig) {
            return redirect()->to(base_url('rig'))->with('error', 'Data Rig tidak ditemukan.');
        }

        return view('rig/form', [
            'title' => 'Ubah Data Rig - Besmindo Reminder',
            'rig'   => $rig
        ]);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        $name = trim($this->request->getPost('name') ?? '');
        $code = trim($this->request->getPost('code') ?? '');
        $location = trim($this->request->getPost('location') ?? '');
        $description = trim($this->request->getPost('description') ?? '');
        $pjName = trim($this->request->getPost('pj_name') ?? '');
        $pjPhone = trim($this->request->getPost('pj_phone') ?? '');
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($name) || empty($code) || empty($location) || empty($pjName) || empty($pjPhone)) {
            return redirect()->back()->withInput()->with('error', 'Nama Rig, Kode, Lokasi, Nama PJ, dan No WA PJ wajib diisi.');
        }

        $data = [
            'name'        => $name,
            'code'        => $code,
            'location'    => $location,
            'description' => $description,
            'pj_name'     => $pjName,
            'pj_phone'    => $pjPhone,
            'is_active'   => $isActive
        ];

        if (!empty($id)) {
            $this->rigModel->update($id, $data);
            $msg = 'Data Rig berhasil diperbarui.';
        } else {
            $exist = $this->rigModel->where('code', $code)->first();
            if ($exist) {
                return redirect()->back()->withInput()->with('error', 'Kode Rig sudah digunakan.');
            }
            $this->rigModel->insert($data);
            $msg = 'Unit Rig baru berhasil didaftarkan.';
        }

        return redirect()->to(base_url('rig'))->with('success', $msg);
    }

    public function delete(int $id)
    {
        $rig = $this->rigModel->find($id);
        if ($rig) {
            $this->rigModel->delete($id);
            return redirect()->to(base_url('rig'))->with('success', "Data Rig {$rig['name']} berhasil dihapus.");
        }
        return redirect()->to(base_url('rig'))->with('error', 'Data Rig tidak ditemukan.');
    }
}
