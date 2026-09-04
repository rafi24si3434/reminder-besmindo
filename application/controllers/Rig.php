<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rig extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rig_model');
    }

    public function index()
    {
        $rigs = $this->Rig_model->get_rig_with_crew_count();
        $data = array(
            'title' => 'Master Data Rig - Besmindo Reminder',
            'rigs'  => $rigs
        );
        $this->render_template('rig/index', $data);
    }

    public function create()
    {
        $data = array(
            'title' => 'Tambah Unit Rig Baru - Besmindo Reminder',
            'rig'   => null
        );
        $this->render_template('rig/form', $data);
    }

    public function edit($id)
    {
        $rig = $this->Rig_model->get_by_id($id);
        if (!$rig) {
            $this->session->set_flashdata('error', 'Data Rig tidak ditemukan.');
            redirect('rig');
        }

        $data = array(
            'title' => 'Ubah Data Rig - Besmindo Reminder',
            'rig'   => $rig
        );
        $this->render_template('rig/form', $data);
    }

    public function save()
    {
        $id = $this->input->post('id', TRUE);
        $name = trim($this->input->post('name', TRUE));
        $code = trim($this->input->post('code', TRUE));
        $location = trim($this->input->post('location', TRUE));
        $description = trim($this->input->post('description', TRUE));
        $pj_name = trim($this->input->post('pj_name', TRUE));
        $wa_group_id = trim($this->input->post('wa_group_id', TRUE));
        $wa_group_name = trim($this->input->post('wa_group_name', TRUE));
        $is_active = $this->input->post('is_active') ? 1 : 0;

        if (empty($name) || empty($code) || empty($location) || empty($pj_name) || empty($pj_phone)) {
            $this->session->set_flashdata('error', 'Nama Rig, Kode, Lokasi, Nama PJ, dan No WA PJ wajib diisi.');
            redirect('rig/create');
        }

        $data = array(
            'name'          => $name,
            'code'          => $code,
            'location'      => $location,
            'wa_group_id'   => $wa_group_id,
            'wa_group_name' => $wa_group_name,
            'description'   => $description,
            'pj_name'       => $pj_name,
            'pj_phone'      => $pj_phone,
            'is_active'     => $is_active
        );

        if (!empty($id)) {
            $this->Rig_model->update($id, $data);
            $this->session->set_flashdata('success', 'Data Rig berhasil diperbarui.');
        } else {
            $this->Rig_model->insert($data);
            $this->session->set_flashdata('success', 'Unit Rig baru berhasil didaftarkan.');
        }

        redirect('rig');
    }


    public function delete($id)
    {
        $rig = $this->Rig_model->get_by_id($id);
        if ($rig) {
            $this->Rig_model->delete($id);
            $this->session->set_flashdata('success', "Data Rig {$rig['name']} berhasil dihapus.");
        }
        redirect('rig');
    }
}
