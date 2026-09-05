<div class="space-y-6">

    <!-- Page Header & Welcome Banner -->
    <div class="rounded-xl border p-6 sm:p-8 shadow-sm transition-colors
        bg-white dark:bg-zinc-900
        border-zinc-200 dark:border-zinc-800">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-2.5 py-1 rounded-md text-xs font-medium mb-3
                    bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-900">
                    <i class="fa-solid fa-tower-observation text-[11px]"></i>
                    <span>Rig Operational Operations Center</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    Monitoring Kehadiran &amp; Reminder Meeting Crew Rig
                </h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1 max-w-2xl leading-relaxed">
                    Pantau jadwal meeting rutin seluruh unit Rig Besmindo, kirim reminder WhatsApp otomatis, dan verifikasi kehadiran Microsoft Teams secara real-time.
                </p>
            </div>
            <div class="flex items-center space-x-2.5 flex-shrink-0">
                <a href="<?= base_url('meeting/create') ?>"
                    class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition
                        bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Buat Jadwal Baru</span>
                </a>
                <a href="<?= base_url('attendance/live') ?>"
                    class="inline-flex items-center space-x-2 px-4 py-2 rounded-lg text-xs font-medium transition border shadow-sm
                        bg-zinc-100 hover:bg-zinc-200 text-zinc-800 border-zinc-200
                        dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-700">
                    <i class="fa-solid fa-video text-indigo-500"></i>
                    <span>Live Teams</span>
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards (Shadcn Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="rounded-xl border p-5 shadow-sm transition-colors
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Meeting Hari Ini</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                    bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 border border-sky-100 dark:border-sky-900">
                    <i class="fa-regular fa-calendar-check text-sm"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50"><?= count($today_meetings) ?></div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1"><?= count($upcoming_meetings) ?> meeting terjadwal ke depan</p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="rounded-xl border p-5 shadow-sm transition-colors
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Hadir Tepat Waktu</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                    bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900">
                    <i class="fa-solid fa-user-check text-sm"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">
                    <?= $attendance_stats['hadir'] ?> <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">Crew</span>
                </div>
                <p class="text-xs text-emerald-600/80 dark:text-emerald-400/80 mt-1 flex items-center space-x-1">
                    <i class="fa-solid fa-check text-[10px]"></i>
                    <span>Tepat waktu di MS Teams</span>
                </p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="rounded-xl border p-5 shadow-sm transition-colors
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Terlambat / Pending</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                    bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900">
                    <i class="fa-solid fa-user-clock text-sm"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-amber-600 dark:text-amber-400">
                    <?= $attendance_stats['terlambat'] ?> <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">Late / <?= $attendance_stats['belum_hadir'] ?> Pending</span>
                </div>
                <p class="text-xs text-amber-600/80 dark:text-amber-400/80 mt-1 flex items-center space-x-1">
                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                    <span>Follow-up WhatsApp</span>
                </p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="rounded-xl border p-5 shadow-sm transition-colors
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Persentase Kehadiran</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                    bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900">
                    <i class="fa-solid fa-chart-line text-sm"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400"><?= $attendance_stats['percentage'] ?>%</div>
                <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full mt-2 overflow-hidden">
                    <div class="bg-indigo-500 h-full rounded-full transition-all" style="width: <?= min(100, $attendance_stats['percentage']) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Meeting Hari Ini & Unit Rig -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Meeting Hari Ini -->
        <div class="lg:col-span-2 rounded-xl border p-5 sm:p-6 shadow-sm transition-colors
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                        <i class="fa-regular fa-clock text-sky-500"></i>
                        <span>Jadwal Meeting Hari Ini (<?= date('d M Y') ?>)</span>
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Daftar meeting yang berlangsung atau akan dimulai hari ini</p>
                </div>
                <a href="<?= base_url('meeting') ?>" class="text-xs text-sky-600 dark:text-sky-400 hover:underline font-medium">Lihat Semua &rarr;</a>
            </div>

            <?php if (empty($today_meetings)): ?>
                <div class="text-center py-10 border border-dashed rounded-lg border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50">
                    <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 mx-auto mb-2.5">
                        <i class="fa-regular fa-calendar-xmark text-lg"></i>
                    </div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tidak ada jadwal meeting hari ini</p>
                    <a href="<?= base_url('meeting/create') ?>" class="mt-3 inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200 transition">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Buat Jadwal Baru</span>
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($today_meetings as $m): ?>
                        <div class="rounded-lg border p-4 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3
                            bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800
                            border-zinc-200 dark:border-zinc-800">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                        <?= htmlspecialchars($m['rig_name']) ?>
                                    </span>
                                    <?php if ($m['status'] === 'in_progress'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1.5"></span> BERLANGSUNG
                                        </span>
                                    <?php elseif ($m['status'] === 'completed'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">SELESAI</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">TERJADWAL</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($m['title']) ?></h3>
                                <div class="flex flex-wrap items-center text-xs text-zinc-500 dark:text-zinc-400 gap-x-4 gap-y-1">
                                    <span><i class="fa-regular fa-clock mr-1 text-zinc-400"></i> <?= substr($m['start_time'], 0, 5) ?> - <?= substr($m['end_time'], 0, 5) ?> WIB</span>
                                    <span><i class="fa-solid fa-user-tie mr-1 text-zinc-400"></i> PJ: <?= htmlspecialchars($m['pj_name'] ?: '-') ?></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <a href="<?= htmlspecialchars($m['teams_link']) ?>" target="_blank" class="px-2.5 py-1.5 rounded-lg border text-xs font-medium flex items-center space-x-1.5 transition
                                    bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border-indigo-200
                                    dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 dark:text-indigo-300 dark:border-indigo-800">
                                    <i class="fa-brands fa-microsoft text-xs"></i>
                                    <span>Teams</span>
                                </a>
                                <a href="<?= base_url('attendance/live/' . $m['id']) ?>" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-white shadow-sm flex items-center space-x-1.5 transition
                                    bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                                    <i class="fa-solid fa-chart-simple text-xs"></i>
                                    <span>Monitoring</span>
                                </a>
                                <a href="<?= base_url('reminder/broadcast/' . $m['id']) ?>" title="Broadcast WhatsApp"
                                    class="p-2 rounded-lg border text-xs transition
                                        bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200
                                        dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 dark:text-emerald-400 dark:border-emerald-800">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Status Unit Rig -->
        <div class="rounded-xl border p-5 sm:p-6 shadow-sm transition-colors
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                        <i class="fa-solid fa-tower-observation text-amber-500"></i>
                        <span>Status Unit Rig</span>
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5"><?= $total_rigs ?> Rig &bull; <?= $total_crews ?> Crew</p>
                </div>
                <a href="<?= base_url('rig') ?>" class="text-xs text-sky-600 dark:text-sky-400 hover:underline font-medium">Kelola &rarr;</a>
            </div>

            <div class="space-y-2.5">
                <?php foreach ($rig_summaries as $r): ?>
                    <div class="p-3 rounded-lg border transition-colors space-y-1.5
                        bg-zinc-50 dark:bg-zinc-800/40 border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($r['name']) ?></span>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars($r['code']) ?></span>
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center justify-between">
                            <span class="truncate"><i class="fa-solid fa-location-dot text-zinc-400 mr-1"></i> <?= htmlspecialchars($r['location']) ?></span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-medium ml-2 flex-shrink-0"><?= $r['total_crew'] ?> Crew</span>
                        </div>
                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400 pt-1.5 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                            <span>PJ: <strong class="text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars($r['pj_name'] ?: '-') ?></strong></span>
                            <a href="<?= base_url('crew?rig_id=' . $r['id']) ?>" class="text-sky-600 dark:text-sky-400 hover:underline">Crew &rarr;</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Charts & Outbox Log -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border p-5 sm:p-6 shadow-sm transition-colors
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                    <i class="fa-solid fa-chart-pie text-indigo-500"></i>
                    <span>Proporsi Status Kehadiran Meeting</span>
                </h3>
                <a href="<?= base_url('report') ?>" class="text-xs text-sky-600 dark:text-sky-400 hover:underline font-medium">Laporan Analitik</a>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="attendanceDonutChart"></canvas>
            </div>
        </div>

        <div class="rounded-xl border p-5 sm:p-6 shadow-sm transition-colors
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                    <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                    <span>Aktivitas Pengiriman Reminder Terbaru</span>
                </h3>
                <a href="<?= base_url('reminder/log') ?>" class="text-xs text-sky-600 dark:text-sky-400 hover:underline font-medium">Lihat Outbox</a>
            </div>

            <?php if (empty($recent_reminders)): ?>
                <div class="text-center py-10 text-xs text-zinc-400">
                    Belum ada riwayat pengiriman reminder WhatsApp hari ini.
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($recent_reminders as $log): ?>
                        <div class="p-3 rounded-lg border flex items-center justify-between text-xs transition-colors
                            bg-zinc-50 dark:bg-zinc-800/40 border-zinc-200 dark:border-zinc-800">
                            <div class="space-y-0.5">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($log['crew_name'] ?: 'Crew') ?></span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800"><?= htmlspecialchars($log['reminder_type']) ?></span>
                                </div>
                                <p class="text-zinc-500 dark:text-zinc-400 truncate max-w-xs text-[11px]"><?= htmlspecialchars($log['meeting_title'] ?: '') ?></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">
                                    <i class="fa-solid fa-check-double mr-1 text-[10px]"></i> Terkirim
                                </span>
                                <p class="text-[10px] text-zinc-400 mt-0.5"><?= date('H:i', strtotime($log['sent_at'])) ?> WIB</p>
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
        const isDarkTheme = document.documentElement.classList.contains('dark');
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
                        '#71717a',
                        '#f43f5e',
                        '#0284c7'
                    ],
                    borderColor: isDarkTheme ? '#18181b' : '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: isDarkTheme ? '#a1a1aa' : '#52525b',
                            font: { size: 11, family: 'Inter' }
                        }
                    }
                },
                cutout: '72%'
            }
        });
    });
</script>

