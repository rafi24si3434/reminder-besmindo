<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header & Meeting Selector -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                <h1 class="text-2xl font-black text-white tracking-tight">Live Attendance Microsoft Teams</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Pemantauan kehadiran crew secara langsung selama meeting berlangsung</p>
        </div>

        <!-- Meeting Dropdown Selector -->
        <div class="flex flex-wrap items-center gap-3">
            <select onchange="window.location.href='<?= base_url('attendance/live/') ?>/' + this.value" class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-sky-500 focus:outline-none shadow-lg">
                <?php foreach ($meetings as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($meeting['id'] == $m['id']) ? 'selected' : '' ?>>
                        [<?= esc($m['rig_code']) ?>] <?= esc($m['title']) ?> (<?= date('d M', strtotime($m['meeting_date'])) ?> <?= substr($m['start_time'], 0, 5) ?> WIB)
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- 1-Click Reminder for Pending Crew (PRD Fitur I) -->
            <?php if ($stats['belum_hadir'] > 0 || $stats['tidak_hadir'] > 0): ?>
                <a href="<?= base_url('attendance/remindNotPresent/' . $meeting['id']) ?>" 
                    class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-lg shadow-amber-600/30 transition animate-pulse">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Ingatkan <?= $stats['belum_hadir'] + $stats['tidak_hadir'] ?> Crew Belum Hadir</span>
                </a>
            <?php endif; ?>

            <a href="<?= base_url('attendance/simulator?meeting_id=' . $meeting['id']) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs transition">
                <i class="fa-solid fa-gamepad text-amber-400"></i>
                <span>Buka Simulator</span>
            </a>
        </div>
    </div>

    <!-- Active Meeting Info Banner -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                    <?= esc($meeting['rig_name']) ?>
                </span>
                <span class="text-xs font-semibold text-slate-400">
                    <i class="fa-regular fa-clock mr-1"></i> <?= substr($meeting['start_time'], 0, 5) ?> - <?= substr($meeting['end_time'], 0, 5) ?> WIB
                </span>
            </div>
            <h2 class="text-lg font-bold text-white"><?= esc($meeting['title']) ?></h2>
            <p class="text-xs text-slate-400 line-clamp-1"><?= esc($meeting['topic']) ?></p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="<?= esc($meeting['teams_link']) ?>" target="_blank" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs flex items-center space-x-2 shadow-lg transition">
                <i class="fa-brands fa-microsoft"></i>
                <span>Masuk Teams Ruang Rapat</span>
            </a>
        </div>
    </div>

    <!-- Metric Counters (PRD Fitur A & H) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow text-center">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Peserta</span>
            <span class="text-2xl font-black text-white block mt-1"><?= $stats['total'] ?></span>
            <span class="text-[10px] text-slate-500">Crew Diundang</span>
        </div>

        <div class="bg-slate-900 border border-emerald-500/30 rounded-xl p-4 shadow text-center">
            <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider block">Hadir Tepat Waktu</span>
            <span class="text-2xl font-black text-emerald-400 block mt-1"><?= $stats['hadir'] ?></span>
            <span class="text-[10px] text-emerald-400/80">On-Time</span>
        </div>

        <div class="bg-slate-900 border border-amber-500/30 rounded-xl p-4 shadow text-center">
            <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">Terlambat</span>
            <span class="text-2xl font-black text-amber-400 block mt-1"><?= $stats['terlambat'] ?></span>
            <span class="text-[10px] text-amber-400/80">>10 Menit</span>
        </div>

        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 shadow text-center">
            <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Belum Hadir</span>
            <span class="text-2xl font-black text-slate-200 block mt-1"><?= $stats['belum_hadir'] ?></span>
            <span class="text-[10px] text-slate-400">Menunggu Masuk</span>
        </div>

        <div class="bg-slate-900 border border-indigo-500/30 rounded-xl p-4 shadow text-center col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-indigo-400 uppercase tracking-wider block">Persentase</span>
            <span class="text-2xl font-black text-indigo-400 block mt-1"><?= $stats['percentage'] ?>%</span>
            <span class="text-[10px] text-indigo-400/80">Kehadiran</span>
        </div>
    </div>

    <!-- Attendance Grid / Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center space-x-2">
                <i class="fa-solid fa-list-check text-sky-400"></i>
                <span>Status Kehadiran Real-Time Setiap Personil</span>
            </h3>
            <span class="text-xs text-slate-400">Auto-refresh ready &bull; Klik aksi untuk koreksi manual</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[11px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Personil Crew</th>
                        <th class="px-5 py-3.5">Jabatan</th>
                        <th class="px-5 py-3.5 text-center">Status Kehadiran</th>
                        <th class="px-5 py-3.5">Waktu Join</th>
                        <th class="px-5 py-3.5">Durasi</th>
                        <th class="px-5 py-3.5">Catatan / Keterangan</th>
                        <th class="px-5 py-3.5 text-right">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($attendances)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                                Tidak ada personil crew di meeting ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendances as $att): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sky-400 font-bold text-xs">
                                            <?= strtoupper(substr($att['crew_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white"><?= esc($att['crew_name']) ?></div>
                                            <div class="text-[10px] text-slate-500 font-mono"><?= esc($att['nik']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-300 font-medium">
                                    <?= esc($att['position']) ?>
                                </td>
                                <td class="px-5 py-3.5 text-center" id="statusBadge_<?= $att['id'] ?>">
                                    <?php if ($att['status'] === 'HADIR'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            <i class="fa-solid fa-check mr-1.5"></i> HADIR (Tepat Waktu)
                                        </span>
                                    <?php elseif ($att['status'] === 'TERLAMBAT'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                            <i class="fa-solid fa-clock mr-1.5"></i> TERLAMBAT
                                        </span>
                                    <?php elseif ($att['status'] === 'IZIN'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                            <i class="fa-solid fa-file-signature mr-1.5"></i> IZIN
                                        </span>
                                    <?php elseif ($att['status'] === 'TIDAK_HADIR'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                            <i class="fa-solid fa-xmark mr-1.5"></i> TIDAK HADIR
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                            <i class="fa-solid fa-hourglass-start mr-1.5"></i> BELUM HADIR
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-300" id="joinTime_<?= $att['id'] ?>">
                                    <?= $att['join_time'] ? date('H:i:s', strtotime($att['join_time'])) : '-' ?>
                                </td>
                                <td class="px-5 py-3.5 text-slate-300">
                                    <?= $att['duration_minutes'] > 0 ? $att['duration_minutes'] . ' Menit' : '-' ?>
                                </td>
                                <td class="px-5 py-3.5 text-slate-400 italic">
                                    <?= esc($att['notes'] ?: '-') ?>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <!-- Quick Simulator Trigger (On-Time) -->
                                        <button type="button" onclick="triggerSimulateJoin(<?= $att['id'] ?>, 'on_time')" title="Simulasikan Masuk Tepat Waktu" 
                                            class="p-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg transition border border-emerald-500/20">
                                            <i class="fa-solid fa-user-check"></i>
                                        </button>

                                        <!-- Quick Simulator Trigger (Late) -->
                                        <button type="button" onclick="triggerSimulateJoin(<?= $att['id'] ?>, 'late')" title="Simulasikan Masuk Terlambat" 
                                            class="p-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg transition border border-amber-500/20">
                                            <i class="fa-solid fa-user-clock"></i>
                                        </button>

                                        <!-- Manual Edit (PRD Fitur H Sub-fitur: Perbaiki Kehadiran Manual) -->
                                        <button type="button" onclick="openManualEdit(<?= $att['id'] ?>, '<?= esc($att['crew_name']) ?>', '<?= $att['status'] ?>', '<?= esc($att['notes'] ?? '') ?>')" title="Koreksi Kehadiran Manual" 
                                            class="p-1.5 text-slate-400 hover:text-sky-400 hover:bg-slate-800 rounded-lg transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Direct WhatsApp -->
                                        <?php
                                        $urgentMsg = "Halo " . $att['crew_name'] . ", Meeting " . $meeting['title'] . " telah dimulai di Microsoft Teams. Mohon segera bergabung: " . $meeting['teams_link'];
                                        ?>
                                        <button type="button" onclick="openWhatsApp('<?= $att['phone'] ?>', '<?= esc($urgentMsg, 'js') ?>')" title="Kirim Pesan WhatsApp Darurat" 
                                            class="p-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 rounded-lg transition">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Koreksi Kehadiran Manual -->
<div id="manualEditModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white flex items-center space-x-2">
                <i class="fa-solid fa-pen-to-square text-sky-400"></i>
                <span>Koreksi Kehadiran Manual</span>
            </h3>
            <button type="button" onclick="closeManualEdit()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?= base_url('attendance/updateStatusManual') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" id="editAttendanceId" name="attendance_id">

            <div>
                <span class="text-xs text-slate-400 block">Personil Crew:</span>
                <strong id="editCrewName" class="text-sm font-bold text-white"></strong>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Status Kehadiran <span class="text-rose-400">*</span></label>
                <select name="status" id="editStatus" required class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="HADIR">HADIR (Tepat Waktu)</option>
                    <option value="TERLAMBAT">TERLAMBAT</option>
                    <option value="IZIN">IZIN / SAKIT / OFF SHIFT</option>
                    <option value="TIDAK_HADIR">TIDAK HADIR (Mangkir)</option>
                    <option value="BELUM_HADIR">BELUM HADIR</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Catatan / Alasan Penyesuaian</label>
                <textarea name="notes" id="editNotes" rows="3" placeholder="Contoh: Sinyal rig sempat gangguan, izin pergantian shift..." class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeManualEdit()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-lg transition">
                    Simpan Koreksi
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function triggerSimulateJoin(attId, type) {
        fetch('<?= base_url('attendance/simulateJoin') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `attendance_id=${attId}&join_type=${type}`
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Simulasi Berhasil!',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false,
                    background: '#1e293b',
                    color: '#f8fafc'
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    function openManualEdit(id, name, status, notes) {
        document.getElementById('editAttendanceId').value = id;
        document.getElementById('editCrewName').innerText = name;
        document.getElementById('editStatus').value = status;
        document.getElementById('editNotes').value = notes;

        const modal = document.getElementById('manualEditModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeManualEdit() {
        const modal = document.getElementById('manualEditModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
<?= $this->endSection() ?>
