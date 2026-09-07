<div class="space-y-6">

    <!-- Top Action & Notification Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <span class="p-2.5 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                    <i class="fa-solid fa-file-signature text-xl"></i>
                </span>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Rekap Absensi & Hasil Rapat</h1>
                        <?php if ($meeting['status'] === 'completed'): ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center space-x-1">
                                <i class="fa-solid fa-lock text-[9px]"></i>
                                <span>SESI RESMI DITUTUP</span>
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 flex items-center space-x-1 animate-pulse">
                                <i class="fa-solid fa-circle-dot text-[9px]"></i>
                                <span>SESI MASIH BERJALAN</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Laporan resmi rekapitulasi kehadiran personil crew dan notulensi rapat PT. Besmindo Materi Sewatama
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Modern & Professional Meeting Switcher -->
            <?php $this->load->view('components/meeting_switcher', array(
                'meetings'        => $meetings,
                'current_meeting' => $meeting,
                'target_route'    => 'attendance/rekap/'
            )); ?>

            <!-- Cetak Rekap Button -->
            <a href="<?= base_url('attendance/print_rekap/' . $meeting['id']) ?>" target="_blank" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-print text-sky-600 dark:text-sky-400"></i>
                <span>Cetak / PDF</span>
            </a>

            <!-- WA Group Broadcast Button -->
            <button type="button" onclick="openRecapWaModal()" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition">
                <i class="fa-brands fa-whatsapp text-xs"></i>
                <span>Kirim ke WA Group</span>
            </button>

            <?php if ($meeting['status'] === 'completed'): ?>
                <!-- Reopen Session Button -->
                <button type="button" onclick="confirmReopenSession(<?= $meeting['id'] ?>)" class="inline-flex items-center space-x-2 px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 font-semibold text-xs shadow-xs transition" title="Buka kembali sesi jika ingin menambah/mengoreksi absensi">
                    <i class="fa-solid fa-unlock-keyhole text-amber-500"></i>
                    <span>Buka Sesi Kembali</span>
                </button>
            <?php else: ?>
                <!-- Tutup Sesi Modal Trigger -->
                <button type="button" onclick="openCloseSessionModal()" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs shadow-xs transition">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Tutup Sesi Sekarang</span>
                </button>
            <?php endif; ?>

            <a href="<?= base_url('attendance/live/' . $meeting['id']) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-tv text-sky-600 dark:text-sky-400"></i>
                <span>Monitor Live</span>
            </a>
        </div>
    </div>

    <!-- Status Banner if Closed -->
    <?php if ($meeting['status'] === 'completed'): ?>
        <div class="bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-500/30 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                    <i class="fa-solid fa-check-double text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Sesi Rapat Resmi Telah Diselesaikan & Ditutup</h3>
                        <span class="text-[11px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 font-mono font-medium">
                            <?= !empty($meeting['closed_at']) ? date('d M Y, H:i', strtotime($meeting['closed_at'])) . ' WIB' : 'Ditutup' ?>
                        </span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                        Seluruh personil yang belum hadir otomatis tercatat sebagai <strong>TIDAK HADIR (Alpha)</strong>. Reminder WhatsApp terjadwal otomatis dimatikan.
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2 self-end sm:self-center">
                <button type="button" onclick="confirmReopenSession(<?= $meeting['id'] ?>)" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 underline transition">
                    Salah menutup? Klik di sini untuk buka kembali
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Meeting Details Card -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-xs grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 space-y-2">
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-sky-500/10 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                    <i class="fa-solid fa-oil-well mr-1"></i> <?= htmlspecialchars($meeting['rig_name']) ?> (<?= htmlspecialchars($meeting['rig_code']) ?>)
                </span>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    <i class="fa-regular fa-calendar mr-1"></i> <?= date('d M Y', strtotime($meeting['meeting_date'])) ?>
                </span>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    <i class="fa-regular fa-clock mr-1"></i> <?= substr($meeting['start_time'], 0, 5) ?> - <?= substr($meeting['end_time'], 0, 5) ?> WIB
                </span>
            </div>
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($meeting['title']) ?></h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400"><?= htmlspecialchars($meeting['topic']) ?></p>
        </div>

        <div class="space-y-1.5 border-t md:border-t-0 md:border-l border-zinc-100 dark:border-zinc-800 md:pl-4">
            <span class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Penanggung Jawab / Host</span>
            <div class="flex items-center space-x-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                <i class="fa-solid fa-user-tie text-sky-600 dark:text-sky-400"></i>
                <span><?= htmlspecialchars(!empty($meeting['pj_name']) ? $meeting['pj_name'] : 'Management Besmindo') ?></span>
            </div>
            <div class="text-[11px] text-zinc-500 dark:text-zinc-400 flex items-center space-x-1">
                <i class="fa-brands fa-whatsapp text-emerald-600 dark:text-emerald-400"></i>
                <span>WA Group: <?= !empty($meeting['wa_group_id']) ? htmlspecialchars($meeting['wa_group_id']) : '<em class="text-zinc-400">Belum diisi</em>' ?></span>
            </div>
        </div>

        <div class="space-y-2 border-t md:border-t-0 md:border-l border-zinc-100 dark:border-zinc-800 md:pl-4 flex flex-col justify-center">
            <span class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Tautan Microsoft Teams</span>
            <a href="<?= htmlspecialchars($meeting['teams_link']) ?>" target="_blank" class="px-3.5 py-2 rounded-lg bg-[#5059C9]/10 hover:bg-[#5059C9]/20 text-[#5059C9] dark:text-indigo-400 border border-[#5059C9]/20 font-semibold text-xs flex items-center justify-between transition">
                <span class="truncate mr-2"><i class="fa-brands fa-microsoft mr-1.5"></i> Buka Teams</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        </div>
    </div>

    <!-- 5 KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs text-center">
            <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Total Undangan</span>
            <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 block mt-1"><?= $stats['total'] ?></span>
            <span class="text-[10px] text-zinc-400 dark:text-zinc-500">Crew Personil</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-emerald-500/30 rounded-xl p-4 shadow-xs text-center">
            <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Total Hadir</span>
            <span class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 block mt-1"><?= $stats['hadir'] ?></span>
            <span class="text-[10px] text-emerald-600/70 dark:text-emerald-400/80">Personil</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-sky-500/30 rounded-xl p-4 shadow-xs text-center">
            <span class="text-[11px] font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider block">Izin / Sakit</span>
            <span class="text-2xl font-bold tracking-tight text-sky-600 dark:text-sky-400 block mt-1"><?= $stats['izin'] ?></span>
            <span class="text-[10px] text-sky-600/70 dark:text-sky-400/80">Dispensasi / Off</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-rose-500/30 rounded-xl p-4 shadow-xs text-center">
            <span class="text-[11px] font-medium text-rose-600 dark:text-rose-400 uppercase tracking-wider block">Tidak Hadir (Alpha)</span>
            <span class="text-2xl font-bold tracking-tight text-rose-600 dark:text-rose-400 block mt-1"><?= $stats['tidak_hadir'] ?></span>
            <span class="text-[10px] text-rose-600/70 dark:text-rose-400/80">Mangkir</span>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs text-center col-span-2 sm:col-span-1">
            <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Belum Hadir</span>
            <span class="text-2xl font-bold tracking-tight text-zinc-700 dark:text-zinc-300 block mt-1"><?= $stats['belum_hadir'] ?></span>
            <span class="text-[10px] text-zinc-400 dark:text-zinc-500">Pending</span>
        </div>
    </div>

    <!-- Attendance Percentage Bar -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-xs space-y-2">
        <div class="flex items-center justify-between text-xs">
            <span class="font-semibold text-zinc-700 dark:text-zinc-300 flex items-center space-x-1.5">
                <i class="fa-solid fa-chart-pie text-indigo-600 dark:text-indigo-400"></i>
                <span>Tingkat Kehadiran Crew Rig:</span>
            </span>
            <span class="font-bold text-base <?= ($stats['percentage'] >= 80) ? 'text-emerald-600 dark:text-emerald-400' : (($stats['percentage'] >= 50) ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') ?>">
                <?= $stats['percentage'] ?>% (<?= $stats['hadir'] ?> dari <?= $stats['total'] ?> Hadir)
            </span>
        </div>
        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2.5 overflow-hidden flex">
            <?php 
                $pctHadir = ($stats['total'] > 0) ? ($stats['hadir'] / $stats['total']) * 100 : 0;
                $pctIzin  = ($stats['total'] > 0) ? ($stats['izin'] / $stats['total']) * 100 : 0;
                $pctAlpha = ($stats['total'] > 0) ? ($stats['tidak_hadir'] / $stats['total']) * 100 : 0;
            ?>
            <div style="width: <?= $pctHadir ?>%" class="bg-emerald-500 h-full rounded-l" title="Hadir: <?= $stats['hadir'] ?>"></div>
            <div style="width: <?= $pctIzin ?>%" class="bg-sky-500 h-full" title="Izin: <?= $stats['izin'] ?>"></div>
            <div style="width: <?= $pctAlpha ?>%" class="bg-rose-500 h-full rounded-r" title="Tidak Hadir: <?= $stats['tidak_hadir'] ?>"></div>
        </div>
        <div class="flex flex-wrap items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400 pt-1">
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center space-x-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> <span>Hadir</span></span>
                <span class="inline-flex items-center space-x-1"><span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block"></span> <span>Izin</span></span>
                <span class="inline-flex items-center space-x-1"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span> <span>Tidak Hadir (Alpha)</span></span>
            </div>
            <span class="text-zinc-400 dark:text-zinc-500 font-mono">Standar KPI Rig: &ge; 85%</span>
        </div>
    </div>

    <!-- Notulensi & Kesimpulan Rapat -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <div class="flex items-center space-x-2">
                <span class="p-1.5 rounded-md bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                    <i class="fa-solid fa-clipboard-list"></i>
                </span>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Notulensi, Hasil Rapat & Tindak Lanjut (Action Items)</h3>
            </div>
            <button type="button" onclick="openNotesModal()" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-pen-to-square text-sky-600 dark:text-sky-400"></i>
                <span><?= empty($meeting['meeting_notes']) ? 'Tambah Notulensi' : 'Edit Notulensi' ?></span>
            </button>
        </div>

        <?php if (!empty($meeting['meeting_notes'])): ?>
            <div class="bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800/80 rounded-lg p-4 text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed whitespace-pre-line font-mono">
                <?= htmlspecialchars($meeting['meeting_notes']) ?>
            </div>
        <?php else: ?>
            <div class="bg-zinc-50/50 dark:bg-zinc-950/40 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg p-6 text-center">
                <i class="fa-solid fa-notes-medical text-3xl text-zinc-400 dark:text-zinc-600 mb-2"></i>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Belum ada catatan atau notulensi hasil rapat yang dimasukkan.</p>
                <button type="button" onclick="openNotesModal()" class="mt-2 text-xs font-semibold text-sky-600 dark:text-sky-400 hover:underline">
                    + Tulis Notulensi Sekarang
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Roster Table Presensi Lengkap -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xs overflow-hidden space-y-3 p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-4">
            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                    <i class="fa-solid fa-users text-sky-600 dark:text-sky-400"></i>
                    <span>Daftar Presensi Personil Crew (<?= count($attendances) ?> Orang)</span>
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Rincian status kehadiran per personil, jam kedatangan, dan durasi bergabung</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap items-center gap-1.5 text-xs">
                <button type="button" onclick="filterTable('all')" class="filter-btn px-3 py-1 rounded-md bg-zinc-900 dark:bg-zinc-50 text-white dark:text-zinc-900 font-semibold transition" data-filter="all">Semua</button>
                <button type="button" onclick="filterTable('HADIR')" class="filter-btn px-3 py-1 rounded-md bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 font-medium border border-zinc-200 dark:border-zinc-700 transition" data-filter="HADIR">Hadir</button>
                <button type="button" onclick="filterTable('IZIN')" class="filter-btn px-3 py-1 rounded-md bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 font-medium border border-zinc-200 dark:border-zinc-700 transition" data-filter="IZIN">Izin</button>
                <button type="button" onclick="filterTable('TIDAK_HADIR')" class="filter-btn px-3 py-1 rounded-md bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 font-medium border border-zinc-200 dark:border-zinc-700 transition" data-filter="TIDAK_HADIR">Tidak Hadir</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300" id="recapTable">
                <thead class="text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-950/60 border-b border-zinc-200 dark:border-zinc-800 font-semibold">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Personil</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">No. WhatsApp</th>
                        <th class="px-4 py-3 text-center">Status Kehadiran</th>
                        <th class="px-4 py-3 text-center">Waktu Masuk</th>
                        <th class="px-4 py-3 text-center">Durasi</th>
                        <th class="px-4 py-3">Catatan / Keterangan</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    <?php if (empty($attendances)): ?>
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data personil crew yang ditugaskan di rig ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($attendances as $att): ?>
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition attendance-row" data-status="<?= $att['status'] ?>">
                                <td class="px-4 py-3 font-mono text-zinc-400"><?= $no++ ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($att['crew_name']) ?></div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono">ID #<?= $att['crew_id'] ?></div>
                                </td>
                                <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">
                                    <?= htmlspecialchars($att['position']) ?>
                                </td>
                                <td class="px-4 py-3 font-mono text-zinc-500 dark:text-zinc-400">
                                    <?= htmlspecialchars($att['phone']) ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($att['status'] === 'HADIR'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            <i class="fa-solid fa-circle-check mr-1"></i> HADIR
                                        </span>
                                    <?php elseif ($att['status'] === 'IZIN'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-sky-500/10 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                            <i class="fa-solid fa-envelope-open-text mr-1"></i> IZIN
                                        </span>
                                    <?php elseif ($att['status'] === 'TIDAK_HADIR'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            <i class="fa-solid fa-circle-xmark mr-1"></i> TIDAK HADIR
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                            <i class="fa-solid fa-hourglass mr-1"></i> BELUM HADIR
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-zinc-500 dark:text-zinc-400">
                                    <?= !empty($att['join_time']) ? date('H:i:s', strtotime($att['join_time'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-zinc-500 dark:text-zinc-400">
                                    <?= !empty($att['duration_minutes']) ? $att['duration_minutes'] . ' Menit' : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 max-w-xs truncate">
                                    <?= !empty($att['notes']) ? htmlspecialchars($att['notes']) : '<span class="text-zinc-400 dark:text-zinc-600 italic">-</span>' ?>
                                    <?php if (!empty($att['source'])): ?>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 font-mono ml-1"><?= $att['source'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="openManualEdit(<?= $att['id'] ?>, '<?= htmlspecialchars(addslashes($att['crew_name'])) ?>', '<?= $att['status'] ?>', '<?= htmlspecialchars(addslashes($att['notes'] ?? '')) ?>')" class="p-1.5 rounded-md bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 transition" title="Koreksi manual status personil ini">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Notulensi Rapat -->
<div id="notesModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-xl w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <i class="fa-solid fa-clipboard-check text-sky-600 dark:text-sky-400"></i>
                <span>Edit Notulensi & Hasil Rapat</span>
            </h3>
            <button type="button" onclick="closeNotesModal()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('attendance/update_notes') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="meeting_id" value="<?= $meeting['id'] ?>">

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                    Notulensi / Kesimpulan / Poin Keputusan:
                </label>
                <textarea name="meeting_notes" rows="8" placeholder="Tuliskan ringkasan rapat, action items, target perbaikan rig, penugasan personil, dsb..." class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none font-sans leading-relaxed transition"><?= htmlspecialchars($meeting['meeting_notes'] ?? '') ?></textarea>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">Notulensi ini akan dicantumkan di lembar cetak laporan resmi dan pesan WhatsApp recap group.</p>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeNotesModal()" class="px-4 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-zinc-900 dark:bg-zinc-50 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-semibold shadow-xs transition">
                    Simpan Notulensi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Broadcast WA Recap ke WhatsApp Group -->
<div id="recapWaModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <i class="fa-brands fa-whatsapp text-emerald-600 dark:text-emerald-400"></i>
                <span>Kirim Laporan Rekap ke WhatsApp Group</span>
            </h3>
            <button type="button" onclick="closeRecapWaModal()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">
                    WhatsApp Group ID Rig <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="targetWaGroupInput" value="<?= htmlspecialchars($meeting['wa_group_id'] ?? '') ?>" placeholder="Contoh: 120363024823901@g.us" class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:outline-none font-mono transition">
                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1 block">Otomatis diambil dari data Unit Rig (<?= htmlspecialchars($meeting['rig_name']) ?>).</span>
            </div>

            <div>
                <span class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">
                    Preview Format Pesan WhatsApp:
                </span>
                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 text-[11px] text-zinc-700 dark:text-zinc-300 font-mono space-y-1 max-h-48 overflow-y-auto">
                    <p class="text-emerald-600 dark:text-emerald-400 font-bold">*[LAPORAN REKAPITULASI & ABSENSI RAPAT]*</p>
                    <p>🏢 *Unit Rig:* <?= htmlspecialchars($meeting['rig_name']) ?></p>
                    <p>📋 *Agenda:* <?= htmlspecialchars($meeting['title']) ?></p>
                    <p>📅 *Tanggal:* <?= date('d M Y', strtotime($meeting['meeting_date'])) ?></p>
                    <p>🔒 *Status Sesi:* RESMI DITUTUP (Completed)</p>
                    <p class="pt-1">📊 *Statistik Kehadiran:*</p>
                    <p>✅ Hadir: <?= $stats['hadir'] ?> | ℹ️ Izin: <?= $stats['izin'] ?> | ❌ Alpha: <?= $stats['tidak_hadir'] ?></p>
                    <p>📈 *Tingkat Kehadiran: <?= $stats['percentage'] ?>%*</p>
                    <p class="text-zinc-400 dark:text-zinc-500 pt-1 italic">...daftar personil & notulensi lengkap disertakan...</p>
                </div>
            </div>

            <div id="waSendFeedback" class="hidden p-3 rounded-lg text-xs"></div>
        </div>

        <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end space-x-2">
            <button type="button" onclick="closeRecapWaModal()" class="px-4 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition">
                Tutup
            </button>
            <button type="button" id="btnSubmitWaRecap" onclick="executeSendRecapWa()" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-xs transition flex items-center space-x-2">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span id="btnSubmitWaRecapText">Kirim Pesan Sekarang</span>
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
                <span>Selesaikan & Tutup Sesi Rapat Ini</span>
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
                    <li>Anda tetap dapat mencetak laporan dan mengirim rekap ke grup WhatsApp.</li>
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
                    Tutup & Rekap Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Koreksi Kehadiran Manual -->
<div id="manualEditModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <i class="fa-solid fa-pen-to-square text-sky-600 dark:text-sky-400"></i>
                <span>Koreksi Presensi Personil</span>
            </h3>
            <button type="button" onclick="closeManualEdit()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('attendance/update_status_manual') ?>" method="POST" class="space-y-4">
            <input type="hidden" id="editAttendanceId" name="attendance_id">

            <div>
                <span class="text-xs text-zinc-500 dark:text-zinc-400 block">Personil:</span>
                <strong id="editCrewName" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"></strong>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">Status Kehadiran <span class="text-rose-500">*</span></label>
                <select name="status" id="editStatus" required class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                    <option value="HADIR">HADIR</option>
                    <option value="IZIN">IZIN / SAKIT / DISPENSASI</option>
                    <option value="TIDAK_HADIR">TIDAK HADIR (Alpha)</option>
                    <option value="BELUM_HADIR">BELUM HADIR</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">Catatan / Keterangan</label>
                <textarea name="notes" id="editNotes" rows="3" placeholder="Alasan izin atau koreksi status..." class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition"></textarea>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeManualEdit()" class="px-4 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-zinc-900 dark:bg-zinc-50 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-semibold shadow-xs transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Notes Modal
    function openNotesModal() {
        const m = document.getElementById('notesModal');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
    function closeNotesModal() {
        const m = document.getElementById('notesModal');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    // WA Recap Modal
    function openRecapWaModal() {
        const m = document.getElementById('recapWaModal');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
    function closeRecapWaModal() {
        const m = document.getElementById('recapWaModal');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    // Close Session Modal
    function openCloseSessionModal() {
        const m = document.getElementById('closeSessionModal');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
    function closeCloseSessionModal() {
        const m = document.getElementById('closeSessionModal');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    // Manual Edit Modal
    function openManualEdit(id, name, status, notes) {
        document.getElementById('editAttendanceId').value = id;
        document.getElementById('editCrewName').innerText = name;
        document.getElementById('editStatus').value = status;
        document.getElementById('editNotes').value = notes;

        const m = document.getElementById('manualEditModal');
        m.classList.remove('hidden');
        m.classList.add('flex');
    }
    function closeManualEdit() {
        const m = document.getElementById('manualEditModal');
        m.classList.add('hidden');
        m.classList.remove('flex');
    }

    // Reopen Session Confirmation
    function confirmReopenSession(meetingId) {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: 'Buka Kembali Sesi Rapat?',
            text: 'Status meeting akan dikembalikan menjadi AKTIF (In Progress). Anda dapat memperbarui presensi crew kembali.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#71717a',
            confirmButtonText: 'Ya, Buka Sesi',
            cancelButtonText: 'Batal',
            background: isDark ? '#18181b' : '#ffffff',
            color: isDark ? '#f4f4f5' : '#18181b'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('attendance/reopen_session/') ?>/' + meetingId;
            }
        });
    }

    // Send Recap via AJAX
    function executeSendRecapWa() {
        const isDark = document.documentElement.classList.contains('dark');
        const group = document.getElementById('targetWaGroupInput').value.trim();
        const feedback = document.getElementById('waSendFeedback');
        const btn = document.getElementById('btnSubmitWaRecap');
        const btnText = document.getElementById('btnSubmitWaRecapText');

        if (!group) {
            Swal.fire({
                icon: 'warning',
                title: 'WhatsApp Group Kosong',
                text: 'Silakan isi target WhatsApp Group ID Rig terlebih dahulu.',
                background: isDark ? '#18181b' : '#ffffff',
                color: isDark ? '#f4f4f5' : '#18181b'
            });
            return;
        }

        btn.disabled = true;
        btnText.innerText = 'Mengirim Pesan...';
        feedback.classList.add('hidden');

        fetch('<?= base_url('attendance/ajax_send_recap_wa') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `meeting_id=<?= $meeting['id'] ?>&target_group=${encodeURIComponent(group)}`
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btnText.innerText = 'Kirim Pesan Sekarang';
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Laporan Berhasil Terkirim!',
                    text: 'Ringkasan absensi dan notulensi rapat telah dikirim ke WhatsApp Group Rig.',
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b'
                }).then(() => {
                    closeRecapWaModal();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Pengiriman Gagal',
                    text: res.message || 'Terjadi kesalahan saat menghubungkan ke gateway WhatsApp.',
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b'
                });
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.innerText = 'Kirim Pesan Sekarang';
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Sistem',
                text: err.toString(),
                background: isDark ? '#18181b' : '#ffffff',
                color: isDark ? '#f4f4f5' : '#18181b'
            });
        });
    }

    // Filter Table
    function filterTable(status) {
        document.querySelectorAll('.filter-btn').forEach(b => {
            if (b.dataset.filter === status) {
                b.className = 'filter-btn px-3 py-1 rounded-md bg-zinc-900 dark:bg-zinc-50 text-white dark:text-zinc-900 font-semibold transition';
            } else {
                b.className = 'filter-btn px-3 py-1 rounded-md bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 font-medium border border-zinc-200 dark:border-zinc-700 transition';
            }
        });

        const rows = document.querySelectorAll('.attendance-row');
        rows.forEach(r => {
            if (status === 'all' || r.dataset.status === status) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }
</script>
