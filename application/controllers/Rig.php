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
            'title' => 'Master Data Rig - Monitoring Pre Hitch Meeting',
            'rigs'  => $rigs
        );
        $this->render_template('rig/index', $data);
    }

    public function create()
    {
        $data = array(
            'title' => 'Tambah Unit Rig Baru - Monitoring Pre Hitch Meeting',
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
            'title' => 'Ubah Data Rig - Monitoring Pre Hitch Meeting',
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
        $pj_phone = trim($this->input->post('pj_phone', TRUE));
        $wa_group_id = trim($this->input->post('wa_group_id', TRUE));
        $wa_group_name = trim($this->input->post('wa_group_name', TRUE));
        $is_active = $this->input->post('is_active') ? 1 : 0;

        $redirect_target = !empty($id) ? 'rig/edit/' . $id : 'rig/create';

        if (empty($name) || empty($code) || empty($location) || empty($pj_name) || empty($pj_phone)) {
            $this->session->set_flashdata('error', 'Nama Rig, Kode, Lokasi, Nama PJ, dan No WA PJ wajib diisi.');
            redirect($redirect_target);
            return;
        }

        // Cek apakah kode rig sudah digunakan oleh Rig lain
        $this->db->where('code', $code);
        if (!empty($id)) {
            $this->db->where('id !=', $id);
        }
        $existing = $this->db->get('rigs')->row_array();
        if ($existing) {
            $this->session->set_flashdata('error', 'Kode Rig "' . htmlspecialchars($code) . '" sudah dipakai oleh rig lain. Gunakan kode yang unik.');
            redirect($redirect_target);
            return;
        }

        // Format dan bersihkan WhatsApp Group ID jika diisi
        if (!empty($wa_group_id)) {
            $wa_group_id = str_replace(' ', '', $wa_group_id);
            // Jika user memasukkan angka saja tanpa akhiran domain, otomatis tambahkan @g.us
            if (strpos($wa_group_id, '@') === false) {
                $wa_group_id = $wa_group_id . '@g.us';
            }
        } else {
            $wa_group_id = null;
        }

        $data = array(
            'name'          => $name,
            'code'          => $code,
            'location'      => $location,
            'wa_group_id'   => $wa_group_id,
            'wa_group_name' => !empty($wa_group_name) ? $wa_group_name : null,
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

    /**
     * AJAX endpoint to fetch participating WhatsApp groups from local Node gateway
     */
    public function api_groups()
    {
        $this->output->set_content_type('application/json');

        $ch = curl_init('http://localhost:3000/groups');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            echo json_encode([
                'success' => false,
                'message' => 'Gateway WhatsApp belum aktif atau belum scan QR di http://localhost:3000.'
            ]);
            return;
        }

        echo $response;
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
