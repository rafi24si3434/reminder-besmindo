# SISTEM REMINDER DAN MONITORING KEHADIRAN MEETING CREW RIG (BESMINDO REMINDER)
**Framework:** CodeIgniter 4 (CI4) &bull; **Bahasa:** PHP 8.4+ &bull; **Database:** MySQL / MariaDB &bull; **Styling:** Tailwind CSS & FontAwesome 6

---

## 📌 Ringkasan Sistem
Sistem berbasis web yang dirancang khusus untuk **Manager Operasional Rig** dalam mengelola jadwal meeting rutin mingguan seluruh unit Rig Besmindo, mengirimkan undangan & pengingat bertingkat melalui WhatsApp, memantau kehadiran crew secara real-time via Microsoft Teams, serta menghasilkan rekap dan laporan analitik kehadiran.

---

## 🚀 Cara Menjalankan Aplikasi di XAMPP

1. **Pastikan Web Server & MySQL Berjalan:**
   - Buka **XAMPP Control Panel**, lalu klik **Start** pada modul **Apache** dan **MySQL**.

2. **Akses Aplikasi Melalui Browser:**
   - Buka browser Anda dan akses:
     ```
     http://localhost/Project%20Besmindo%20Reminder/
     ```

3. **Inisialisasi Database 1-Klik:**
   - Jika database belum terbuat, Anda dapat langsung membuka menu instalasi di:
     ```
     http://localhost/Project%20Besmindo%20Reminder/install
     ```
   - Klik tombol **"Inisialisasi Database Sekarang"**. Sistem akan otomatis membuat database `db_besmindo_reminder`, tabel-tabel, dan mengisi data demo awal (Rig 01, Rig 02, Rig 03, Daftar Crew Lengkap, Jadwal Meeting Pekan Ini, dan Sampel Kehadiran).

4. **Kredensial Login Default Manager:**
   - **Username:** `admin`
   - **Password:** `admin123`

---

## 🧭 Panduan Fitur Sesuai 5 Fase Roadmap

### 1. Fase 1: Jadwal Mingguan & Master Data Crew/Rig
- **Manajemen Crew (`/crew`):** Tambah, ubah, nonaktifkan personil crew, filter penempatan unit Rig, dan nomor WhatsApp.
- **Master Data Rig (`/rig`):** Pengelolaan unit Rig (Rig 01 Duri, Rig 02 Minas, Rig 03 Bekasap) beserta Penanggung Jawab (PJ Rig).
- **Jadwal Meeting (`/meeting`):** Buat jadwal meeting rutin mingguan (otomatis berulang setiap Senin/Selasa/dsb.) atau meeting sekali jalan (ad-hoc), lengkap dengan generator link Microsoft Teams dan pemilihan peserta crew.
- **Quick Reschedule:** Ubah jam atau tanggal meeting secara cepat lewat modal 1-klik.

### 2. Fase 2: Undangan WhatsApp (`/reminder`)
- **Template Dinamis:** Generator format pesan resmi undangan meeting dengan tag nama personil, nama meeting, rig, jadwal WIB, topik, dan tautan langsung Microsoft Teams.
- **Broadcast Cepat:** Kirim broadcast ke seluruh peserta atau personil tertentu per Rig.
- **Direct WhatsApp:** Integrasi link `wa.me` langsung ke aplikasi WhatsApp.

### 3. Fase 3: Pengingat Meeting Bertingkat (`/reminder/log` & `/api/check-reminders`)
- Pengingat berjenjang otomatis:
  - **H-1:** Pengingat persiapan rapat 1 hari sebelum meeting.
  - **H-1 Jam (60 Menit):** Pengingat kesiapan internet & perangkat Teams.
  - **H-15 Menit:** Panggilan segera bergabung ke ruang rapat Teams.
- **Scan Reminders Button:** Tombol di topbar untuk langsung memindai dan mengirimkan notifikasi antrian.

### 4. Fase 4: Monitoring Kehadiran Microsoft Teams (`/attendance/live`)
- **Live Attendance Dashboard:** Pantau badge status real-time:
  - 🟢 **Hadir (Tepat Waktu)**
  - 🟡 **Terlambat** (>10 menit setelah meeting dimulai)
  - ⚪ **Belum Hadir** (Meeting sedang berlangsung)
  - 🔴 **Tidak Hadir**
  - 🔵 **Izin / Sakit**
- **1-Click "Ingatkan Crew Belum Hadir":** Tombol instan untuk langsung mengirim reminder WhatsApp darurat ke personil yang belum masuk ruang rapat.
- **Teams Attendance Simulator (`/attendance/simulator`):** Simulasikan kedatangan crew on-time atau terlambat, serta fitur import file CSV Attendance resmi dari Microsoft Teams.
- **Koreksi Kehadiran Manual:** Manager dapat menyesuaikan status dan memberikan catatan khusus (misal: kendala sinyal di lapangan).

### 5. Fase 5: Rekap Kehadiran & Laporan Analitik (`/report`)
- **Analisis Kedisiplinan:** Menampilkan daftar peringkat personil crew yang paling sering tidak hadir / terlambat.
- **Performa per Unit Rig:** Perbandingan persentase kehadiran Rig 01 vs Rig 02 vs Rig 03.
- **Ekspor Excel:** Unduh rekap lengkap ke format file `.csv` (Excel compatible).
- **Cetak Laporan / PDF (`/report/print`):** Tampilan kop surat resmi PT Besmindo Oilfield Operations lengkap dengan kolom tanda tangan Manager & PJ Rig, siap dicetak atau disimpan sebagai PDF.

---

## 📁 Struktur Direktori CodeIgniter 4
```
Project Besmindo Reminder/
├── app/
│   ├── Config/          # Konfigurasi App, Database, Routes, Filters
│   ├── Controllers/     # Auth, Dashboard, Crew, Rig, Meeting, Reminder, Attendance, Report, Api, Install
│   ├── Filters/         # AuthFilter Middleware
│   ├── Models/          # UserModel, RigModel, CrewModel, MeetingModel, ReminderModel, AttendanceModel, SettingModel
│   └── Views/           # Templates Layouts, Auth, Dashboard, Crew, Rig, Meeting, Reminder, Attendance, Report, Install
├── public/
│   ├── assets/          # CSS kustom, JS app
│   └── index.php        # Front Controller CI4
├── database.sql         # Skema tabel & data seeder default
├── .env                 # Environment config
├── .htaccess            # Root URL redirection untuk XAMPP
└── README.md
```

&copy; PT Besmindo Oilfield Operations &bull; Rig Meeting & Attendance System
