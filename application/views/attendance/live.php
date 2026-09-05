<div class="space-y-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Live Attendance Microsoft Teams</h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Pemantauan kehadiran crew secara langsung selama meeting berlangsung</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Modern & Professional Meeting Switcher -->
            <?php $this->load->view('components/meeting_switcher', array(
                'meetings'        => $meetings,
                'current_meeting' => $meeting,
                'target_route'    => 'attendance/live/'
            )); ?>

            <!-- Tombol Quick Sync: Tempel Roster Teams -->
            <button type="button" onclick="openQuickPasteModal()" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-clipboard-check text-xs"></i>
                <span>Tempel Peserta Teams</span>
            </button>

            <a href="<?= base_url('attendance/rekap/' . $meeting['id']) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-file-signature text-xs text-sky-600 dark:text-sky-400"></i>
                <span>Rekap Absensi</span>
            </a>

            <?php if ($meeting['status'] !== 'completed'): ?>
                <button type="button" onclick="openCloseSessionModal()" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs shadow-xs transition">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Tutup Sesi</span>
                </button>
            <?php endif; ?>

            <?php if ($meeting['status'] !== 'completed' && ($stats['belum_hadir'] > 0 || $stats['tidak_hadir'] > 0)): ?>
                <a href="<?= base_url('attendance/remind_not_present/' . $meeting['id']) ?>" 
                    class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-xs transition animate-pulse">
                    <i class="fa-brands fa-whatsapp text-xs"></i>
                    <span>Ingatkan <?= $stats['belum_hadir'] + $stats['tidak_hadir'] ?> Crew</span>
                </a>
            <?php endif; ?>

            <a href="<?= base_url('attendance/simulator?meeting_id=' . $meeting['id']) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-gamepad text-amber-500"></i>
                <span>Simulator</span>
            </a>
        </div>
    </div>

    <?php if ($meeting['status'] === 'completed'): ?>
        <div class="bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-500/30 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                    <i class="fa-solid fa-lock text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Sesi Rapat Telah Resmi Ditutup</h3>
                        <span class="text-[11px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 font-mono font-medium">
                            <?= !empty($meeting['closed_at']) ? date('d M Y, H:i', strtotime($meeting['closed_at'])) . ' WIB' : 'Completed' ?>
                        </span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                        Data kehadiran telah dikunci dan direkap. Anda dapat melihat notulensi atau mencetak laporan resmi.
                    </p>
                </div>
            </div>
            <a href="<?= base_url('attendance/rekap/' . $meeting['id']) ?>" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition flex items-center space-x-2 shrink-0">
                <i class="fa-solid fa-file-signature"></i>
                <span>Lihat Rekap &amp; Notulensi</span>
            </a>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-sky-500/10 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                    <?= htmlspecialchars($meeting['rig_name']) ?>
                </span>
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                    <i class="fa-regular fa-clock mr-1"></i> <?= substr($meeting['start_time'], 0, 5) ?> - <?= substr($meeting['end_time'], 0, 5) ?> WIB
                </span>
            </div>
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($meeting['title']) ?></h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-1"><?= htmlspecialchars($meeting['topic']) ?></p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="<?= htmlspecialchars($meeting['teams_link']) ?>" target="_blank" class="px-3.5 py-2 rounded-lg bg-[#5059C9] hover:bg-[#434BA6] text-white font-semibold text-xs flex items-center space-x-2 shadow-xs transition">
                <i class="fa-brands fa-microsoft"></i>
                <span>Masuk Teams Ruang Rapat</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs text-center">
            <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Total Peserta</span>
            <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 block mt-1" id="statTotal"><?= $stats['total'] ?></span>
            <span class="text-[10px] text-zinc-400 dark:text-zinc-500">Crew Diundang</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-emerald-500/30 rounded-xl p-4 shadow-xs text-center">
            <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Hadir Tepat Waktu</span>
            <span class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 block mt-1" id="statHadir"><?= $stats['hadir'] ?></span>
            <span class="text-[10px] text-emerald-600/70 dark:text-emerald-400/80">On-Time</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-amber-500/30 rounded-xl p-4 shadow-xs text-center">
            <span class="text-[11px] font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wider block">Terlambat</span>
            <span class="text-2xl font-bold tracking-tight text-amber-600 dark:text-amber-400 block mt-1" id="statTerlambat"><?= $stats['terlambat'] ?></span>
            <span class="text-[10px] text-amber-600/70 dark:text-amber-400/80">&gt;10 Menit</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs text-center">
            <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Belum Hadir</span>
            <span class="text-2xl font-bold tracking-tight text-zinc-700 dark:text-zinc-300 block mt-1" id="statBelumHadir"><?= $stats['belum_hadir'] ?></span>
            <span class="text-[10px] text-zinc-400 dark:text-zinc-500">Menunggu Masuk</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-indigo-500/30 rounded-xl p-4 shadow-xs text-center col-span-2 sm:col-span-1">
            <span class="text-[11px] font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Persentase</span>
            <span class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 block mt-1" id="statPercentage"><?= $stats['percentage'] ?>%</span>
            <span class="text-[10px] text-indigo-600/70 dark:text-indigo-400/80">Kehadiran</span>
        </div>
    </div>

    <!-- Attendance Live Table -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xs overflow-hidden">
        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <i class="fa-solid fa-list-check text-sky-600 dark:text-sky-400"></i>
                <span>Status Kehadiran Real-Time Setiap Personil</span>
            </h3>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Auto-refresh aktif &bull; Klik aksi untuk koreksi manual</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                <thead class="bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400 uppercase font-semibold text-[11px] tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-3">Personil Crew</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3 text-center">Status Kehadiran</th>
                        <th class="px-4 py-3">Waktu Join</th>
                        <th class="px-4 py-3">Durasi</th>
                        <th class="px-4 py-3">Catatan / Keterangan</th>
                        <th class="px-4 py-3 text-right">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    <?php if (empty($attendances)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada personil crew di meeting ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendances as $att): ?>
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-sky-600 dark:text-sky-400 font-bold text-xs">
                                            <?= strtoupper(substr($att['crew_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($att['crew_name']) ?></div>
                                            <div class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono"><?= htmlspecialchars($att['nik']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300 font-medium">
                                    <?= htmlspecialchars($att['position']) ?>
                                </td>
                                <td class="px-4 py-3 text-center" id="statusBadge_<?= $att['id'] ?>">
                                    <?php if ($att['status'] === 'HADIR'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            <i class="fa-solid fa-check mr-1"></i> HADIR (Tepat Waktu)
                                        </span>
                                    <?php elseif ($att['status'] === 'TERLAMBAT'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            <i class="fa-solid fa-clock mr-1"></i> TERLAMBAT
                                        </span>
                                    <?php elseif ($att['status'] === 'IZIN'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                            <i class="fa-solid fa-file-signature mr-1"></i> IZIN
                                        </span>
                                    <?php elseif ($att['status'] === 'TIDAK_HADIR'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            <i class="fa-solid fa-xmark mr-1"></i> TIDAK HADIR
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                            <i class="fa-solid fa-hourglass-start mr-1"></i> BELUM HADIR
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-mono text-zinc-600 dark:text-zinc-300" id="joinTime_<?= $att['id'] ?>">
                                    <?= $att['join_time'] ? date('H:i:s', strtotime($att['join_time'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300" id="duration_<?= $att['id'] ?>">
                                    <?= $att['duration_minutes'] > 0 ? $att['duration_minutes'] . ' Menit' : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 italic" id="notes_<?= $att['id'] ?>">
                                    <?= htmlspecialchars($att['notes'] ?: '-') ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <button type="button" onclick="triggerSimulateJoin(<?= $att['id'] ?>, 'on_time')" title="Simulasikan Masuk Tepat Waktu" 
                                            class="p-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-md transition border border-emerald-500/20">
                                            <i class="fa-solid fa-user-check text-xs"></i>
                                        </button>
                                        <button type="button" onclick="triggerSimulateJoin(<?= $att['id'] ?>, 'late')" title="Simulasikan Masuk Terlambat" 
                                            class="p-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-md transition border border-amber-500/20">
                                            <i class="fa-solid fa-user-clock text-xs"></i>
                                        </button>
                                        <button type="button" onclick="openManualEdit(<?= $att['id'] ?>, '<?= htmlspecialchars($att['crew_name']) ?>', '<?= $att['status'] ?>', '<?= htmlspecialchars($att['notes'] ?: '') ?>')" title="Koreksi Kehadiran Manual" 
                                            class="p-1.5 text-zinc-400 hover:text-sky-600 dark:hover:text-sky-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                        <?php
                                        $urgentMsg = "Halo " . $att['crew_name'] . ", Meeting " . $meeting['title'] . " telah dimulai di Microsoft Teams. Mohon segera bergabung: " . $meeting['teams_link'];
                                        ?>
                                        <button type="button" onclick="openWhatsApp('<?= $att['phone'] ?>', '<?= addslashes($urgentMsg) ?>')" title="Kirim Pesan WhatsApp Darurat" 
                                            class="p-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-md transition">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
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

<!-- Manual Edit Modal -->
<div id="manualEditModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <i class="fa-solid fa-pen-to-square text-sky-600 dark:text-sky-400"></i>
                <span>Koreksi Kehadiran Manual</span>
            </h3>
            <button type="button" onclick="closeManualEdit()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?= base_url('attendance/update_status_manual') ?>" method="POST" class="space-y-4">
            <input type="hidden" id="editAttendanceId" name="attendance_id">

            <div>
                <span class="text-xs text-zinc-500 dark:text-zinc-400 block">Personil Crew:</span>
                <strong id="editCrewName" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"></strong>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">Status Kehadiran <span class="text-rose-500">*</span></label>
                <select name="status" id="editStatus" required class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                    <option value="HADIR">HADIR (Tepat Waktu)</option>
                    <option value="TERLAMBAT">TERLAMBAT</option>
                    <option value="IZIN">IZIN / SAKIT / OFF SHIFT</option>
                    <option value="TIDAK_HADIR">TIDAK HADIR (Mangkir)</option>
                    <option value="BELUM_HADIR">BELUM HADIR</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">Catatan / Alasan Penyesuaian</label>
                <textarea name="notes" id="editNotes" rows="3" placeholder="Contoh: Sinyal rig sempat gangguan, izin pergantian shift..." class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition"></textarea>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeManualEdit()" class="px-4 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-zinc-900 dark:bg-zinc-50 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-semibold shadow-xs transition">
                    Simpan Koreksi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Quick Paste Roster Teams -->
<div id="quickPasteModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-xl w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <div class="flex items-center space-x-3">
                <span class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xl shrink-0">
                    📋
                </span>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Sinkronisasi Cepat Peserta Teams</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Salin daftar peserta di Teams, tempel di sini &rarr; status langsung HADIR!</p>
                </div>
            </div>
            <button type="button" onclick="closeQuickPasteModal()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="space-y-3">
            <div class="bg-zinc-50 dark:bg-zinc-950 p-3.5 rounded-lg border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 space-y-1.5">
                <div class="font-semibold text-emerald-600 dark:text-emerald-400 flex items-center space-x-1.5">
                    <i class="fa-solid fa-lightbulb"></i>
                    <span>Cara Cepat 3 Detik:</span>
                </div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    1. Di tab Microsoft Teams, buka panel <strong>Participants (Peserta)</strong>.<br>
                    2. Sorot / blok nama peserta (atau ketik nama yang hadir per baris), lalu <strong>Copy</strong>.<br>
                    3. <strong>Paste</strong> di kotak bawah ini lalu klik <strong>"Proses Hadir Sekarang"</strong>.
                </p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                    Daftar Nama Peserta Teams (Pisahkan dengan baris baru atau koma):
                </label>
                <textarea id="quickPasteInput" rows="6" placeholder="Contoh:&#10;Muhammad Rafi&#10;Fhara&#10;Syabil Al Jabbar&#10;Salsabila Adinda&#10;Farid Athaya" class="w-full px-4 py-3 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:outline-none transition"></textarea>
            </div>

            <div id="quickPasteResult" class="hidden p-3 rounded-lg text-xs font-medium"></div>
        </div>

        <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end space-x-3">
            <button type="button" onclick="closeQuickPasteModal()" class="px-4 py-2.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition">
                Batal
            </button>
            <button type="button" id="btnProcessQuickPaste" onclick="processQuickPaste()" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-xs transition flex items-center space-x-2">
                <i class="fa-solid fa-check-double"></i>
                <span>Proses Hadir Sekarang</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Tutup Sesi Rapat -->
<div id="closeSessionModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <i class="fa-solid fa-lock text-rose-600 dark:text-rose-400"></i>
                <span>Selesaikan &amp; Tutup Sesi Rapat Ini</span>
            </h3>
            <button type="button" onclick="closeCloseSessionModal()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('attendance/close_session') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="meeting_id" value="<?= $meeting['id'] ?>">

            <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-500/30 rounded-lg p-3 text-xs text-rose-800 dark:text-rose-200">
                <p class="font-semibold flex items-center space-x-1.5 mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                    <span>Konfirmasi Penutupan Sesi:</span>
                </p>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700/90 dark:text-rose-300/90">
                    <li>Seluruh crew yang berstatus <strong>BELUM HADIR</strong> akan diubah otomatis menjadi <strong>TIDAK HADIR (Alpha)</strong>.</li>
                    <li>Status rapat menjadi <strong>COMPLETED</strong> dan pengingat WA terjadwal dinonaktifkan.</li>
                    <li>Sistem akan merekap statistik dan Anda dapat mencetak laporan resmi.</li>
                </ul>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                    Notulensi / Kesimpulan Hasil Rapat:
                </label>
                <textarea name="meeting_notes" rows="4" placeholder="Catatan singkat hasil keputusan rapat (opsional)..." class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 focus:outline-none transition"><?= htmlspecialchars($meeting['meeting_notes'] ?? '') ?></textarea>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-950/60 p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 flex items-center space-x-3">
                <input type="checkbox" id="broadcastRecapCheck" name="broadcast_recap_wa" value="1" <?= !empty($meeting['wa_group_id']) ? 'checked' : '' ?> class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500/20 bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700">
                <label for="broadcastRecapCheck" class="text-xs text-zinc-700 dark:text-zinc-300 cursor-pointer">
                    Kirim langsung rekapitulasi kehadiran ke WhatsApp Group Rig 
                    <?php if (!empty($meeting['wa_group_id'])): ?>
                        <span class="font-mono text-emerald-600 dark:text-emerald-400 text-[11px] block"><?= htmlspecialchars($meeting['wa_group_id']) ?></span>
                    <?php endif; ?>
                </label>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeCloseSessionModal()" class="px-4 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold shadow-xs transition">
                    Tutup &amp; Rekap Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function triggerSimulateJoin(attId, type) {
        const isDark = document.documentElement.classList.contains('dark');
        fetch('<?= base_url('attendance/simulate_join') ?>', {
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
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b'
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

    function openCloseSessionModal() {
        const modal = document.getElementById('closeSessionModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCloseSessionModal() {
        const modal = document.getElementById('closeSessionModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openQuickPasteModal() {
        const modal = document.getElementById('quickPasteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('quickPasteResult').classList.add('hidden');
        setTimeout(() => document.getElementById('quickPasteInput').focus(), 100);
    }

    function closeQuickPasteModal() {
        const modal = document.getElementById('quickPasteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function processQuickPaste() {
        const isDark = document.documentElement.classList.contains('dark');
        const input = document.getElementById('quickPasteInput').value.trim();
        const resBox = document.getElementById('quickPasteResult');
        const btn = document.getElementById('btnProcessQuickPaste');

        if (!input) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Kosong',
                text: 'Silakan tempel nama peserta Teams terlebih dahulu!',
                background: isDark ? '#18181b' : '#ffffff',
                color: isDark ? '#f4f4f5' : '#18181b'
            });
            return;
        }

        const rawLines = input.split(/[\r\n,]+/);
        const names = [];
        rawLines.forEach(l => {
            const clean = l.trim();
            if (clean.length >= 2) names.push(clean);
        });

        if (names.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Nama Tidak Terbaca',
                text: 'Pastikan format nama valid.',
                background: isDark ? '#18181b' : '#ffffff',
                color: isDark ? '#f4f4f5' : '#18181b'
            });
            return;
        }

        const originalBtn = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Memproses...</span>';

        fetch('<?= base_url('api/sync_teams_live') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                meeting_id: <?= $meeting['id'] ?>,
                names: names
            })
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = originalBtn;

            if (res.success) {
                resBox.className = 'p-3 rounded-lg text-xs bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-500/40 text-emerald-800 dark:text-emerald-300 space-y-1 block';
                resBox.innerHTML = `<strong>🎉 Berhasil!</strong> ${res.matched_count} personil cocok dari ${res.received_count} nama yang ditempel.<br>Status kehadiran di dashboard otomatis terupdate!`;

                Swal.fire({
                    icon: 'success',
                    title: 'Absensi Terupdate!',
                    html: `<b>${res.matched_count} crew</b> otomatis dinyatakan <b>HADIR</b> di sistem!`,
                    timer: 1600,
                    showConfirmButton: false,
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b'
                }).then(() => {
                    closeQuickPasteModal();
                    pollLiveAttendance();
                });
            } else {
                resBox.className = 'p-3 rounded-lg text-xs bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-500/40 text-rose-800 dark:text-rose-300 block';
                resBox.innerHTML = `<strong>Gagal:</strong> ${res.message || 'Terjadi kesalahan'}`;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalBtn;
            resBox.className = 'p-3 rounded-lg text-xs bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-500/40 text-rose-800 dark:text-rose-300 block';
            resBox.innerHTML = `<strong>Error Koneksi:</strong> ${err.toString()}`;
        });
    }

    // Polling Real-Time Attendance setiap 6 detik
    function pollLiveAttendance() {
        fetch('<?= base_url('api/get_live_attendance/' . $meeting['id']) ?>')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update KPI stats
                const st = data.stats;
                if (document.getElementById('statTotal')) document.getElementById('statTotal').innerText = st.total;
                if (document.getElementById('statHadir')) document.getElementById('statHadir').innerText = st.hadir;
                if (document.getElementById('statTerlambat')) document.getElementById('statTerlambat').innerText = st.terlambat;
                if (document.getElementById('statBelumHadir')) document.getElementById('statBelumHadir').innerText = st.belum_hadir;
                if (document.getElementById('statPercentage')) document.getElementById('statPercentage').innerText = st.percentage + '%';

                // Update row badges
                if (data.attendances && Array.isArray(data.attendances)) {
                    data.attendances.forEach(att => {
                        const badgeCell = document.getElementById('statusBadge_' + att.id);
                        if (badgeCell) {
                            let badgeHtml = '';
                            if (att.status === 'HADIR') {
                                badgeHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20"><i class="fa-solid fa-check mr-1"></i> HADIR (Tepat Waktu)</span>';
                            } else if (att.status === 'TERLAMBAT') {
                                badgeHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20"><i class="fa-solid fa-clock mr-1"></i> TERLAMBAT</span>';
                            } else if (att.status === 'IZIN') {
                                badgeHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20"><i class="fa-solid fa-file-signature mr-1"></i> IZIN</span>';
                            } else if (att.status === 'TIDAK_HADIR') {
                                badgeHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20"><i class="fa-solid fa-xmark mr-1"></i> TIDAK HADIR</span>';
                            } else {
                                badgeHtml = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700"><i class="fa-solid fa-hourglass-start mr-1"></i> BELUM HADIR</span>';
                            }
                            badgeCell.innerHTML = badgeHtml;
                        }

                        const joinCell = document.getElementById('joinTime_' + att.id);
                        if (joinCell && att.join_time) {
                            joinCell.innerText = att.join_time.split(' ')[1];
                        }

                        const durCell = document.getElementById('duration_' + att.id);
                        if (durCell && att.duration_minutes > 0) {
                            durCell.innerText = att.duration_minutes + ' Menit';
                        }

                        const notesCell = document.getElementById('notes_' + att.id);
                        if (notesCell && att.notes) {
                            notesCell.innerText = att.notes;
                        }
                    });
                }
            }
        })
        .catch(() => {});
    }

    // Mulai polling saat halaman aktif
    setInterval(pollLiveAttendance, 6000);
</script>
