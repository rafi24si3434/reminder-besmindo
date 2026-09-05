<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-chart-pie text-2xl text-amber-500"></i>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Rekap & Analitik Kehadiran Crew Rig</h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Laporan rekapitulasi kehadiran, analisis kedisiplinan crew, dan performa meeting per rig</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= base_url('report/export_excel?' . http_build_query($_GET)) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-file-excel"></i>
                <span>Download Excel (.CSV)</span>
            </a>
            <a href="<?= base_url('report/print_report?' . http_build_query($_GET)) ?>" target="_blank" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-print text-sky-600 dark:text-sky-400"></i>
                <span>Cetak Laporan / PDF</span>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs">
        <form method="GET" action="<?= base_url('report') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div>
                    <select name="rig_id" onchange="this.form.submit()" class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                        <option value="">-- Semua Unit Rig --</option>
                        <?php foreach ($rigs as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($selectedRig == $r['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center space-x-2 text-xs text-zinc-500 dark:text-zinc-400">
                    <span>Dari:</span>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($startDate ?: '') ?>" onchange="this.form.submit()" class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                </div>
                <div class="flex items-center space-x-2 text-xs text-zinc-500 dark:text-zinc-400">
                    <span>Sampai:</span>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($endDate ?: '') ?>" onchange="this.form.submit()" class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                </div>
                <?php if ($selectedRig || $startDate || $endDate): ?>
                    <a href="<?= base_url('report') ?>" class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 flex items-center space-x-1 font-medium transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset Filter</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-7 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <div>
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                        <span>Personil Crew Paling Sering Terlambat / Absen</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Evaluasi kedisiplinan dan bahan tindak lanjut Manager Operasional</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400 uppercase font-semibold text-[10px] tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-3 py-2.5">Personil Crew</th>
                            <th class="px-3 py-2.5">Rig</th>
                            <th class="px-3 py-2.5 text-center text-rose-600 dark:text-rose-400">Tidak Hadir</th>
                            <th class="px-3 py-2.5 text-center text-amber-600 dark:text-amber-400">Terlambat</th>
                            <th class="px-3 py-2.5 text-center text-emerald-600 dark:text-emerald-400">Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <?php if (empty($mostAbsentCrews)): ?>
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-zinc-500 dark:text-zinc-400">Belum ada data kehadiran crew.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($mostAbsentCrews as $mac): ?>
                                <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition">
                                    <td class="px-3 py-2.5">
                                        <div class="font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($mac['name']) ?></div>
                                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400"><?= htmlspecialchars($mac['position']) ?></div>
                                    </td>
                                    <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">
                                        <?= htmlspecialchars($mac['rig_name'] ?: '-') ?>
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold text-rose-600 dark:text-rose-400 font-mono">
                                        <?= $mac['count_tidak_hadir'] ?>x
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold text-amber-600 dark:text-amber-400 font-mono">
                                        <?= $mac['count_terlambat'] ?>x
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold text-emerald-600 dark:text-emerald-400 font-mono">
                                        <?= $mac['count_hadir'] ?>x
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                    <i class="fa-solid fa-tower-observation text-sky-600 dark:text-sky-400"></i>
                    <span>Tingkat Kehadiran per Unit Rig</span>
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Perbandingan kepatuhan absensi meeting antar unit Rig</p>
            </div>

            <div class="space-y-3">
                <?php foreach ($rigRecaps as $rr): ?>
                    <?php
                    $total = $rr['total_meeting_records'] ?: 1;
                    $hadirPct = round(($rr['count_hadir'] / $total) * 100);
                    $latePct = round(($rr['count_terlambat'] / $total) * 100);
                    $absentPct = round(($rr['count_tidak_hadir'] / $total) * 100);
                    ?>
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-lg space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($rr['name']) ?></span>
                            <span class="text-xs font-bold text-sky-600 dark:text-sky-400 font-mono"><?= $hadirPct ?>% Hadir</span>
                        </div>
                        <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-2 rounded-full flex overflow-hidden">
                            <div class="bg-emerald-500 h-full" style="width: <?= $hadirPct ?>%"></div>
                            <div class="bg-amber-500 h-full" style="width: <?= $latePct ?>%"></div>
                            <div class="bg-rose-500 h-full" style="width: <?= $absentPct ?>%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 pt-1">
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold"><?= $rr['count_hadir'] ?> Hadir</span>
                            <span class="text-amber-600 dark:text-amber-400 font-semibold"><?= $rr['count_terlambat'] ?> Terlambat</span>
                            <span class="text-rose-600 dark:text-rose-400 font-semibold"><?= $rr['count_tidak_hadir'] ?> Absen</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xs overflow-hidden">
        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <i class="fa-solid fa-list text-sky-600 dark:text-sky-400"></i>
                <span>Tabel Rincian Kehadiran Keseluruhan (<?= count($records) ?> Baris Data)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                <thead class="bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400 uppercase font-semibold text-[11px] tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-3">Tanggal & Jam</th>
                        <th class="px-4 py-3">Meeting Rig</th>
                        <th class="px-4 py-3">Nama Crew</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3">Waktu Join</th>
                        <th class="px-4 py-3">Durasi</th>
                        <th class="px-4 py-3">Sumber Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data catatan kehadiran yang sesuai filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3 font-mono text-zinc-500 dark:text-zinc-400">
                                    <span class="text-zinc-900 dark:text-zinc-100 font-medium block"><?= date('d M Y', strtotime($rec['meeting_date'])) ?></span>
                                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500"><?= substr($rec['start_time'], 0, 5) ?> WIB</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100 truncate max-w-xs"><?= htmlspecialchars($rec['meeting_title']) ?></div>
                                    <div class="text-[10px] text-sky-600 dark:text-sky-400 font-medium"><?= htmlspecialchars($rec['rig_name']) ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($rec['crew_name']) ?></div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono"><?= htmlspecialchars($rec['nik']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                    <?= htmlspecialchars($rec['position']) ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($rec['status'] === 'HADIR'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">HADIR</span>
                                    <?php elseif ($rec['status'] === 'TERLAMBAT'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20">TERLAMBAT</span>
                                    <?php elseif ($rec['status'] === 'IZIN'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20">IZIN</span>
                                    <?php elseif ($rec['status'] === 'TIDAK_HADIR'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20">TIDAK HADIR</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">BELUM HADIR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-mono text-zinc-600 dark:text-zinc-300">
                                    <?= $rec['join_time'] ? date('H:i:s', strtotime($rec['join_time'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                    <?= $rec['duration_minutes'] > 0 ? $rec['duration_minutes'] . ' Menit' : '-' ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                        <?= htmlspecialchars($rec['source']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
