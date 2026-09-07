<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Meeting_model');
        $this->load->model('Meeting_participant_model');
        $this->load->model('Crew_model');
        $this->load->model('Reminder_model');
        $this->load->model('Attendance_model');
    }

    public function check_reminders()
    {
        $now = time();
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $meetings = $this->Meeting_model->get_meetings_detailed();
        $processed = 0;
        $logs = array();

        foreach ($meetings as $m) {
            $meeting_time = strtotime($m['meeting_date'] . ' ' . $m['start_time']);
            $diff_seconds = $meeting_time - $now;
            $diff_minutes = round($diff_seconds / 60);

            $participants = $this->Meeting_participant_model->get_participants_by_meeting($m['id']);

            // 1. H-1
            if ($m['meeting_date'] === $tomorrow && $m['status'] === 'scheduled') {
                foreach ($participants as $p) {
                    if (!$this->Reminder_model->is_already_sent($m['id'], $p['crew_id'], 'H-1')) {
                        $msg = "*[REMINDER H-1 MEETING]*\nHalo {$p['crew_name']}, besok Anda ada meeting: {$m['title']} ({$m['rig_name']}) jam " . substr($m['start_time'], 0, 5) . " WIB.\nLink: {$m['teams_link']}";
                        $this->Reminder_model->insert(array(
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-1',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ));
                        $processed++;
                        $logs[] = "Sent H-1 to {$p['crew_name']}";
                    }
                }
            }

            // 2. 1 Jam
            if ($m['meeting_date'] === $today && $diff_minutes >= 45 && $diff_minutes <= 75 && $m['status'] === 'scheduled') {
                foreach ($participants as $p) {
                    if (!$this->Reminder_model->is_already_sent($m['id'], $p['crew_id'], 'H-1_JAM')) {
                        $msg = "*[REMINDER 1 JAM]*\nHalo {$p['crew_name']}, meeting {$m['title']} ({$m['rig_name']}) akan dimulai dalam 1 jam.\nLink: {$m['teams_link']}";
                        $this->Reminder_model->insert(array(
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-1_JAM',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ));
                        $processed++;
                        $logs[] = "Sent 1-Hour reminder to {$p['crew_name']}";
                    }
                }
            }

            // 3. 15 Menit
            if ($m['meeting_date'] === $today && $diff_minutes >= 0 && $diff_minutes <= 20 && ($m['status'] === 'scheduled' || $m['status'] === 'in_progress')) {
                foreach ($participants as $p) {
                    if (!$this->Reminder_model->is_already_sent($m['id'], $p['crew_id'], 'H-15_MIN')) {
                        $msg = "*[REMINDER 15 MENIT]*\nHalo {$p['crew_name']}, ruang meeting {$m['title']} telah dibuka. Segera masuk Teams sekarang:\nLink: {$m['teams_link']}";
                        $this->Reminder_model->insert(array(
                            'meeting_id'    => $m['id'],
                            'crew_id'       => $p['crew_id'],
                            'reminder_type' => 'H-15_MIN',
                            'target_phone'  => $p['phone'],
                            'message_body'  => $msg,
                            'status'        => 'sent',
                            'sent_at'       => date('Y-m-d H:i:s')
                        ));
                        $processed++;
                        $logs[] = "Sent 15-Min reminder to {$p['crew_name']}";
                    }
                }
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'   => true,
                'message'   => "Pemeriksaan reminder selesai. {$processed} notifikasi diproses.",
                'processed' => $processed,
                'logs'      => $logs,
                'timestamp' => date('d M Y H:i:s') . ' WIB'
            )));
    }

    /**
     * Endpoint Sinkronisasi Real-Time dari Teams Auto-Sync Robot
     * Menerima array nama peserta yang sedang aktif di ruang Microsoft Teams
     */
    public function sync_teams_live()
    {
        // Izinkan CORS dari domain manapun termasuk teams.microsoft.com
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        if (!is_array($data)) {
            $data = $this->input->post(NULL, TRUE) ?: array();
        }

        $meeting_id = isset($data['meeting_id']) ? (int)$data['meeting_id'] : 0;
        $rawNames   = isset($data['names']) && is_array($data['names']) ? $data['names'] : array();

        if (!$meeting_id) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Parameter meeting_id diperlukan.')));
        }

        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Meeting tidak ditemukan.')));
        }

        $this->Attendance_model->init_meeting_attendances($meeting_id);
        $attendances = $this->Attendance_model->get_meeting_attendances($meeting_id);

        // Helper fungsi normalisasi nama (Sangat fleksibel & case-insensitive)
        $normalizeName = function($str) {
            // 1. Hapus teks dalam kurung: (Guest), (Tamu), (Presenter), (Organizer), (Penyelenggara), (You), dll
            $str = preg_replace('/\s*\([^)]*\)/iu', '', $str);
            // 2. Hapus kata-kata status umum di akhir nama
            $str = preg_replace('/\b(organizer|penyelenggara|guest|tamu|presenter|attendee|peserta|you|anda)\b/iu', '', $str);
            // 3. Hapus simbol, emoji, tanda baca selain huruf & angka
            $str = preg_replace('/[^a-zA-Z0-9\s]/u', ' ', $str);
            // 4. Standarisasi spasi ganda dan jadikan huruf kecil semua (lowercase)
            return strtolower(trim(preg_replace('/\s+/', ' ', $str)));
        };

        // Siapkan daftar crew yang dinormalisasi
        $crewLookup = array();
        foreach ($attendances as $att) {
            $cleanCrew = $normalizeName($att['crew_name']);
            $words     = array_values(array_filter(explode(' ', $cleanCrew), function($w) { return strlen($w) >= 2; }));
            $crewLookup[] = array(
                'att'        => $att,
                'clean_name' => $cleanCrew,
                'words'      => $words
            );
        }

        $newlyJoined      = array();
        $currentlyPresent = array();
        $unmatchedNames   = array();
        $nowTime          = time();
        $schedTime        = strtotime($meeting['meeting_date'] . ' ' . $meeting['start_time']);
        $toleranceTime    = $schedTime + (10 * 60); // 10 menit toleransi

        foreach ($rawNames as $rawName) {
            $cleanName = $normalizeName($rawName);
            if (empty($cleanName) || strlen($cleanName) < 2) continue;

            $matchedAtt = NULL;

            foreach ($crewLookup as $c) {
                // 1. Exact match (Case-insensitive)
                if ($c['clean_name'] === $cleanName) {
                    $matchedAtt = $c['att'];
                    break;
                }

                // 2. Substring match (salah satu mengandung nama yang lain)
                if (strlen($c['clean_name']) >= 3 && strlen($cleanName) >= 3) {
                    if (strpos($cleanName, $c['clean_name']) !== false || strpos($c['clean_name'], $cleanName) !== false) {
                        $matchedAtt = $c['att'];
                        break;
                    }
                }

                // 3. Word token matching (Cocok kata demi kata)
                $teamsWords = array_values(array_filter(explode(' ', $cleanName), function($w) { return strlen($w) >= 2; }));
                $commonWords = array_intersect($c['words'], $teamsWords);

                // Jika punya >= 1 kata yang sama panjang (misal: "MUHAMMAD RAFI" vs "Rafi")
                if (count($commonWords) >= 2) {
                    $matchedAtt = $c['att'];
                    break;
                } elseif (count($commonWords) === 1) {
                    // Cek jika kata yang sama cukup spesifik (>= 4 huruf dan nama unik)
                    $matchedWord = reset($commonWords);
                    if (strlen($matchedWord) >= 4) {
                        $matchedAtt = $c['att'];
                        break;
                    }
                }

                // 4. Similarity fuzzy matching >= 75%
                similar_text($cleanName, $c['clean_name'], $pct);
                if ($pct >= 75) {
                    $matchedAtt = $c['att'];
                    break;
                }
            }

            if ($matchedAtt) {
                $statusNow = $matchedAtt['status'];

                // Jika crew belum tercatat hadir
                if ($statusNow === 'BELUM_HADIR' || $statusNow === 'TIDAK_HADIR') {
                    $newStatus = 'HADIR';
                    $this->Attendance_model->update($matchedAtt['id'], array(
                        'status'           => $newStatus,
                        'join_time'        => date('Y-m-d H:i:s'),
                        'duration_minutes' => 1,
                        'source'           => 'TEAMS_ROBOT',
                        'notes'            => 'Terdeteksi otomatis via Teams Auto-Sync Robot'
                    ));

                    $newlyJoined[] = array(
                        'crew_name' => $matchedAtt['crew_name'],
                        'status'    => $newStatus,
                        'time'      => date('H:i:s')
                    );
                } else {
                    // Update durasi keikutsertaan crew yang sudah ada di ruang
                    $joinTime = !empty($matchedAtt['join_time']) ? strtotime($matchedAtt['join_time']) : $nowTime;
                    $duration = max(1, round(($nowTime - $joinTime) / 60));
                    $this->Attendance_model->update($matchedAtt['id'], array(
                        'duration_minutes' => $duration
                    ));
                }

                $currentlyPresent[] = $matchedAtt['crew_name'];
            } else {
                $unmatchedNames[] = $rawName;
            }
        }

        $stats = $this->Attendance_model->get_attendance_stats($meeting_id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'           => true,
                'meeting_id'        => $meeting_id,
                'meeting_title'     => $meeting['title'],
                'received_count'    => count($rawNames),
                'matched_count'     => count($currentlyPresent),
                'newly_joined'      => $newlyJoined,
                'currently_present' => array_values(array_unique($currentlyPresent)),
                'unmatched_names'   => $unmatchedNames,
                'stats'             => $stats,
                'server_time'       => date('H:i:s') . ' WIB'
            )));
    }

    /**
     * Endpoint polling data live attendance untuk auto-refresh halaman browser
     */
    public function get_live_attendance($meeting_id)
    {
        header("Access-Control-Allow-Origin: *");
        $meeting_id = (int)$meeting_id;
        
        $meeting = $this->Meeting_model->get_meeting_detail($meeting_id);
        if (!$meeting) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Meeting tidak ditemukan.')));
        }

        $attendances = $this->Attendance_model->get_meeting_attendances($meeting_id);
        $stats       = $this->Attendance_model->get_attendance_stats($meeting_id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success'     => true,
                'meeting'     => $meeting,
                'attendances' => $attendances,
                'stats'       => $stats,
                'server_time' => date('H:i:s') . ' WIB'
            )));
    }

    /**
     * Pusat notifikasi admin (real-time, tanpa tabel DB).
     * Item dihitung dari state DB saat poll; status unread per-session disimpan di PHP session.
     */
    /**
     * Pusat notifikasi: komputasi item real-time dari state DB saat ini.
     * Dipakai bersama oleh `notifications()` (baca) dan `notifications_read()` (tandai semua)
     * agar kunci unread konsisten.
     */
    private function _build_items()
    {
        $now = time();
        $today = date('Y-m-d');
        $items = array();

        // --- Sumber 1: Meeting akan segera dimulai / sedang berlangsung ---
        $meetings = $this->Meeting_model->get_meetings_detailed();
        foreach ($meetings as $m) {
            if (!in_array($m['status'], array('scheduled', 'in_progress'), TRUE)) continue;

            $start = strtotime($m['meeting_date'] . ' ' . $m['start_time']);
            $minuteDiff = round(($start - $now) / 60);

            if ($m['status'] === 'in_progress' || ($minuteDiff >= -30 && $minuteDiff <= 15)) {
                if ($minuteDiff > 1) {
                    $type = 'meeting_soon';
                    $icon = 'fa-regular fa-clock';
                    $color = 'text-amber-500 bg-amber-500/10';
                    $text = "Meeting <strong>{$m['title']}</strong> ({$m['rig_name']}) dimulai dalam <strong>{$minuteDiff} menit</strong>.";
                    $agoTxt = 'mulai ' . date('H:i', $start) . ' WIB';
                } else {
                    $type = 'meeting_now';
                    $icon = 'fa-solid fa-video';
                    $color = 'text-emerald-500 bg-emerald-500/10';
                    $text = "<strong>{$m['title']}</strong> ({$m['rig_name']}) sedang <strong>berlangsung</strong> sekarang.";
                    $agoTxt = 'dibuka pukul ' . date('H:i', $start) . ' WIB';
                }
                $items[] = $this->_notif_item($type, $icon, $color, $text, 'meeting/detail/' . $m['id'], $start, $agoTxt, 'm' . $m['id']);
            }
        }

        // --- Sumber 2: Crew masih BELUM_HADIR pada meeting berlangsung ---
        if (!isset($this->Attendance_model)) $this->load->model('Attendance_model');
        foreach ($meetings as $m) {
            if ($m['status'] !== 'in_progress') continue;
            $msStart = strtotime($m['meeting_date'] . ' ' . $m['start_time']);
            $atts = $this->Attendance_model->get_meeting_attendances($m['id']);
            $pending = 0;
            foreach ($atts as $a) if ($a['status'] === 'BELUM_HADIR') $pending++;
            if ($pending > 0) {
                $items[] = $this->_notif_item(
                    'not_present',
                    'fa-solid fa-user-clock',
                    'text-rose-500 bg-rose-500/10',
                    "<strong>{$pending} crew</strong> masih <strong>belum hadir</strong> di {$m['title']} ({$m['rig_name']}).",
                    'attendance/live/' . $m['id'],
                    $msStart,
                    $this->_ago($now, $msStart),
                    'np' . $m['id']
                );
            }
        }

        // --- Sumber 3: Kehadiran crew baru (HADIR) dalam 10 menit terakhir ---
        $recentWindow = date('Y-m-d H:i:s', $now - 600);
        $this->db->select('attendances.status, attendances.updated_at, attendances.meeting_id, attendances.crew_id, crews.name as crew_name, meetings.title, rigs.name as rig_name');
        $this->db->from('attendances');
        $this->db->join('crews', 'crews.id = attendances.crew_id');
        $this->db->join('meetings', 'meetings.id = attendances.meeting_id');
        $this->db->join('rigs', 'rigs.id = meetings.rig_id', 'left');
        $this->db->where('attendances.status !=', 'BELUM_HADIR');
        $this->db->where('attendances.updated_at >=', $recentWindow);
        $this->db->order_by('attendances.updated_at', 'DESC');
        $this->db->limit(15);
        $recentJoins = $this->db->get()->result_array();
        foreach ($recentJoins as $rj) {
            $items[] = $this->_notif_item(
                'attendance',
                'fa-solid fa-user-check',
                'text-emerald-500 bg-emerald-500/10',
                "<strong>{$rj['crew_name']}</strong> hadir di {$rj['title']} ({$rj['rig_name']}).",
                'attendance/live/' . $rj['meeting_id'],
                strtotime($rj['updated_at']),
                $this->_ago($now, strtotime($rj['updated_at'])),
                'a' . $rj['meeting_id'] . '_' . $rj['crew_id']
            );
        }

        // --- Sumber 4: Log reminder/broadcast hari ini (real dispatch / outbox) ---
        if (!isset($this->Reminder_model)) $this->load->model('Reminder_model');
        $logs = $this->Reminder_model->get_logs(null, 20);
        $typeLabel = array(
            'UNDANGAN'             => 'undangan',
            'H-1'                  => 'H-1',
            'H-1_JAM'              => '1 jam',
            'H-15_MIN'             => '15 menit',
            'NOT_PRESENT_REMINDER' => 'belum hadir'
        );
        foreach ($logs as $log) {
            if ($log['sent_at'] < $today . ' 00:00:00') continue; // hanya hari ini
            $label = isset($typeLabel[$log['reminder_type']]) ? $typeLabel[$log['reminder_type']] : strtolower($log['reminder_type']);
            if ($log['status'] === 'failed') {
                $icon = 'fa-solid fa-circle-xmark'; $color = 'text-rose-500 bg-rose-500/10';
                $text = "<strong>Gagal kirim</strong> reminder {$label} ke {$log['crew_name']} untuk {$log['meeting_title']}.";
            } else {
                $icon = 'fa-brands fa-whatsapp'; $color = 'text-emerald-500 bg-emerald-500/10';
                $text = "Reminder <strong>{$label}</strong> dikirim ke <strong>{$log['crew_name']}</strong> untuk {$log['meeting_title']}.";
            }
            $items[] = $this->_notif_item(
                'reminder',
                $icon, $color, $text,
                'reminder/log',
                strtotime($log['sent_at']),
                $this->_ago($now, strtotime($log['sent_at'])),
                'r' . $log['id']
            );
        }

        // --- Urutkan dari terbaru, kap maks 30 item ---
        usort($items, function ($a, $b) { return strcmp($b['time_sort'], $a['time_sort']); });
        return array_slice($items, 0, 30);
    }

    public function notifications()
    {
        $isLogged = (bool)$this->session->userdata('logged_in');

        // Auth guard: tanpa login, jangan bocorkan data & jangan kunci unread di session.
        if (!$isLogged) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('items' => array(), 'unread' => 0, 'error' => 'login_required')));
        }

        $items = $this->_build_items();

        // --- Item baru (belum dilihat) pada sesi ini ---
        $seen = $this->session->userdata('notif_seen');
        if (!is_array($seen)) $seen = array();
        $fresh = array();
        foreach ($items as $it) {
            if (!isset($seen[$it['key']])) $fresh[] = $it;
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'items'  => $items,
                'unread' => count($fresh),
                'now'    => date('d M Y H:i:s') . ' WIB'
            )));
    }

    /**
     * Tandai item notifikasi sebagai sudah dibaca pada sesi ini.
     * Body JSON `keys`: array kunci; kosongkan / null = tandai SEMUA item yang kini tampil.
     */
    public function notifications_read()
    {
        $isLogged = (bool)$this->session->userdata('logged_in');
        if (!$isLogged) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false)));
        }
        $seen = $this->session->userdata('notif_seen');
        if (!is_array($seen)) $seen = array();

        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);
        $keys = (isset($body['keys']) && is_array($body['keys'])) ? $body['keys'] : null;

        if ($keys === null) {
            // Tandai SEMUA item yang kini tampil: pakai builder yang sama → kunci pasti konsisten.
            foreach ($this->_build_items() as $it) {
                $seen[$it['key']] = true;
            }
        } else {
            foreach ($keys as $k) { $seen[$k] = true; }
        }
        $this->session->set_userdata('notif_seen', $seen);

        return $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => true)));
    }

    private function _ago($now, $ts)
    {
        $diff = $now - $ts;
        if ($diff < 0) $diff = 0;
        if ($diff < 60) return $diff . ' dtk lalu';
        if ($diff < 3600) return round($diff / 60) . ' mnt lalu';
        if ($diff < 86400) return round($diff / 3600) . ' jam lalu';
        return date('d M', $ts);
    }

    private function _notif_item($type, $icon, $color, $text, $link, $sortTs, $ago, $idKey)
    {
        // Kunci stabil per entitas (mis. a<meeting>_<crew>, m<meeting>, r<log>) agar status unread
        // tidak berubah seiring teks waktu yang bergulir antar poll.
        $key = $type . '_' . md5($type . '|' . $link . '|' . $idKey);
        return array(
            'type'      => $type,
            'icon'      => $icon,
            'color'     => $color,
            'text'      => $text,
            'link'      => base_url($link),
            'time_text' => $ago,
            'time_sort' => date('YmdHis', $sortTs),
            'key'       => $key
        );
    }
}

