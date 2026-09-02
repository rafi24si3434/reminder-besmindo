<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-chart-pie text-2xl text-amber-400"></i>
                <h1 class="text-2xl font-black text-white tracking-tight">Rekap & Analitik Kehadiran Crew Rig</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Laporan rekapitulasi kehadiran, analisis kedisiplinan crew, dan performa meeting per rig</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= base_url('report/exportExcel?' . http_build_query($_GET)) ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition">
                <i class="fa-solid fa-file-excel"></i>
                <span>Download Excel (.CSV)</span>
            </a>
            <a href="<?= base_url('report/print?' . http_build_query($_GET)) ?>" target="_blank" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs transition">
                <i class="fa-solid fa-print text-sky-400"></i>
                <span>Cetak Laporan / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg">
        <form method="GET" action="<?= base_url('report') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div>
                    <select name="rig_id" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">-- Semua Unit Rig --</option>
                        <?php foreach ($rigs as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($selectedRig == $r['id']) ? 'selected' : '' ?>>
                                <?= esc($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center space-x-2 text-xs text-slate-400">
                    <span>Dari:</span>
                    <input type="date" name="start_date" value="<?= esc($startDate) ?>" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div class="flex items-center space-x-2 text-xs text-slate-400">
                    <span>Sampai:</span>
                    <input type="date" name="end_date" value="<?= esc($endDate) ?>" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <?php if ($selectedRig || $startDate || $endDate): ?>
                    <a href="<?= base_url('report') ?>" class="text-xs text-slate-400 hover:text-white flex items-center space-x-1">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset Filter</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Analytics Cards: Ranking Crew Sering Bolos/Terlambat & Rekap Rig -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Top Absent/Late Crew (7 Cols) - PRD Fase 5: Crew paling sering bolos -->
        <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center space-x-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                        <span>Personil Crew Paling Sering Terlambat / Absen</span>
                    </h3>
                    <p class="text-xs text-slate-400">Evaluasi kedisiplinan dan bahan tindak lanjut Manager Operasional</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-3 py-2.5">Personil Crew</th>
                            <th class="px-3 py-2.5">Rig</th>
                            <th class="px-3 py-2.5 text-center text-rose-400">Tidak Hadir</th>
                            <th class="px-3 py-2.5 text-center text-amber-400">Terlambat</th>
                            <th class="px-3 py-2.5 text-center text-emerald-400">Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <?php if (empty($mostAbsentCrews)): ?>
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-slate-500">Belum ada data kehadiran crew.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($mostAbsentCrews as $mac): ?>
                                <tr class="hover:bg-slate-800/40">
                                    <td class="px-3 py-2.5">
                                        <div class="font-bold text-white"><?= esc($mac['name']) ?></div>
                                        <div class="text-[10px] text-slate-400"><?= esc($mac['position']) ?></div>
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-300">
                                        <?= esc($mac['rig_name'] ?? '-') ?>
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold text-rose-400">
                                        <?= $mac['count_tidak_hadir'] ?>x
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold text-amber-400">
                                        <?= $mac['count_terlambat'] ?>x
                                    </td>
                                    <td class="px-3 py-2.5 text-center font-bold text-emerald-400">
                                        <?= $mac['count_hadir'] ?>x
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performa Kehadiran per Rig (5 Cols) -->
        <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center space-x-2">
                    <i class="fa-solid fa-tower-observation text-sky-400"></i>
                    <span>Tingkat Kehadiran per Unit Rig</span>
                </h3>
                <p class="text-xs text-slate-400">Perbandingan kepatuhan absensi meeting antar unit Rig</p>
            </div>

            <div class="space-y-3">
                <?php foreach ($rigRecaps as $rr): ?>
                    <?php
                    $total = $rr['total_meeting_records'] ?: 1;
                    $hadirPct = round(($rr['count_hadir'] / $total) * 100);
                    $latePct = round(($rr['count_terlambat'] / $total) * 100);
                    $absentPct = round(($rr['count_tidak_hadir'] / $total) * 100);
                    ?>
                    <div class="p-3.5 bg-slate-800/50 border border-slate-700/60 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-white"><?= esc($rr['name']) ?></span>
                            <span class="text-xs font-bold text-sky-400"><?= $hadirPct ?>% Hadir</span>
                        </div>
                        <div class="w-full bg-slate-950 h-2 rounded-full flex overflow-hidden">
                            <div class="bg-emerald-500 h-full" style="width: <?= $hadirPct ?>%"></div>
                            <div class="bg-amber-500 h-full" style="width: <?= $latePct ?>%"></div>
                            <div class="bg-rose-500 h-full" style="width: <?= $absentPct ?>%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                            <span class="text-emerald-400 font-semibold"><?= $rr['count_hadir'] ?> Hadir</span>
                            <span class="text-amber-400 font-semibold"><?= $rr['count_terlambat'] ?> Terlambat</span>
                            <span class="text-rose-400 font-semibold"><?= $rr['count_tidak_hadir'] ?> Absen</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Detail Riwayat Kehadiran Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center space-x-2">
                <i class="fa-solid fa-list text-sky-400"></i>
                <span>Tabel Rincian Kehadiran Keseluruhan (<?= count($records) ?> Baris Data)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[11px] tracking-wider border-b border-slate-800">
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
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                                Tidak ada data catatan kehadiran yang sesuai filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3 font-mono text-slate-400">
                                    <span class="text-white block"><?= date('d M Y', strtotime($rec['meeting_date'])) ?></span>
                                    <span class="text-[10px] text-slate-500"><?= substr($rec['start_time'], 0, 5) ?> WIB</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white truncate max-w-xs"><?= esc($rec['meeting_title']) ?></div>
                                    <div class="text-[10px] text-sky-400"><?= esc($rec['rig_name']) ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white"><?= esc($rec['crew_name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= esc($rec['nik']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    <?= esc($rec['position']) ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($rec['status'] === 'HADIR'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400">HADIR</span>
                                    <?php elseif ($rec['status'] === 'TERLAMBAT'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400">TERLAMBAT</span>
                                    <?php elseif ($rec['status'] === 'IZIN'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-400">IZIN</span>
                                    <?php elseif ($rec['status'] === 'TIDAK_HADIR'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-400">TIDAK HADIR</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-700 text-slate-300">BELUM HADIR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-300">
                                    <?= $rec['join_time'] ? date('H:i:s', strtotime($rec['join_time'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    <?= $rec['duration_minutes'] > 0 ? $rec['duration_minutes'] . ' Menit' : '-' ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-400">
                                        <?= esc($rec['source']) ?>
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
<?= $this->endSection() ?>
