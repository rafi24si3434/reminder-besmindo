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
            'title'       => 'Live Attendance: ' . $meeting['title'] . ' - Monitoring Pre Hitch Meeting',
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
            'title'       => 'Import Teams (.xlsx / .csv) & Analisis Kehadiran - Monitoring Pre Hitch Meeting',
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

        $join_time = date('Y-m-d H:i:s', strtotime("{$meeting_date} {$start_time} -2 minutes"));
        $status = 'HADIR';
        $duration = 60;

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
                'message'   => "Status kehadiran berhasil diupdate: HADIR"
            )));
    }

    public function update_status_manual()
    {
        $attendance_id = (int)$this->input->post('attendance_id', TRUE);
        $status = $this->input->post('status', TRUE);
        $notes = trim($this->input->post('notes', TRUE));

        $valid = array('HADIR', 'BELUM_HADIR', 'TIDAK_HADIR', 'IZIN');
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
                        $status = 'HADIR';
                        
                        $exist = $this->db->get_where('attendances', array('meeting_id' => $meeting_id, 'crew_id' => $crew['id']))->row_array();
                        if ($exist) {
                            $this->Attendance_model->update($exist['id'], array(
                                'status'           => $status,
                                'join_time'        => date('Y-m-d H:i:s'),
                                'duration_minutes' => 50,
                                'source'           => 'TEAMS_FILE'
                            ));
                        } else {
                            $this->Attendance_model->insert(array(
                                'meeting_id'       => $meeting_id,
                                'crew_id'          => $crew['id'],
                                'status'           => $status,
                                'join_time'        => date('Y-m-d H:i:s'),
                                'duration_minutes' => 50,
                                'source'           => 'TEAMS_FILE'
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

    public function analyze_teams_file()
    {
        $meeting_id = (int)$this->input->post('meeting_id', TRUE);
        if (!$meeting_id) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Pilih jadwal meeting terlebih dahulu.')));
        }

        if (empty($_FILES['teams_file']['tmp_name']) || !is_uploaded_file($_FILES['teams_file']['tmp_name'])) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Silakan unggah file Microsoft Teams (.xlsx atau .csv).')));
        }

        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Data meeting tidak ditemukan.')));
        }

        $this->Attendance_model->init_meeting_attendances($meeting_id);
        $participants = $this->Attendance_model->get_meeting_attendances($meeting_id);

        $this->load->library('SimpleXLSX');
        $filePath = $_FILES['teams_file']['tmp_name'];
        $originalName = $_FILES['teams_file']['name'];

        $parsedRows = SimpleXLSX::parse($filePath);
        if ($parsedRows === false || empty($parsedRows)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Gagal membaca isi file. Pastikan format file Excel (.xlsx) atau CSV Teams valid.')));
        }

        $headerRowIndex = -1;
        $colName = -1;
        $colJoin = -1;
        $colDuration = -1;

        for ($i = 0; $i < min(15, count($parsedRows)); $i++) {
            $row = $parsedRows[$i];
            foreach ($row as $colIdx => $val) {
                $v = strtolower(trim((string)$val));
                if (in_array($v, array('name', 'full name', 'nama', 'participant', 'peserta', 'display name', 'attendee name', 'name (original name)'))) {
                    $headerRowIndex = $i;
                    $colName = $colIdx;
                } elseif (in_array($v, array('join time', 'first join', 'waktu gabung', 'joined', 'join_time', 'time joined'))) {
                    $colJoin = $colIdx;
                } elseif (in_array($v, array('duration', 'total duration', 'durasi', 'in-meeting duration', 'time in meeting'))) {
                    $colDuration = $colIdx;
                }
            }
            if ($colName !== -1) break;
        }

        if ($colName === -1) {
            $headerRowIndex = 0;
            $colName = 0;
            $colJoin = (isset($parsedRows[0][1])) ? 1 : -1;
            $colDuration = (isset($parsedRows[0][2])) ? 2 : -1;
        }

        $extractedAttendees = array();
        $startRow = $headerRowIndex + 1;

        for ($r = $startRow; $r < count($parsedRows); $r++) {
            $row = $parsedRows[$r];
            if (!isset($row[$colName])) continue;
            $rawName = trim((string)$row[$colName]);
            if (empty($rawName) || strpos($rawName, '---') !== false) continue;
            if (preg_match('/^(summary|meeting title|start time|end time|total participants)/i', $rawName)) continue;

            $joinTimeVal = ($colJoin !== -1 && isset($row[$colJoin])) ? trim((string)$row[$colJoin]) : '';
            $durationVal = ($colDuration !== -1 && isset($row[$colDuration])) ? trim((string)$row[$colDuration]) : '';

            $durMinutes = 0;
            if (!empty($durationVal)) {
                if (preg_match('/(\d+)\s*h/i', $durationVal, $mH)) {
                    $durMinutes += ((int)$mH[1]) * 60;
                }
                if (preg_match('/(\d+)\s*m/i', $durationVal, $mM)) {
                    $durMinutes += (int)$mM[1];
                }
                if ($durMinutes === 0 && is_numeric($durationVal)) {
                    $num = (int)$durationVal;
                    $durMinutes = ($num > 300) ? round($num / 60) : $num;
                }
            }
            if ($durMinutes === 0) $durMinutes = 45;

            $extractedAttendees[] = array(
                'raw_name'         => $rawName,
                'clean_name'       => strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $rawName)),
                'join_time'        => $joinTimeVal,
                'duration_minutes' => $durMinutes,
                'matched'          => false
            );
        }

        $crewIndex = array();
        foreach ($participants as $p) {
            $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p['crew_name']));
            $tokens = array_filter(explode(' ', strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $p['crew_name'])))));
            $crewIndex[$p['crew_id']] = array(
                'participant' => $p,
                'clean_name'  => $clean,
                'tokens'      => $tokens,
                'matched'     => false
            );
        }

        $matchedList = array();
        $unrecognizedList = array();

        foreach ($extractedAttendees as &$att) {
            $bestMatchCrewId = null;
            $matchMethod = null;
            $highestPct = 0;

            // 1. Exact Match
            foreach ($crewIndex as $cid => $cData) {
                if ($cData['matched']) continue;
                if ($att['clean_name'] === $cData['clean_name']) {
                    $bestMatchCrewId = $cid;
                    $matchMethod = 'Exact Match';
                    break;
                }
            }

            // 2. Substring Match
            if (!$bestMatchCrewId) {
                foreach ($crewIndex as $cid => $cData) {
                    if ($cData['matched']) continue;
                    if (!empty($cData['clean_name']) && !empty($att['clean_name'])) {
                        if (strpos($cData['clean_name'], $att['clean_name']) !== false || strpos($att['clean_name'], $cData['clean_name']) !== false) {
                            $bestMatchCrewId = $cid;
                            $matchMethod = 'Substring Match';
                            break;
                        }
                    }
                }
            }

            // 3. Token Match
            if (!$bestMatchCrewId) {
                $attTokens = array_filter(explode(' ', strtolower(trim(preg_replace('/[^a-zA-Z0-9 ]/', ' ', $att['raw_name'])))));
                foreach ($crewIndex as $cid => $cData) {
                    if ($cData['matched']) continue;
                    $common = array_intersect($attTokens, $cData['tokens']);
                    if (count($common) >= 2 || (count($cData['tokens']) === 1 && count($common) === 1 && strlen(reset($common)) >= 4)) {
                        $bestMatchCrewId = $cid;
                        $matchMethod = 'Token Match';
                        break;
                    }
                }
            }

            // 4. Fuzzy Match (similar_text >= 75%)
            if (!$bestMatchCrewId) {
                foreach ($crewIndex as $cid => $cData) {
                    if ($cData['matched']) continue;
                    similar_text($att['clean_name'], $cData['clean_name'], $pct);
                    if ($pct >= 75 && $pct > $highestPct) {
                        $highestPct = $pct;
                        $bestMatchCrewId = $cid;
                        $matchMethod = 'Fuzzy Match (' . round($pct) . '%)';
                    }
                }
            }

            if ($bestMatchCrewId) {
                $att['matched'] = true;
                $crewIndex[$bestMatchCrewId]['matched'] = true;

                $matchedList[] = array(
                    'attendance_id'    => $crewIndex[$bestMatchCrewId]['participant']['id'],
                    'crew_id'          => $bestMatchCrewId,
                    'crew_name'        => $crewIndex[$bestMatchCrewId]['participant']['crew_name'],
                    'nik'              => $crewIndex[$bestMatchCrewId]['participant']['nik'],
                    'position'         => $crewIndex[$bestMatchCrewId]['participant']['position'],
                    'group_code'       => $crewIndex[$bestMatchCrewId]['participant']['group_code'],
                    'phone'            => $crewIndex[$bestMatchCrewId]['participant']['phone'],
                    'file_name'        => $att['raw_name'],
                    'method'           => $matchMethod,
                    'duration_minutes' => $att['duration_minutes'],
                    'join_time'        => $att['join_time'] ?: date('H:i:s')
                );
            } else {
                $unrecognizedList[] = array(
                    'raw_name'         => $att['raw_name'],
                    'join_time'        => $att['join_time'] ?: '-',
                    'duration_minutes' => $att['duration_minutes']
                );
            }
        }

        $unmatchedCrewList = array();
        foreach ($crewIndex as $cid => $cData) {
            if (!$cData['matched']) {
                $unmatchedCrewList[] = array(
                    'attendance_id' => $cData['participant']['id'],
                    'crew_id'       => $cid,
                    'crew_name'     => $cData['participant']['crew_name'],
                    'nik'           => $cData['participant']['nik'],
                    'position'      => $cData['participant']['position'],
                    'group_code'    => $cData['participant']['group_code'],
                    'phone'         => $cData['participant']['phone'],
                    'current_status'=> $cData['participant']['status']
                );
            }
        }

        $totalExpected = count($participants);
        $totalFound = count($matchedList);
        $totalUnmatched = count($unmatchedCrewList);
        $attendanceRate = $totalExpected > 0 ? round(($totalFound / $totalExpected) * 100, 1) : 0;

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'            => true,
                'fileName'           => $originalName,
                'meeting'            => array(
                    'id'           => $meeting['id'],
                    'title'        => $meeting['title'],
                    'rig_name'     => $meeting['rig_name'],
                    'meeting_date' => date('d M Y', strtotime($meeting['meeting_date'])),
                    'start_time'   => substr($meeting['start_time'], 0, 5) . ' WIB',
                    'is_joint'     => $meeting['is_joint'],
                    'joint_summary'=> $meeting['joint_summary']
                ),
                'summary'            => array(
                    'total_expected'  => $totalExpected,
                    'total_found'     => $totalFound,
                    'total_unmatched' => $totalUnmatched,
                    'total_external'  => count($unrecognizedList),
                    'percentage'      => $attendanceRate
                ),
                'matched'            => $matchedList,
                'unmatched'          => $unmatchedCrewList,
                'unrecognized'       => $unrecognizedList
            )));
    }

    public function apply_teams_attendance()
    {
        $meeting_id = (int)$this->input->post('meeting_id', TRUE);
        $matched_json = $this->input->post('matched_data');
        $unmatched_action = $this->input->post('unmatched_action', TRUE);

        if (!$meeting_id || empty($matched_json)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Data hasil analisis tidak lengkap.')));
        }

        $matchedList = json_decode($matched_json, true);
        if (!is_array($matchedList)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Format data tidak valid.')));
        }

        $meeting = $this->Meeting_model->get_by_id($meeting_id);
        $appliedCount = 0;

        foreach ($matchedList as $item) {
            $attId = isset($item['attendance_id']) ? (int)$item['attendance_id'] : 0;
            if (!$attId) continue;

            $joinTime = date('Y-m-d H:i:s');
            if (!empty($item['join_time']) && strpos($item['join_time'], ':') !== false) {
                $timePart = (strlen($item['join_time']) === 5) ? $item['join_time'] . ':00' : $item['join_time'];
                $joinTime = $meeting['meeting_date'] . ' ' . $timePart;
            }

            $dur = !empty($item['duration_minutes']) ? (int)$item['duration_minutes'] : 45;
            $fileName = isset($item['file_name']) ? $item['file_name'] : $item['crew_name'];
            $method = isset($item['method']) ? $item['method'] : 'Teams File';

            $this->Attendance_model->update($attId, array(
                'status'           => 'HADIR',
                'join_time'        => $joinTime,
                'duration_minutes' => $dur,
                'source'           => 'TEAMS_FILE',
                'notes'            => "Cocok dengan '{$fileName}' ({$method})"
            ));
            $appliedCount++;
        }

        $alphaCount = 0;
        if ($unmatched_action === 'mark_alpha') {
            $this->db->where('meeting_id', $meeting_id);
            $this->db->where('status', 'BELUM_HADIR');
            $this->db->update('attendances', array(
                'status' => 'TIDAK_HADIR',
                'notes'  => 'Tidak ditemukan pada file Microsoft Teams',
                'source' => 'TEAMS_FILE'
            ));
            $alphaCount = $this->db->affected_rows();
        }

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'     => true,
                'applied'     => $appliedCount,
                'alpha_count' => $alphaCount,
                'message'     => "Berhasil menerapkan presensi: {$appliedCount} personil ditandai HADIR!" . ($alphaCount > 0 ? " ({$alphaCount} crew ditandai Alpha)" : "")
            )));
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
            'title'       => 'Rekap Kehadiran: ' . $meeting['title'] . ' - Monitoring Pre Hitch Meeting',
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
            . "✅ Total Hadir: *{$stats['hadir']}* personil\n"
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

