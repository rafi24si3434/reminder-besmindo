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

    public function close_session()
    {
        $meeting_id         = (int)$this->input->post('meeting_id', TRUE);
        $meeting_notes      = trim($this->input->post('meeting_notes', TRUE));
        $broadcast_recap_wa = (int)$this->input->post('broadcast_recap_wa', TRUE);

        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('attendance/live');
        }

        // 1. Eksekusi penutupan sesi & otomatisasi BELUM_HADIR -> TIDAK_HADIR
        $this->Attendance_model->close_meeting_session($meeting_id, $meeting_notes);

        // 2. Ambil data statistik absensi final
        $stats = $this->Attendance_model->get_attendance_stats($meeting_id);
        $attendances = $this->Attendance_model->get_meeting_attendances($meeting_id);

        $waFeedback = '';
        // 3. Kirim Rekap ke WhatsApp Group Rig jika dicentang
        if ($broadcast_recap_wa == 1) {
            if (!empty($meeting['wa_group_id'])) {
                $recapMsg = $this->build_recap_message($meeting, $stats, $attendances, $meeting_notes);
                $resWA = $this->whatsapp_service->send_to_group($meeting['wa_group_id'], $recapMsg);

                $this->Reminder_model->insert(array(
                    'meeting_id'    => $meeting_id,
                    'crew_id'       => NULL,
                    'reminder_type' => 'UNDANGAN',
                    'target_phone'  => $meeting['wa_group_id'],
                    'message_body'  => $recapMsg,
                    'status'        => (!empty($resWA['success'])) ? 'sent' : 'failed',
                    'sent_at'       => date('Y-m-d H:i:s')
                ));

                if (!empty($resWA['success'])) {
                    $waFeedback = ' dan ringkasan absensi berhasil dibroadcast ke WhatsApp Group Rig';
                }
            }
        }

        $this->session->set_flashdata('success', "Sesi rapat [{$meeting['title']}] telah resmi DITUTUP{$waFeedback}. Seluruh data absensi telah dikunci dan direkap.");
        redirect('attendance/rekap/' . $meeting_id);
    }

    public function reopen_session($meeting_id)
    {
        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('attendance/live');
        }

        $this->Attendance_model->reopen_meeting_session($meeting_id);
        $this->session->set_flashdata('success', "Sesi rapat [{$meeting['title']}] telah dibuka kembali. Anda dapat memperbarui data absensi.");
        redirect('attendance/rekap/' . $meeting_id);
    }

    public function update_notes()
    {
        $meeting_id    = (int)$this->input->post('meeting_id', TRUE);
        $meeting_notes = trim($this->input->post('meeting_notes', TRUE));

        $this->Attendance_model->update_meeting_notes($meeting_id, $meeting_notes);
        $this->session->set_flashdata('success', 'Catatan / Notulensi rapat berhasil diperbarui.');
        redirect('attendance/rekap/' . $meeting_id);
    }

    public function rekap($meeting_id = NULL)
    {
        if (!$meeting_id) {
            $latest = $this->db->order_by('id', 'DESC')->get('meetings')->row_array();
            if ($latest) {
                redirect('attendance/rekap/' . $latest['id']);
            } else {
                redirect('meeting');
            }
        }

        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            $this->session->set_flashdata('error', 'Meeting tidak ditemukan.');
            redirect('meeting');
        }

        $this->Attendance_model->init_meeting_attendances($meeting_id);
        $attendances = $this->Attendance_model->get_meeting_attendances($meeting_id);
        $stats       = $this->Attendance_model->get_attendance_stats($meeting_id);
        $meetings    = $this->Meeting_model->get_meetings_detailed();

        $data = array(
            'title'       => 'Rekap Absensi & Hasil Rapat: ' . $meeting['title'],
            'meeting'     => $meeting,
            'attendances' => $attendances,
            'stats'       => $stats,
            'meetings'    => $meetings
        );

        $this->render_template('attendance/rekap', $data);
    }

    public function print_rekap($meeting_id)
    {
        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            show_404();
        }

        $attendances = $this->Attendance_model->get_meeting_attendances($meeting_id);
        $stats       = $this->Attendance_model->get_attendance_stats($meeting_id);

        $this->load->view('attendance/print_rekap', array(
            'title'       => 'Laporan Rekap Absensi Meeting: ' . $meeting['title'],
            'meeting'     => $meeting,
            'attendances' => $attendances,
            'stats'       => $stats
        ));
    }

    public function ajax_send_recap_wa()
    {
        $meeting_id   = (int)$this->input->post('meeting_id', TRUE);
        $target_group = trim($this->input->post('target_group', TRUE));

        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'success' => false,
                'message' => 'Meeting tidak ditemukan.'
            )));
        }

        $target = !empty($target_group) ? $target_group : $meeting['wa_group_id'];
        if (empty($target)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'success' => false,
                'message' => 'ID WhatsApp Group Rig belum diatur di Master Rig.'
            )));
        }

        $stats       = $this->Attendance_model->get_attendance_stats($meeting_id);
        $attendances = $this->Attendance_model->get_meeting_attendances($meeting_id);
        $msg         = $this->build_recap_message($meeting, $stats, $attendances, $meeting['meeting_notes']);

        $res = $this->whatsapp_service->send_to_group($target, $msg);

        if (!empty($res['success'])) {
            $this->Reminder_model->insert(array(
                'meeting_id'    => $meeting_id,
                'crew_id'       => NULL,
                'reminder_type' => 'UNDANGAN',
                'target_phone'  => $target,
                'message_body'  => $msg,
                'status'        => 'sent',
                'sent_at'       => date('Y-m-d H:i:s')
            ));
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode($res));
    }

    private function build_recap_message($meeting, $stats, $attendances, $notes = '')
    {
        $tanggal = date('d M Y', strtotime($meeting['meeting_date']));
        $jam     = substr($meeting['start_time'], 0, 5) . ' - ' . substr($meeting['end_time'], 0, 5) . ' WIB';
        $pj      = !empty($meeting['pj_name']) ? $meeting['pj_name'] : 'Management Besmindo';
        $refCode = 'BSM-' . strtoupper(substr(md5($meeting['id'] . microtime()), 0, 6));

        $hadirList = array();
        $absenList = array();

        foreach ($attendances as $att) {
            if ($att['status'] === 'HADIR') {
                $hadirList[] = "• {$att['crew_name']} ({$att['position']})";
            } elseif ($att['status'] === 'TERLAMBAT') {
                $hadirList[] = "• {$att['crew_name']} ({$att['position']}) [Terlambat]";
            } elseif ($att['status'] === 'IZIN') {
                $absenList[] = "• {$att['crew_name']} ({$att['position']}) [Izin]";
            } else {
                $absenList[] = "• {$att['crew_name']} ({$att['position']}) [Tidak Hadir]";
            }
        }

        $hadirStr = !empty($hadirList) ? implode("\n", array_slice($hadirList, 0, 15)) : "- Tidak ada data -";
        if (count($hadirList) > 15) {
            $hadirStr .= "\n...dan " . (count($hadirList) - 15) . " personil lainnya.";
        }

        $absenStr = !empty($absenList) ? implode("\n", array_slice($absenList, 0, 10)) : "- Nihil (Semua Hadir) -";
        if (count($absenList) > 10) {
            $absenStr .= "\n...dan " . (count($absenList) - 10) . " personil lainnya.";
        }

        $notesBlock = "";
        if (!empty($notes)) {
            $notesBlock = "\n📝 *Notulensi & Hasil Rapat:*\n" . $notes . "\n";
        }

        return "*[LAPORAN REKAPITULASI & ABSENSI RAPAT]*\n\n"
            . "🏢 *Unit Rig:* {$meeting['rig_name']}\n"
            . "📋 *Agenda:* {$meeting['title']}\n"
            . "📝 *Topik:* {$meeting['topic']}\n"
            . "📅 *Tanggal:* {$tanggal} ({$jam})\n"
            . "👤 *PJ Rig:* {$pj}\n"
            . "🔒 *Status Sesi:* RESMI DITUTUP (Completed)\n\n"
            . "📊 *Statistik Kehadiran Crew:*\n"
            . "✅ Hadir Tepat Waktu: *{$stats['hadir']}* personil\n"
            . "⚠️ Terlambat: *{$stats['terlambat']}* personil\n"
            . "ℹ️ Izin: *{$stats['izin']}* personil\n"
            . "❌ Tidak Hadir (Alpha): *{$stats['tidak_hadir']}* personil\n"
            . "📈 *Tingkat Kehadiran: {$stats['percentage']}%* (Total {$stats['total']} Personil)\n"
            . "{$notesBlock}\n"
            . "👥 *Daftar Personil Hadir:*\n{$hadirStr}\n\n"
            . "⚠️ *Personil Tidak Hadir / Izin:*\n{$absenStr}\n\n"
            . "Dokumen absensi dan notulensi resmi telah direkap dan disimpan ke sistem.\n"
            . "_Management PT. Besmindo Materi Sewatama_\n"
            . "_(Ref: #{$refCode})_";
    }
}

