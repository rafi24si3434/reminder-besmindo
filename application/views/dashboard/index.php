<div class="space-y-6">

    <!-- Page Header & Welcome Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-sky-950 to-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/20 mb-3">
                    <i class="fa-solid fa-tower-observation"></i>
                    <span>Rig Operational Operations Center</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Monitoring Kehadiran & Reminder Meeting Crew Rig
                </h1>
                <p class="text-slate-400 text-sm mt-1 max-w-2xl">
                    Pantau jadwal meeting rutin seluruh unit Rig Besmindo, kirim reminder WhatsApp otomatis, dan verifikasi kehadiran Microsoft Teams secara real-time.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="<?= base_url('meeting/create') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-sm shadow-lg shadow-sky-600/30 transition">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Buat Jadwal Baru</span>
                </a>
                <a href="<?= base_url('attendance/live') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-sm transition">
                    <i class="fa-solid fa-video text-indigo-400"></i>
                    <span>Live Teams</span>
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- KPI Metric Cards (PRD Fitur A) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Meeting Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center border border-sky-500/20">
                    <i class="fa-regular fa-calendar-check text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-extrabold text-white"><?= count($today_meetings) ?></div>
                <div class="flex items-center space-x-1.5 text-xs text-slate-400 mt-1">
                    <span><?= count($upcoming_meetings) ?> meeting terjadwal ke depan</span>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hadir Tepat Waktu</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <i class="fa-solid fa-user-check text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-extrabold text-emerald-400"><?= $attendance_stats['hadir'] ?> <span class="text-xs font-normal text-slate-400">Crew</span></div>
                <div class="flex items-center space-x-1 text-xs text-emerald-400/80 mt-1">
                    <i class="fa-solid fa-check"></i>
                    <span>Tepat waktu di MS Teams</span>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terlambat / Pending</span>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <i class="fa-solid fa-user-clock text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-extrabold text-amber-400">
                    <?= $attendance_stats['terlambat'] ?> <span class="text-sm font-normal text-slate-400">Late / <?= $attendance_stats['belum_hadir'] ?> Pending</span>
                </div>
                <div class="flex items-center space-x-1 text-xs text-amber-400/80 mt-1">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Follow-up WhatsApp</span>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Persentase Kehadiran</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                    <i class="fa-solid fa-chart-line text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-extrabold text-indigo-400"><?= $attendance_stats['percentage'] ?>%</div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-sky-500 to-indigo-500 h-full rounded-full" style="width: <?= min(100, $attendance_stats['percentage']) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Meeting Hari Ini & Unit Rig -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <i class="fa-regular fa-clock text-sky-400"></i>
                        <span>Jadwal Meeting Hari Ini (<?= date('d M Y') ?>)</span>
                    </h2>
                    <p class="text-xs text-slate-400">Daftar meeting yang berlangsung atau akan dimulai hari ini</p>
                </div>
                <a href="<?= base_url('meeting') ?>" class="text-xs text-sky-400 hover:text-sky-300 font-medium">Lihat Semua &rarr;</a>
            </div>

            <?php if (empty($today_meetings)): ?>
                <div class="text-center py-10 border border-dashed border-slate-800 rounded-xl bg-slate-950/40">
                    <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-slate-500 mx-auto mb-3">
                        <i class="fa-regular fa-calendar-xmark text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-300">Tidak ada jadwal meeting hari ini</p>
                    <a href="<?= base_url('meeting/create') ?>" class="mt-3 inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-xs font-semibold text-white transition">
                        <i class="fa-solid fa-plus"></i>
                        <span>Buat Jadwal Baru</span>
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($today_meetings as $m): ?>
                        <div class="bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 rounded-xl p-4 transition duration-150 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                        <?= htmlspecialchars($m['rig_name']) ?>
                                    </span>
                                    <?php if ($m['status'] === 'in_progress'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping mr-1"></span> BERLANGSUNG
                                        </span>
                                    <?php elseif ($m['status'] === 'completed'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-700 text-slate-300">SELESAI</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400">TERJADWAL</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-sm font-bold text-white"><?= htmlspecialchars($m['title']) ?></h3>
                                <div class="flex flex-wrap items-center text-xs text-slate-400 gap-x-4 gap-y-1">
                                    <span><i class="fa-regular fa-clock mr-1 text-slate-500"></i> <?= substr($m['start_time'], 0, 5) ?> - <?= substr($m['end_time'], 0, 5) ?> WIB</span>
                                    <span><i class="fa-solid fa-user-tie mr-1 text-slate-500"></i> PJ: <?= htmlspecialchars($m['pj_name'] ?: '-') ?></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="<?= htmlspecialchars($m['teams_link']) ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 text-xs font-semibold flex items-center space-x-1.5 transition">
                                    <i class="fa-brands fa-microsoft text-sm"></i>
                                    <span>Teams</span>
                                </a>
                                <a href="<?= base_url('attendance/live/' . $m['id']) ?>" class="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold flex items-center space-x-1.5 shadow transition">
                                    <i class="fa-solid fa-chart-simple"></i>
                                    <span>Monitoring</span>
                                </a>
                                <a href="<?= base_url('reminder/broadcast/' . $m['id']) ?>" class="px-2.5 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 text-xs transition">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <i class="fa-solid fa-oil-well text-amber-400"></i>
                        <span>Status Unit Rig</span>
                    </h2>
                    <p class="text-xs text-slate-400">Total <?= $total_rigs ?> Rig Aktif &bull; <?= $total_crews ?> Crew Terdaftar</p>
                </div>
                <a href="<?= base_url('rig') ?>" class="text-xs text-sky-400 hover:text-sky-300 font-medium">Kelola &rarr;</a>
            </div>

            <div class="space-y-3">
                <?php foreach ($rig_summaries as $r): ?>
                    <div class="p-3.5 bg-slate-800/50 border border-slate-700/50 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-white"><?= htmlspecialchars($r['name']) ?></span>
                            <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-700 text-sky-300"><?= htmlspecialchars($r['code']) ?></span>
                        </div>
                        <div class="text-xs text-slate-400 flex items-center justify-between">
                            <span><i class="fa-solid fa-location-dot text-slate-500 mr-1"></i> <?= htmlspecialchars($r['location']) ?></span>
                            <span class="text-emerald-400 font-semibold"><i class="fa-solid fa-users mr-1"></i> <?= $r['total_crew'] ?> Crew</span>
                        </div>
                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-700/40 flex items-center justify-between">
                            <span>PJ: <strong class="text-slate-300"><?= htmlspecialchars($r['pj_name']) ?></strong></span>
                            <a href="<?= base_url('crew?rig_id=' . $r['id']) ?>" class="text-sky-400 hover:underline">Lihat Crew</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Charts & Outbox Log -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center space-x-2">
                    <i class="fa-solid fa-chart-pie text-indigo-400"></i>
                    <span>Proporsi Status Kehadiran Meeting</span>
                </h3>
                <a href="<?= base_url('report') ?>" class="text-xs text-sky-400 hover:underline">Laporan Analitik</a>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="attendanceDonutChart"></canvas>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center space-x-2">
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                    <span>Aktivitas Pengiriman Reminder Terbaru</span>
                </h3>
                <a href="<?= base_url('reminder/log') ?>" class="text-xs text-sky-400 hover:underline">Lihat Outbox</a>
            </div>

            <?php if (empty($recent_reminders)): ?>
                <div class="text-center py-10 text-xs text-slate-500">
                    Belum ada riwayat pengiriman reminder WhatsApp hari ini.
                </div>
            <?php else: ?>
                <div class="space-y-2.5">
                    <?php foreach ($recent_reminders as $log): ?>
                        <div class="p-3 bg-slate-800/40 border border-slate-700/50 rounded-xl flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-white"><?= htmlspecialchars($log['crew_name'] ?: 'Crew') ?></span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-sky-500/20 text-sky-300"><?= htmlspecialchars($log['reminder_type']) ?></span>
                                </div>
                                <p class="text-slate-400 truncate max-w-xs text-[11px]"><?= htmlspecialchars($log['meeting_title'] ?: '') ?></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center text-[11px] text-emerald-400 font-semibold">
                                    <i class="fa-solid fa-check-double mr-1 text-[10px]"></i> Terkirim
                                </span>
                                <p class="text-[10px] text-slate-500"><?= date('H:i', strtotime($log['sent_at'])) ?> WIB</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('attendanceDonutChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir Tepat Waktu', 'Terlambat', 'Belum Hadir', 'Tidak Hadir', 'Izin'],
                datasets: [{
                    data: [
                        <?= $attendance_stats['hadir'] ?>,
                        <?= $attendance_stats['terlambat'] ?>,
                        <?= $attendance_stats['belum_hadir'] ?>,
                        <?= $attendance_stats['tidak_hadir'] ?>,
                        <?= $attendance_stats['izin'] ?>
                    ],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#64748b',
                        '#f43f5e',
                        '#3b82f6'
                    ],
                    borderColor: '#0f172a',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            font: { size: 11 }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
