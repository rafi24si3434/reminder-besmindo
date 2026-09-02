<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Meeting_model');
        $this->load->model('Meeting_participant_model');
        $this->load->model('Crew_model');
        $this->load->model('Attendance_model');
        $this->load->model('Reminder_model');
        $this->load->model('Setting_model');
        $this->load->library('whatsapp_service');
    }

    public function live($meeting_id = NULL)
    {
        $meetings = $this->Meeting_model->get_meetings_detailed();
        if (empty($meetings)) {
            $this->session->set_flashdata('error', 'Belum ada jadwal meeting.');
            redirect('meeting');
        }

        if (!$meeting_id) {
            $today = date('Y-m-d');
            $in_progress = array_filter($meetings, function($m) { return $m['status'] === 'in_progress'; });
            if (!empty($in_progress)) {
                $first = reset($in_progress);
                $meeting_id = $first['id'];
            } else {
                $today_m = array_filter($meetings, function($m) use ($today) { return $m['meeting_date'] === $today; });
                $meeting_id = !empty($today_m) ? reset($today_m)['id'] : $meetings[0]['id'];
            }
        }

        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('meeting');
        }

        $this->Attendance_model->init_meeting_attendances($meeting_id);
        $attendances = $this->Attendance_model->get_meeting_attendances($meeting_id);
        $stats = $this->Attendance_model->get_attendance_stats($meeting_id);

        $data = array(
            'title'       => 'Live Attendance Microsoft Teams: ' . $meeting['title'],
            'meetings'    => $meetings,
            'meeting'     => $meeting,
            'attendances' => $attendances,
            'stats'       => $stats
        );

        $this->render_template('attendance/live', $data);
    }

    public function simulator()
    {
        $meetings = $this->Meeting_model->get_meetings_detailed();
        $selected_meeting_id = $this->input->get('meeting_id', TRUE);
        if (!$selected_meeting_id && !empty($meetings)) {
            $selected_meeting_id = $meetings[0]['id'];
        }

        $meeting = null;
        $attendances = array();
        if ($selected_meeting_id) {
            $meeting = $this->Meeting_model->get_meeting_detail((int)$selected_meeting_id);
            $this->Attendance_model->init_meeting_attendances((int)$selected_meeting_id);
            $attendances = $this->Attendance_model->get_meeting_attendances((int)$selected_meeting_id);
        }

        $data = array(
            'title'       => 'Microsoft Teams Attendance Simulator & CSV Import',
            'meetings'    => $meetings,
            'meeting'     => $meeting,
            'attendances' => $attendances
        );

        $this->render_template('attendance/simulator', $data);
    }

    public function simulate_join()
    {
        $attendance_id = (int)$this->input->post('attendance_id', TRUE);
        $join_type = $this->input->post('join_type', TRUE) ?: 'on_time';

        $attendance = $this->Attendance_model->get_by_id($attendance_id);
        if (!$attendance) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Data kehadiran tidak ditemukan.')));
        }

        $meeting = $this->Meeting_model->get_by_id($attendance['meeting_id']);
        $meeting_date = $meeting['meeting_date'];
        $start_time = $meeting['start_time'];

        if ($join_type === 'late') {
            $join_time = date('Y-m-d H:i:s', strtotime("{$meeting_date} {$start_time} +20 minutes"));
            $status = 'TERLAMBAT';
            $duration = 40;
        } else {
            $join_time = date('Y-m-d H:i:s', strtotime("{$meeting_date} {$start_time} -2 minutes"));
            $status = 'HADIR';
            $duration = 60;
        }

        $this->Attendance_model->update($attendance_id, array(
            'status'           => $status,
            'join_time'        => $join_time,
            'duration_minutes' => $duration,
            'source'           => 'TEAMS_SIMULATOR'
        ));

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'   => true,
                'status'    => $status,
                'join_time' => $join_time,
                'message'   => "Status kehadiran berhasil diupdate: {$status}"
            )));
    }

    public function update_status_manual()
    {
        $attendance_id = (int)$this->input->post('attendance_id', TRUE);
        $status = $this->input->post('status', TRUE);
        $notes = trim($this->input->post('notes', TRUE));

        $valid = array('HADIR', 'TERLAMBAT', 'BELUM_HADIR', 'TIDAK_HADIR', 'IZIN');
        if (!in_array($status, $valid)) {
            $this->session->set_flashdata('error', 'Status tidak valid.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->Attendance_model->update($attendance_id, array(
            'status' => $status,
            'notes'  => $notes,
            'source' => 'MANUAL'
        ));

        $this->session->set_flashdata('success', 'Status kehadiran berhasil disesuaikan secara manual.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function remind_not_present($meeting_id)
    {
        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('attendance/live');
        }

        $this->db->select('attendances.*, crews.name, crews.phone, crews.position');
        $this->db->from('attendances');
        $this->db->join('crews', 'crews.id = attendances.crew_id');
        $this->db->where('attendances.meeting_id', $meeting_id);
        $this->db->where_in('attendances.status', array('BELUM_HADIR', 'TIDAK_HADIR'));
        $attendances = $this->db->get()->result_array();

        if (empty($attendances)) {
            $this->session->set_flashdata('success', 'Semua crew sudah hadir di ruang meeting!');
            redirect('attendance/live/' . $meeting_id);
        }

        $count = 0;
        foreach ($attendances as $att) {
            $msg = "*[PENTING: REMINDER KEHADIRAN MEETING]*\n\n"
                 . "Halo *{$att['name']}* ({$att['position']}),\n"
                 . "Meeting rutin *{$meeting['title']}* telah dimulai saat ini.\n"
                 . "Anda tercatat *BELUM HADIR* di Microsoft Teams.\n\n"
                 . "Mohon segera bergabung sekarang melalui tautan:\n"
                 . "👉 {$meeting['teams_link']}\n\n"
                 . "Terima kasih.";

            $sendRes = $this->whatsapp_service->send_message($att['phone'], $msg);
            $status = ($sendRes['success']) ? 'sent' : 'failed';

            $this->Reminder_model->insert(array(
                'meeting_id'    => $meeting_id,
                'crew_id'       => $att['crew_id'],
                'reminder_type' => 'NOT_PRESENT_REMINDER',
                'target_phone'  => $att['phone'],
                'message_body'  => $msg,
                'status'        => $status,
                'sent_at'       => date('Y-m-d H:i:s')
            ));
            if ($sendRes['success']) {
                $count++;
            }
        }

        $this->session->set_flashdata('success', "Berhasil mengirim reminder WhatsApp ke {$count} crew yang belum hadir dari nomor pengirim 085148410891!");
        redirect('attendance/live/' . $meeting_id);
    }

    public function import_csv()
    {
        $meeting_id = (int)$this->input->post('meeting_id', TRUE);
        
        if (!$meeting_id || empty($_FILES['teams_csv']['tmp_name'])) {
            $this->session->set_flashdata('error', 'Silakan pilih meeting dan unggah file CSV Microsoft Teams Attendance.');
            redirect('attendance/simulator');
        }

        $meeting = $this->Meeting_model->get_by_id($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('attendance/simulator');
        }

        $handle = fopen($_FILES['teams_csv']['tmp_name'], 'r');
        $imported_count = 0;

        if ($handle !== FALSE) {
            $header = fgetcsv($handle, 1000, ',');
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                if (count($data) >= 1) {
                    $name = trim($data[0]);
                    if (empty($name)) continue;

                    $crew = $this->db->like('name', $name)->get('crews')->row_array();
                    if ($crew) {
                        $status = (isset($data[1]) && strtotime($data[1]) > strtotime($meeting['meeting_date'] . ' ' . $meeting['start_time'] . ' +10 minutes')) ? 'TERLAMBAT' : 'HADIR';
                        
                        $exist = $this->db->get_where('attendances', array('meeting_id' => $meeting_id, 'crew_id' => $crew['id']))->row_array();
                        if ($exist) {
                            $this->Attendance_model->update($exist['id'], array(
                                'status'           => $status,
                                'join_time'        => date('Y-m-d H:i:s'),
                                'duration_minutes' => 50,
                                'source'           => 'TEAMS_SYNC'
                            ));
                        } else {
                            $this->Attendance_model->insert(array(
                                'meeting_id'       => $meeting_id,
                                'crew_id'          => $crew['id'],
                                'status'           => $status,
                                'join_time'        => date('Y-m-d H:i:s'),
                                'duration_minutes' => 50,
                                'source'           => 'TEAMS_SYNC'
                            ));
                        }
                        $imported_count++;
                    }
                }
            }
            fclose($handle);
        }

        $this->session->set_flashdata('success', "Berhasil mengimpor & mensinkronisasi data kehadiran {$imported_count} crew dari file Microsoft Teams!");
        redirect('attendance/live/' . $meeting_id);
    }
}
