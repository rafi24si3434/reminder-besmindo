<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Install extends Controller
{
    public function index()
    {
        $dbConfig = config('Database')->default;
        $dbHost = $dbConfig['hostname'] ?? 'localhost';
        $dbUser = $dbConfig['username'] ?? 'root';
        $dbPass = $dbConfig['password'] ?? '';
        $dbName = $dbConfig['database'] ?? 'db_besmindo_reminder';
        $dbPort = $dbConfig['port'] ?? 3306;

        $status = [
            'can_connect_server' => false,
            'database_exists'    => false,
            'tables_created'     => false,
            'seeded'             => false,
            'message'            => ''
        ];

        try {
            $mysqli = @new \mysqli($dbHost, $dbUser, $dbPass, '', (int)$dbPort);
            if ($mysqli->connect_error) {
                $status['message'] = "Gagal menghubungkan ke MySQL: " . $mysqli->connect_error . " (Pastikan MySQL di XAMPP / Laragon sudah dinyalakan).";
                return view('install/index', ['status' => $status, 'config' => $dbConfig]);
            }
            $status['can_connect_server'] = true;

            // Check if database exists
            $res = $mysqli->query("SHOW DATABASES LIKE '{$dbName}'");
            if ($res && $res->num_rows > 0) {
                $status['database_exists'] = true;
                $mysqli->select_db($dbName);
                $tablesRes = $mysqli->query("SHOW TABLES LIKE 'meetings'");
                if ($tablesRes && $tablesRes->num_rows > 0) {
                    $status['tables_created'] = true;
                }
            }
        } catch (\Throwable $e) {
            $status['message'] = $e->getMessage();
        }

        return view('install/index', ['status' => $status, 'config' => $dbConfig]);
    }

    public function run()
    {
        $dbConfig = config('Database')->default;
        $dbHost = $dbConfig['hostname'] ?? 'localhost';
        $dbUser = $dbConfig['username'] ?? 'root';
        $dbPass = $dbConfig['password'] ?? '';
        $dbName = $dbConfig['database'] ?? 'db_besmindo_reminder';
        $dbPort = $dbConfig['port'] ?? 3306;

        try {
            $mysqli = @new \mysqli($dbHost, $dbUser, $dbPass, '', (int)$dbPort);
            if ($mysqli->connect_error) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Koneksi MySQL gagal: ' . $mysqli->connect_error . '. Pastikan MySQL di XAMPP sudah di-start.'
                ]);
            }

            // 1. Create database
            $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $mysqli->select_db($dbName);

            // 2. Read schema file
            $sqlFile = ROOTPATH . 'database.sql';
            if (file_exists($sqlFile)) {
                $sqlContent = file_get_contents($sqlFile);
                $queries = explode(';', $sqlContent);
                foreach ($queries as $q) {
                    $q = trim($q);
                    if (!empty($q)) {
                        $mysqli->query($q);
                    }
                }
            }

            // 3. Ensure sample meetings for this week are seeded
            $this->seedDynamicMeetings($mysqli);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Database & Demo Data berhasil diinstal!'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    private function seedDynamicMeetings(\mysqli $mysqli)
    {
        // Add dynamic meetings for current week and today
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $check = $mysqli->query("SELECT COUNT(*) as c FROM meetings WHERE meeting_date = '{$today}'");
        $row = $check ? $check->fetch_assoc() : null;

        if (!$row || $row['c'] == 0) {
            // Meeting 1: Today Routine Meeting (In Progress)
            $mysqli->query("INSERT INTO meetings (title, topic, rig_id, pj_crew_id, meeting_date, start_time, end_time, teams_link, is_recurring, recurring_day, status)
                VALUES ('Weekly Rig Safety & Operational Review', 'Review KPI Keselamatan Kerja & Prosedur Drilling Rig 01', 1, 1, '{$today}', '08:30:00', '09:30:00', 'https://teams.microsoft.com/l/meetup-join/19%3ameeting_rig01_review%40thread.v2/0', 1, 'Monday', 'in_progress')");
            $mId1 = $mysqli->insert_id;

            // Meeting 2: Today Technical Meeting (Scheduled)
            $mysqli->query("INSERT INTO meetings (title, topic, rig_id, pj_crew_id, meeting_date, start_time, end_time, teams_link, is_recurring, recurring_day, status)
                VALUES ('HSE Pre-Tour & Mud Circulation Briefing', 'Briefing sirkulasi lumpur dan kesiapan alat keselamatan Rig 02', 2, 8, '{$today}', '14:00:00', '15:00:00', 'https://teams.microsoft.com/l/meetup-join/19%3ameeting_rig02_hse%40thread.v2/0', 1, 'Monday', 'scheduled')");
            $mId2 = $mysqli->insert_id;

            // Meeting 3: Yesterday (Completed)
            $mysqli->query("INSERT INTO meetings (title, topic, rig_id, pj_crew_id, meeting_date, start_time, end_time, teams_link, is_recurring, recurring_day, status)
                VALUES ('Weekly Offshore Operations Review', 'Evaluasi hasil pengeboran sumur gas lepas pantai Rig 03', 3, 13, '{$yesterday}', '09:00:00', '10:00:00', 'https://teams.microsoft.com/l/meetup-join/19%3ameeting_rig03_eval%40thread.v2/0', 1, 'Sunday', 'completed')");
            $mId3 = $mysqli->insert_id;

            // Meeting 4: Tomorrow (Scheduled)
            $mysqli->query("INSERT INTO meetings (title, topic, rig_id, pj_crew_id, meeting_date, start_time, end_time, teams_link, is_recurring, recurring_day, status)
                VALUES ('Preventive Maintenance Coordination', 'Jadwal servis berkala generator & drawworks Rig 01', 1, 7, '{$tomorrow}', '10:00:00', '11:00:00', 'https://teams.microsoft.com/l/meetup-join/19%3ameeting_rig01_maint%40thread.v2/0', 0, NULL, 'scheduled')");
            $mId4 = $mysqli->insert_id;

            // Seed participants & attendances for Meeting 1 (Rig 01)
            $crews1 = [1, 2, 3, 4, 5, 6, 7];
            foreach ($crews1 as $cid) {
                $mysqli->query("INSERT IGNORE INTO meeting_participants (meeting_id, crew_id) VALUES ({$mId1}, {$cid})");
            }
            // Seed sample live attendance for meeting 1
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, join_time, duration_minutes, source) VALUES ({$mId1}, 1, 'HADIR', '{$today} 08:28:10', 45, 'TEAMS_SIMULATOR')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, join_time, duration_minutes, source) VALUES ({$mId1}, 2, 'HADIR', '{$today} 08:29:45', 42, 'TEAMS_SIMULATOR')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, join_time, duration_minutes, source) VALUES ({$mId1}, 3, 'TERLAMBAT', '{$today} 08:42:15', 30, 'TEAMS_SIMULATOR')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, source) VALUES ({$mId1}, 4, 'BELUM_HADIR', 'TEAMS_SIMULATOR')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, source) VALUES ({$mId1}, 5, 'BELUM_HADIR', 'TEAMS_SIMULATOR')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, notes, source) VALUES ({$mId1}, 6, 'IZIN', 'Izin pergantian shift jaga rig', 'MANUAL')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, join_time, duration_minutes, source) VALUES ({$mId1}, 7, 'HADIR', '{$today} 08:30:00', 40, 'TEAMS_SIMULATOR')");

            // Seed participants for Meeting 2 (Rig 02)
            $crews2 = [8, 9, 10, 11, 12];
            foreach ($crews2 as $cid) {
                $mysqli->query("INSERT IGNORE INTO meeting_participants (meeting_id, crew_id) VALUES ({$mId2}, {$cid})");
                $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, source) VALUES ({$mId2}, {$cid}, 'BELUM_HADIR', 'TEAMS_SIMULATOR')");
            }

            // Seed participants & completed attendances for Meeting 3 (Rig 03)
            $crews3 = [13, 14, 15, 16, 17];
            foreach ($crews3 as $cid) {
                $mysqli->query("INSERT IGNORE INTO meeting_participants (meeting_id, crew_id) VALUES ({$mId3}, {$cid})");
            }
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, join_time, leave_time, duration_minutes, source) VALUES ({$mId3}, 13, 'HADIR', '{$yesterday} 08:58:00', '{$yesterday} 10:00:00', 62, 'TEAMS_SYNC')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, join_time, leave_time, duration_minutes, source) VALUES ({$mId3}, 14, 'HADIR', '{$yesterday} 09:00:10', '{$yesterday} 10:00:00', 60, 'TEAMS_SYNC')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, join_time, leave_time, duration_minutes, source) VALUES ({$mId3}, 15, 'TERLAMBAT', '{$yesterday} 09:18:22', '{$yesterday} 10:00:00', 42, 'TEAMS_SYNC')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, join_time, leave_time, duration_minutes, source) VALUES ({$mId3}, 16, 'HADIR', '{$yesterday} 08:55:30', '{$yesterday} 10:00:00', 65, 'TEAMS_SYNC')");
            $mysqli->query("INSERT INTO attendances (meeting_id, crew_id, status, notes, source) VALUES ({$mId3}, 17, 'TIDAK_HADIR', 'Tanpa keterangan / Off duty', 'MANUAL')");

            // Seed sample reminder logs
            $mysqli->query("INSERT INTO reminders_log (meeting_id, crew_id, reminder_type, target_phone, message_body, status, sent_at)
                VALUES ({$mId1}, 1, 'UNDANGAN', '6281234567801', 'Halo Agus Kurniawan, Anda diundang ke meeting: Weekly Rig Safety & Operational Review pada {$today} 08:30 WIB. Link: https://teams.microsoft.com/...', 'sent', '{$today} 07:00:00')");
            $mysqli->query("INSERT INTO reminders_log (meeting_id, crew_id, reminder_type, target_phone, message_body, status, sent_at)
                VALUES ({$mId1}, 1, 'H-1_JAM', '6281234567801', '[REMINDER 1 JAM] Meeting Weekly Rig Safety & Operational Review akan dimulai pukul 08:30 WIB.', 'sent', '{$today} 07:30:00')");
            $mysqli->query("INSERT INTO reminders_log (meeting_id, crew_id, reminder_type, target_phone, message_body, status, sent_at)
                VALUES ({$mId1}, 1, 'H-15_MIN', '6281234567801', '[REMINDER 15 MENIT] Bersiap bergabung ke Microsoft Teams sekarang.', 'sent', '{$today} 08:15:00')");
        }
    }
}
