<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="<?= base_url('meeting') ?>" class="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 mr-1 text-sm transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($meeting['title']) ?></h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Detail jadwal, penanggung jawab, dan daftar peserta crew rig</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= htmlspecialchars($meeting['teams_link']) ?>" target="_blank" class="px-3.5 py-2 rounded-lg bg-[#5059C9] hover:bg-[#434BA6] text-white font-semibold text-xs flex items-center space-x-2 shadow-xs transition">
                <i class="fa-brands fa-microsoft"></i>
                <span>Masuk Microsoft Teams</span>
            </a>

            <a href="<?= base_url('attendance/live/' . $meeting['id']) ?>" class="px-3.5 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-video"></i>
                <span>Live Monitor</span>
            </a>

            <a href="<?= base_url('reminder/broadcast/' . $meeting['id']) ?>" class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs flex items-center space-x-2 shadow-xs transition">
                <i class="fa-brands fa-whatsapp"></i>
                <span>Broadcast Undangan</span>
            </a>

            <a href="<?= base_url('meeting/edit/' . $meeting['id']) ?>" title="Edit Meeting" class="px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-xs border border-zinc-200 dark:border-zinc-800 shadow-xs transition">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-sky-500/10 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                    <i class="fa-solid fa-oil-well mr-1.5"></i> <?= htmlspecialchars($meeting['rig_name']) ?> (<?= htmlspecialchars($meeting['rig_code']) ?>)
                </span>

                <?php if ($meeting['is_recurring']): ?>
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                        <i class="fa-solid fa-repeat mr-1.5"></i> Rutin Setiap <?= htmlspecialchars($meeting['recurring_day']) ?>
                    </span>
                <?php endif; ?>

                <?php if ($meeting['status'] === 'in_progress'): ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping mr-2"></span> SEDANG BERLANGSUNG
                    </span>
                <?php elseif ($meeting['status'] === 'completed'): ?>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">SELESAI</span>
                <?php elseif ($meeting['status'] === 'cancelled'): ?>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20">DIBATALKAN</span>
                <?php else: ?>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20">TERJADWAL</span>
                <?php endif; ?>
            </div>

            <div class="space-y-1.5">
                <h2 class="text-xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($meeting['title']) ?></h2>
                <div class="text-xs text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-950/60 p-3.5 rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <strong class="text-zinc-500 dark:text-zinc-400 block text-[11px] mb-1 font-semibold uppercase tracking-wider">Topik / Agenda Rapat:</strong>
                    <?= nl2br(htmlspecialchars($meeting['topic'] ?: 'Tidak ada deskripsi topik.')) ?>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-xs">
                <div class="bg-zinc-50 dark:bg-zinc-950/60 p-3.5 rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-500 dark:text-zinc-400 block text-[11px] uppercase tracking-wider font-semibold">Tanggal:</span>
                    <strong class="text-zinc-900 dark:text-zinc-100 text-sm mt-0.5 block"><?= date('d M Y', strtotime($meeting['meeting_date'])) ?></strong>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-950/60 p-3.5 rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <span class="text-zinc-500 dark:text-zinc-400 block text-[11px] uppercase tracking-wider font-semibold">Waktu:</span>
                    <strong class="text-zinc-900 dark:text-zinc-100 text-sm mt-0.5 block"><?= substr($meeting['start_time'], 0, 5) ?> - <?= substr($meeting['end_time'], 0, 5) ?> WIB</strong>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-950/60 p-3.5 rounded-lg border border-zinc-200 dark:border-zinc-800 col-span-2 sm:col-span-1">
                    <span class="text-zinc-500 dark:text-zinc-400 block text-[11px] uppercase tracking-wider font-semibold">Lokasi Rig:</span>
                    <strong class="text-zinc-900 dark:text-zinc-100 text-xs truncate mt-0.5 block"><?= htmlspecialchars($meeting['rig_location']) ?></strong>
                </div>
            </div>

            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-lg flex items-center justify-between text-xs">
                <div class="truncate mr-2">
                    <span class="text-zinc-500 dark:text-zinc-400 text-[10px] block uppercase font-semibold">Link Microsoft Teams:</span>
                    <span class="text-sky-600 dark:text-sky-400 font-mono truncate block"><?= htmlspecialchars($meeting['teams_link']) ?></span>
                </div>
                <a href="<?= htmlspecialchars($meeting['teams_link']) ?>" target="_blank" class="px-3 py-1.5 rounded-md bg-[#5059C9]/10 hover:bg-[#5059C9]/20 text-[#5059C9] dark:text-indigo-400 font-semibold text-xs flex-shrink-0 transition">
                    Buka Teams &rarr;
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-100 dark:border-zinc-800 pb-3">
                Penanggung Jawab (PJ) Rig
            </h3>

            <div class="flex items-center space-x-3 p-3.5 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-lg">
                <div class="w-10 h-10 rounded-lg bg-sky-500/10 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($meeting['pj_name'] ?: 'Belum ditentukan') ?></h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400"><?= htmlspecialchars($meeting['pj_position'] ?: 'PJ Rig') ?></p>
                </div>
            </div>

            <?php if (!empty($meeting['pj_phone'])): ?>
                <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center justify-between">
                    <span>WhatsApp PJ:</span>
                    <a href="https://wa.me/<?= htmlspecialchars($meeting['pj_phone']) ?>" target="_blank" class="text-emerald-600 dark:text-emerald-400 font-mono hover:underline flex items-center space-x-1 font-medium">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>+<?= htmlspecialchars($meeting['pj_phone']) ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Ubah Status Pertemuan:</span>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="<?= base_url('meeting/update_status/' . $meeting['id'] . '/in_progress') ?>" class="px-3 py-2 rounded-lg text-center bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-semibold border border-emerald-500/20 transition">
                        Mulai Meeting
                    </a>
                    <a href="<?= base_url('meeting/update_status/' . $meeting['id'] . '/completed') ?>" class="px-3 py-2 rounded-lg text-center bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold border border-zinc-200 dark:border-zinc-700 transition">
                        Tandai Selesai
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-4">
            <div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                    <i class="fa-solid fa-clipboard-user text-sky-600 dark:text-sky-400"></i>
                    <span>Daftar Kehadiran Peserta Meeting (<?= count($attendances) ?> Crew)</span>
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Pantau kehadiran real-time dan hubungi crew yang belum hadir</p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="<?= base_url('attendance/live/' . $meeting['id']) ?>" class="px-3.5 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold flex items-center space-x-1.5 shadow-xs transition">
                    <i class="fa-solid fa-video"></i>
                    <span>Buka Live Monitor</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                <thead class="bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400 uppercase font-semibold text-[11px] tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-3">Crew</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3 text-center">Status Kehadiran</th>
                        <th class="px-4 py-3">Waktu Join</th>
                        <th class="px-4 py-3">Durasi</th>
                        <th class="px-4 py-3 text-right">Tindakan WA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    <?php if (empty($attendances)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Belum ada peserta crew yang terdaftar di meeting ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendances as $att): ?>
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($att['crew_name']) ?></div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono"><?= htmlspecialchars($att['nik']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                    <?= htmlspecialchars($att['position']) ?>
                                </td>
                                <td class="px-4 py-3 text-center">
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
                                            <i class="fa-solid fa-file-signature mr-1"></i> IZIN / SAKIT
                                        </span>
                                    <?php elseif ($att['status'] === 'TIDAK_HADIR'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            <i class="fa-solid fa-xmark mr-1"></i> TIDAK HADIR
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                            <i class="fa-solid fa-hourglass mr-1"></i> BELUM HADIR
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300 font-mono">
                                    <?= $att['join_time'] ? date('H:i:s', strtotime($att['join_time'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                    <?= $att['duration_minutes'] > 0 ? $att['duration_minutes'] . ' Menit' : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <?php
                                    $msg = "Halo " . $att['crew_name'] . ", Anda diundang ke meeting: " . $meeting['title'] . " pada " . date('d M Y', strtotime($meeting['meeting_date'])) . " " . substr($meeting['start_time'], 0, 5) . " WIB. Link Teams: " . $meeting['teams_link'];
                                    ?>
                                    <button type="button" onclick="openWhatsApp('<?= $att['phone'] ?>', '<?= addslashes($msg) ?>')" 
                                        class="px-2.5 py-1 rounded-md bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold border border-emerald-500/20 transition inline-flex items-center space-x-1">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <span>Kirim WA</span>
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
