-- =======================================================
-- SISTEM REMINDER DAN MONITORING KEHADIRAN MEETING CREW RIG
-- BESMINDO REMINDER DATABASE SCHEMA & SEED DATA
-- =======================================================

CREATE DATABASE IF NOT EXISTS `db_besmindo_reminder` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_besmindo_reminder`;

-- 1. Tabel Users (Manager)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('manager', 'admin') DEFAULT 'manager',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel Rigs
CREATE TABLE IF NOT EXISTS `rigs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `location` VARCHAR(150) NOT NULL,
    `wa_group_id` VARCHAR(100) DEFAULT NULL,
    `wa_group_name` VARCHAR(150) DEFAULT NULL,
    `description` TEXT,
    `pj_name` VARCHAR(100) NOT NULL,
    `pj_phone` VARCHAR(25) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel Crews
CREATE TABLE IF NOT EXISTS `crews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nik` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `position` VARCHAR(100) NOT NULL,
    `group_code` ENUM('A', 'B', 'C') DEFAULT 'A',
    `rig_id` INT NOT NULL,
    `phone` VARCHAR(25) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`rig_id`) REFERENCES `rigs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel Meetings
CREATE TABLE IF NOT EXISTS `meetings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `topic` TEXT NOT NULL,
    `meeting_notes` TEXT DEFAULT NULL,
    `rig_id` INT NOT NULL,
    `group_target` ENUM('ALL', 'A', 'B', 'C') DEFAULT 'A',
    `session_time` ENUM('SIANG', 'MALAM') DEFAULT 'SIANG',
    `is_joint` TINYINT(1) DEFAULT 0,
    `joint_summary` VARCHAR(255) DEFAULT NULL,
    `pj_crew_id` INT DEFAULT NULL,
    `meeting_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `teams_link` VARCHAR(500) NOT NULL,
    `is_recurring` TINYINT(1) DEFAULT 0,
    `recurring_day` VARCHAR(20) DEFAULT NULL, -- 'Monday', 'Tuesday', etc.
    `status` ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    `closed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`rig_id`) REFERENCES `rigs`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`pj_crew_id`) REFERENCES `crews`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabel Meeting Participants
CREATE TABLE IF NOT EXISTS `meeting_participants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `meeting_id` INT NOT NULL,
    `crew_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`meeting_id`) REFERENCES `meetings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`crew_id`) REFERENCES `crews`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_participant` (`meeting_id`, `crew_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabel Reminders Log
CREATE TABLE IF NOT EXISTS `reminders_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `meeting_id` INT NOT NULL,
    `crew_id` INT NOT NULL,
    `reminder_type` ENUM('UNDANGAN', 'H-1', 'H-1_JAM', 'H-15_MIN', 'NOT_PRESENT_REMINDER') NOT NULL,
    `target_phone` VARCHAR(25) NOT NULL,
    `message_body` TEXT NOT NULL,
    `status` ENUM('pending', 'sent', 'failed') DEFAULT 'sent',
    `sent_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`meeting_id`) REFERENCES `meetings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`crew_id`) REFERENCES `crews`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Tabel Attendances
CREATE TABLE IF NOT EXISTS `attendances` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `meeting_id` INT NOT NULL,
    `crew_id` INT NOT NULL,
    `status` ENUM('HADIR', 'BELUM_HADIR', 'TIDAK_HADIR', 'IZIN') DEFAULT 'BELUM_HADIR',
    `join_time` DATETIME DEFAULT NULL,
    `leave_time` DATETIME DEFAULT NULL,
    `duration_minutes` INT DEFAULT 0,
    `notes` TEXT DEFAULT NULL,
    `source` ENUM('TEAMS_SIMULATOR', 'TEAMS_SYNC', 'TEAMS_FILE', 'MANUAL') DEFAULT 'TEAMS_SIMULATOR',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`meeting_id`) REFERENCES `meetings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`crew_id`) REFERENCES `crews`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_meeting_attendance` (`meeting_id`, `crew_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Tabel App Settings
CREATE TABLE IF NOT EXISTS `app_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(50) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =======================================================
-- SEED DATA (DATA AWAL)
-- =======================================================

-- Users (Password: admin123)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`) VALUES
(1, 'Manager Operasional Rig', 'admin', 'manager@besmindo.co.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- App Settings
INSERT INTO `app_settings` (`setting_key`, `setting_value`) VALUES
('company_name', 'PT. Besmindo Materi Sewatama'),
('wa_gateway_provider', 'LOCAL_NODE'),
('wa_sender_number', ''),
('wa_api_url', 'http://localhost:3000/send-message'),
('wa_api_token', ''),
('late_tolerance_minutes', '10'),
('default_teams_link', 'https://teams.microsoft.com/l/meetup-join/19%3ameeting_besmindo_rig_routine%40thread.v2/0?context=%7b%22Tid%22%3a%22besmindo-group%22%7d')
ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`;

