<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crew extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Crew_model');
        $this->load->model('Rig_model');
    }

    public function index()
    {
        $rig_id = $this->input->get('rig_id', TRUE);
        $active_only = $this->input->get('active', TRUE);

        $crews = $this->Crew_model->get_crews_with_rig($rig_id, $active_only);
        $rigs = $this->Rig_model->get_all();

        $data = array(
            'title'        => 'Manajemen Crew Rig - Besmindo Reminder',
            'crews'        => $crews,
            'rigs'         => $rigs,
            'selectedRig'  => $rig_id,
            'activeFilter' => $active_only
        );

        $this->render_template('crew/index', $data);
    }

    public function create()
    {
        $rigs = $this->Rig_model->get_all();
        $data = array(
            'title' => 'Tambah Crew Baru - Besmindo Reminder',
            'rigs'  => $rigs,
            'crew'  => null
        );
        $this->render_template('crew/form', $data);
    }

    public function edit($id)
    {
        $crew = $this->Crew_model->get_by_id($id);
        if (!$crew) {
            $this->session->set_flashdata('error', 'Data Crew tidak ditemukan.');
            redirect('crew');
        }

        $rigs = $this->Rig_model->get_all();
        $data = array(
            'title' => 'Ubah Data Crew - Besmindo Reminder',
            'rigs'  => $rigs,
            'crew'  => $crew
        );
        $this->render_template('crew/form', $data);
    }

    public function save()
    {
        $id = $this->input->post('id', TRUE);
        $nik = trim($this->input->post('nik', TRUE));
        $name = trim($this->input->post('name', TRUE));
        $position = trim($this->input->post('position', TRUE));
        $rig_id = (int)$this->input->post('rig_id', TRUE);
        $phone = trim($this->input->post('phone', TRUE));
        $email = trim($this->input->post('email', TRUE));
        $is_active = $this->input->post('is_active') ? 1 : 0;

        if (empty($nik) || empty($name) || empty($position) || empty($rig_id) || empty($phone)) {
            $this->session->set_flashdata('error', 'NIK, Nama, Jabatan, Rig, dan No. WhatsApp wajib diisi.');
            redirect('crew/create');
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $data = array(
            'nik'       => $nik,
            'name'      => $name,
            'position'  => $position,
            'rig_id'    => $rig_id,
            'phone'     => $phone,
            'email'     => $email,
            'is_active' => $is_active
        );

        if (!empty($id)) {
            $this->Crew_model->update($id, $data);
            $this->session->set_flashdata('success', 'Data crew berhasil diperbarui.');
        } else {
            $this->Crew_model->insert($data);
            $this->session->set_flashdata('success', 'Crew baru berhasil ditambahkan.');
        }

        redirect('crew');
    }

    public function toggle_status($id)
    {
        $crew = $this->Crew_model->get_by_id($id);
        if ($crew) {
            $new_status = $crew['is_active'] ? 0 : 1;
            $this->Crew_model->update($id, array('is_active' => $new_status));
            $text = $new_status ? 'diaktifkan' : 'dinonaktifkan';
            $this->session->set_flashdata('success', "Status Crew {$crew['name']} berhasil {$text}.");
        }
        redirect('crew');
    }

    public function delete($id)
    {
        $crew = $this->Crew_model->get_by_id($id);
        if ($crew) {
            $this->Crew_model->delete($id);
            $this->session->set_flashdata('success', "Data Crew {$crew['name']} berhasil dihapus.");
        }
        redirect('crew');
    }
}
