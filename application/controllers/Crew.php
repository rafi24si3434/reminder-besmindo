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

    /**
     * AJAX: Get participating groups for selector
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
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            echo json_encode([
                'success' => false,
                'message' => 'Gateway WhatsApp belum tersambung di http://localhost:3000.'
            ]);
            return;
        }
        echo $response;
    }

    /**
     * AJAX: Get group participants & check which numbers are already registered in crews table
     */
    public function api_group_members()
    {
        $this->output->set_content_type('application/json');
        $groupId = $this->input->get_post('group_id', TRUE);

        if (empty($groupId)) {
            echo json_encode(['success' => false, 'message' => 'Target Group WhatsApp belum dipilih.']);
            return;
        }

        $url = 'http://localhost:3000/group-participants?groupId=' . urlencode($groupId);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengambil peserta grup dari WhatsApp Gateway. Pastikan server aktif dan nomor WhatsApp terhubung.'
            ]);
            return;
        }

        $result = json_decode($response, true);
        if (!$result || empty($result['success'])) {
            echo json_encode($result ?: ['success' => false, 'message' => 'Respons gateway tidak valid.']);
            return;
        }

        // Ambil data seluruh crew yang sudah ada di database untuk dicocokkan
        $existingCrews = $this->db->select('crews.*, rigs.name as rig_name, rigs.code as rig_code')
            ->from('crews')
            ->join('rigs', 'rigs.id = crews.rig_id', 'left')
            ->get()->result_array();

        $crewPhoneMap = [];
        foreach ($existingCrews as $c) {
            $numClean = preg_replace('/[^0-9]/', '', $c['phone']);
            $crewPhoneMap[$numClean] = $c;
            if (substr($numClean, 0, 2) === '62') {
                $crewPhoneMap['0' . substr($numClean, 2)] = $c;
            } elseif (substr($numClean, 0, 1) === '0') {
                $crewPhoneMap['62' . substr($numClean, 1)] = $c;
            }
        }

        $participants = $result['participants'] ?? [];
        $registeredCount = 0;
        $unregisteredCount = 0;

        foreach ($participants as &$p) {
            $checkNum = $p['number'];
            $check08 = $p['phoneFormatted'];

            if (isset($crewPhoneMap[$checkNum]) || isset($crewPhoneMap[$check08])) {
                $found = $crewPhoneMap[$checkNum] ?? $crewPhoneMap[$check08];
                $p['isRegistered'] = true;
                $p['existingCrew'] = [
                    'id'       => $found['id'],
                    'nik'      => $found['nik'],
                    'name'     => $found['name'],
                    'position' => $found['position'],
                    'rig_name' => $found['rig_name'] ?? '-'
                ];
                $registeredCount++;
            } else {
                $p['isRegistered'] = false;
                $p['existingCrew'] = null;
                $unregisteredCount++;
            }
        }

        echo json_encode([
            'success'            => true,
            'groupName'          => $result['groupName'],
            'total'              => count($participants),
            'registeredCount'    => $registeredCount,
            'unregisteredCount'  => $unregisteredCount,
            'participants'       => $participants
        ]);
    }

    /**
     * AJAX / POST: Save batch imported crew members
     */
    public function save_batch_imported()
    {
        $this->output->set_content_type('application/json');
        
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        $items = $payload['crews'] ?? [];

        if (empty($items) || !is_array($items)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada data crew yang dikirim.']);
            return;
        }

        $inserted = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $name = trim($item['name'] ?? '');
            $phone = trim($item['phone'] ?? '');
            $position = trim($item['position'] ?? 'Crew Lapangan');
            $rig_id = (int)($item['rig_id'] ?? 0);
            $nik = trim($item['nik'] ?? '');

            if (empty($name) || empty($phone) || empty($rig_id)) {
                $skipped++;
                continue;
            }

            // Normalisasi nomor HP ke format 62
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($cleanPhone, 0, 1) === '0') {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }

            // Jika NIK kosong, auto generate NIK unik
            if (empty($nik)) {
                $nik = 'CRW-' . substr($cleanPhone, -4) . '-' . mt_rand(100, 999);
            }

            // Cek duplikasi NIK
            $dupNik = $this->db->get_where('crews', ['nik' => $nik])->row_array();
            if ($dupNik) {
                $nik = 'CRW-' . substr($cleanPhone, -4) . '-' . mt_rand(1000, 9999);
            }

            // Cek duplikasi nomor telepon
            $dupPhone = $this->db->get_where('crews', ['phone' => $cleanPhone])->row_array();
            if ($dupPhone) {
                $skipped++;
                continue;
            }

            $data = [
                'nik'       => $nik,
                'name'      => $name,
                'position'  => $position,
                'rig_id'    => $rig_id,
                'phone'     => $cleanPhone,
                'email'     => null,
                'is_active' => 1
            ];

            $this->Crew_model->insert($data);
            $inserted++;
        }

        $this->session->set_flashdata('success', "Berhasil mengimpor {$inserted} Crew baru dari grup WhatsApp.");

        echo json_encode([
            'success'  => true,
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'message'  => "Berhasil mengimpor {$inserted} personil baru ke Master Data Crew."
        ]);
    }
}
