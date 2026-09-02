<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Meeting_model');
        $this->load->model('Meeting_participant_model');
        $this->load->model('Rig_model');
        $this->load->model('Crew_model');
        $this->load->model('Attendance_model');
    }

    public function index()
    {
        $status = $this->input->get('status', TRUE);
        $rig_id = $this->input->get('rig_id', TRUE);
        $date = $this->input->get('date', TRUE);

        $meetings = $this->Meeting_model->get_meetings_detailed($status, $rig_id, $date);
        $rigs = $this->Rig_model->get_all();

        $data = array(
            'title'          => 'Jadwal Meeting Rig - Besmindo Reminder',
            'meetings'       => $meetings,
            'rigs'           => $rigs,
            'selectedStatus' => $status,
            'selectedRig'    => $rig_id,
            'selectedDate'   => $date
        );

        $this->render_template('meeting/index', $data);
    }

    public function create()
    {
        $rigs = $this->Rig_model->get_all();
        $crews = $this->Crew_model->get_crews_with_rig(null, 1);

        $data = array(
            'title' => 'Buat Jadwal Meeting Baru - Besmindo Reminder',
            'rigs'  => $rigs,
            'crews' => $crews
        );

        $this->render_template('meeting/create', $data);
    }

    public function edit($id)
    {
        $meeting = $this->Meeting_model->get_meeting_detail($id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('meeting');
        }

        $rigs = $this->Rig_model->get_all();
        $crews = $this->Crew_model->get_crews_with_rig($meeting['rig_id'], 1);
        $participants = $this->db->get_where('meeting_participants', array('meeting_id' => $id))->result_array();
        $participant_ids = array_column($participants, 'crew_id');

        $data = array(
            'title'          => 'Ubah / Reschedule Jadwal Meeting - Besmindo Reminder',
            'meeting'        => $meeting,
            'rigs'           => $rigs,
            'crews'          => $crews,
            'participantIds' => $participant_ids
        );

        $this->render_template('meeting/edit', $data);
    }

    public function detail($id)
    {
        $meeting = $this->Meeting_model->get_meeting_detail($id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('meeting');
        }

        $participants = $this->Meeting_participant_model->get_participants_by_meeting($id);
        $attendances = $this->Attendance_model->get_meeting_attendances($id);
        $attendance_stats = $this->Attendance_model->get_attendance_stats($id);

        $data = array(
            'title'           => 'Detail Meeting: ' . $meeting['title'],
            'meeting'         => $meeting,
            'participants'    => $participants,
            'attendances'     => $attendances,
            'attendanceStats' => $attendance_stats
        );

        $this->render_template('meeting/detail', $data);
    }

    public function save()
    {
        $id = $this->input->post('id', TRUE);
        $title = trim($this->input->post('title', TRUE));
        $topic = trim($this->input->post('topic', TRUE));
        $rig_id = (int)$this->input->post('rig_id', TRUE);
        $pj_crew_id = (int)$this->input->post('pj_crew_id', TRUE);
        $meeting_date = $this->input->post('meeting_date', TRUE);
        $start_time = $this->input->post('start_time', TRUE);
        $end_time = $this->input->post('end_time', TRUE);
        $teams_link = trim($this->input->post('teams_link', TRUE));
        $is_recurring = $this->input->post('is_recurring') ? 1 : 0;
        $recurring_day = $is_recurring ? date('l', strtotime($meeting_date)) : null;
        $status = $this->input->post('status', TRUE) ?: 'scheduled';
        $selected_crews = $this->input->post('crew_ids') ?: array();

        if (empty($title) || empty($rig_id) || empty($meeting_date) || empty($start_time) || empty($end_time)) {
            $this->session->set_flashdata('error', 'Judul Meeting, Rig, Tanggal, Jam Mulai dan Jam Selesai wajib diisi.');
            redirect('meeting/create');
        }

        if (empty($teams_link)) {
            $teams_link = 'https://teams.microsoft.com/l/meetup-join/19%3ameeting_' . url_title($title, '_', TRUE) . '%40thread.v2/0';
        }

        $data = array(
            'title'         => $title,
            'topic'         => $topic,
            'rig_id'        => $rig_id,
            'pj_crew_id'    => $pj_crew_id ?: null,
            'meeting_date'  => $meeting_date,
            'start_time'    => $start_time,
            'end_time'      => $end_time,
            'teams_link'    => $teams_link,
            'is_recurring'  => $is_recurring,
            'recurring_day' => $recurring_day,
            'status'        => $status
        );

        if (!empty($id)) {
            $this->Meeting_model->update($id, $data);
            $meeting_id = $id;
            $msg = 'Jadwal meeting berhasil diperbarui / di-reschedule.';
        } else {
            $meeting_id = $this->Meeting_model->insert($data);
            $msg = 'Jadwal meeting baru berhasil dibuat.';
        }

        if (empty($selected_crews)) {
            $rig_crews = $this->Crew_model->get_by_rig($rig_id);
            $selected_crews = array_column($rig_crews, 'id');
        }

        $this->Meeting_participant_model->sync_participants($meeting_id, $selected_crews);
        $this->Attendance_model->init_meeting_attendances($meeting_id);

        $this->session->set_flashdata('success', $msg);
        redirect('meeting/detail/' . $meeting_id);
    }

    public function quick_reschedule()
    {
        $id = (int)$this->input->post('meeting_id', TRUE);
        $meeting_date = $this->input->post('meeting_date', TRUE);
        $start_time = $this->input->post('start_time', TRUE);
        $end_time = $this->input->post('end_time', TRUE);

        if (!$id || !$meeting_date || !$start_time || !$end_time) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Parameter tanggal dan waktu tidak lengkap.')));
        }

        $this->Meeting_model->update($id, array(
            'meeting_date' => $meeting_date,
            'start_time'   => $start_time,
            'end_time'     => $end_time,
            'status'       => 'scheduled'
        ));

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('success' => true, 'message' => 'Jadwal meeting berhasil diubah secara instan!')));
    }

    public function update_status($id, $new_status)
    {
        $valid = array('scheduled', 'in_progress', 'completed', 'cancelled');
        if (in_array($new_status, $valid)) {
            $this->Meeting_model->update($id, array('status' => $new_status));
            $this->session->set_flashdata('success', "Status meeting diubah menjadi {$new_status}.");
        }
        redirect('meeting/detail/' . $id);
    }

    public function delete($id)
    {
        $meeting = $this->Meeting_model->get_by_id($id);
        if ($meeting) {
            $this->Meeting_model->delete($id);
            $this->session->set_flashdata('success', "Meeting '{$meeting['title']}' berhasil dihapus.");
        }
        redirect('meeting');
    }

    public function get_crews_by_rig_json($rig_id)
    {
        $crews = $this->Crew_model->get_by_rig($rig_id);
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($crews));
    }
}
