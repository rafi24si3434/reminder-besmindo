<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="<?= base_url('meeting') ?>" class="text-slate-400 hover:text-white mr-1 text-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-black text-white tracking-tight"><?= htmlspecialchars($meeting['title']) ?></h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Detail jadwal, penanggung jawab, dan daftar peserta crew rig</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= htmlspecialchars($meeting['teams_link']) ?>" target="_blank" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs flex items-center space-x-2 shadow-lg shadow-indigo-600/30 transition">
                <i class="fa-brands fa-microsoft"></i>
                <span>Masuk Microsoft Teams</span>
            </a>

            <a href="<?= base_url('attendance/live/' . $meeting['id']) ?>" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs flex items-center space-x-2 shadow-lg shadow-sky-600/30 transition">
                <i class="fa-solid fa-video"></i>
                <span>Live Attendance Monitor</span>
            </a>

            <a href="<?= base_url('reminder/broadcast/' . $meeting['id']) ?>" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs flex items-center space-x-2 shadow-lg shadow-emerald-600/30 transition">
                <i class="fa-brands fa-whatsapp"></i>
                <span>Broadcast Undangan</span>
            </a>

            <a href="<?= base_url('meeting/edit/' . $meeting['id']) ?>" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-md text-xs font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                    <i class="fa-solid fa-oil-well mr-1.5"></i> <?= htmlspecialchars($meeting['rig_name']) ?> (<?= htmlspecialchars($meeting['rig_code']) ?>)
                </span>

                <?php if ($meeting['is_recurring']): ?>
                    <span class="px-3 py-1 rounded-md text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        <i class="fa-solid fa-repeat mr-1.5"></i> Rutin Setiap <?= htmlspecialchars($meeting['recurring_day']) ?>
                    </span>
                <?php endif; ?>

                <?php if ($meeting['status'] === 'in_progress'): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping mr-2"></span> SEDANG BERLANGSUNG
                    </span>
                <?php elseif ($meeting['status'] === 'completed'): ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-400">SELESAI</span>
                <?php elseif ($meeting['status'] === 'cancelled'): ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400">DIBATALKAN</span>
                <?php else: ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">TERJADWAL</span>
                <?php endif; ?>
            </div>

            <div class="space-y-1">
                <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($meeting['title']) ?></h2>
                <p class="text-xs text-slate-300 bg-slate-800/60 p-3 rounded-xl border border-slate-700/60">
                    <strong class="text-slate-400 block mb-0.5">Topik / Agenda Rapat:</strong>
                    <?= nl2br(htmlspecialchars($meeting['topic'] ?: 'Tidak ada deskripsi topik.')) ?>
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-xs">
                <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700/40">
                    <span class="text-slate-400 block text-[11px]">Tanggal:</span>
                    <strong class="text-white text-sm"><?= date('d M Y', strtotime($meeting['meeting_date'])) ?></strong>
                </div>
                <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700/40">
                    <span class="text-slate-400 block text-[11px]">Waktu:</span>
                    <strong class="text-white text-sm"><?= substr($meeting['start_time'], 0, 5) ?> - <?= substr($meeting['end_time'], 0, 5) ?> WIB</strong>
                </div>
                <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700/40 col-span-2 sm:col-span-1">
                    <span class="text-slate-400 block text-[11px]">Lokasi Rig:</span>
                    <strong class="text-white text-xs truncate block"><?= htmlspecialchars($meeting['rig_location']) ?></strong>
                </div>
            </div>

            <div class="p-3 bg-slate-800/30 border border-slate-700/40 rounded-xl flex items-center justify-between text-xs">
                <div class="truncate mr-2">
                    <span class="text-slate-500 text-[10px] block">Link Microsoft Teams:</span>
                    <span class="text-sky-400 font-mono truncate block"><?= htmlspecialchars($meeting['teams_link']) ?></span>
                </div>
                <a href="<?= htmlspecialchars($meeting['teams_link']) ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 font-semibold text-xs flex-shrink-0">
                    Buka Teams &rarr;
                </a>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800 pb-3">
                Penanggung Jawab (PJ) Rig
            </h3>

            <div class="flex items-center space-x-3 p-3.5 bg-slate-800/50 border border-slate-700/50 rounded-xl">
                <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white"><?= htmlspecialchars($meeting['pj_name'] ?: 'Belum ditentukan') ?></h4>
                    <p class="text-[11px] text-slate-400"><?= htmlspecialchars($meeting['pj_position'] ?: 'PJ Rig') ?></p>
                </div>
            </div>

            <?php if (!empty($meeting['pj_phone'])): ?>
                <div class="text-xs text-slate-400 flex items-center justify-between">
                    <span>WhatsApp PJ:</span>
                    <a href="https://wa.me/<?= htmlspecialchars($meeting['pj_phone']) ?>" target="_blank" class="text-emerald-400 font-mono hover:underline flex items-center space-x-1">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>+<?= htmlspecialchars($meeting['pj_phone']) ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <div class="pt-4 border-t border-slate-800 space-y-2">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Ubah Status Pertemuan:</span>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="<?= base_url('meeting/update_status/' . $meeting['id'] . '/in_progress') ?>" class="px-3 py-1.5 rounded-lg text-center bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 font-semibold border border-emerald-500/30 transition">
                        Mulai Meeting
                    </a>
                    <a href="<?= base_url('meeting/update_status/' . $meeting['id'] . '/completed') ?>" class="px-3 py-1.5 rounded-lg text-center bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold border border-slate-700 transition">
                        Tandai Selesai
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-base font-bold text-white flex items-center space-x-2">
                    <i class="fa-solid fa-clipboard-user text-sky-400"></i>
                    <span>Daftar Kehadiran Peserta Meeting (<?= count($attendances) ?> Crew)</span>
                </h3>
                <p class="text-xs text-slate-400">Pantau kehadiran real-time dan hubungi crew yang belum hadir</p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="<?= base_url('attendance/live/' . $meeting['id']) ?>" class="px-3.5 py-1.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold flex items-center space-x-1.5 shadow transition">
                    <i class="fa-solid fa-video"></i>
                    <span>Buka Live Monitor</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[11px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Crew</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3 text-center">Status Kehadiran</th>
                        <th class="px-4 py-3">Waktu Join</th>
                        <th class="px-4 py-3">Durasi</th>
                        <th class="px-4 py-3 text-right">Tindakan WA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($attendances)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                Belum ada peserta crew yang terdaftar di meeting ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendances as $att): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white"><?= htmlspecialchars($att['crew_name']) ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($att['nik']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    <?= htmlspecialchars($att['position']) ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($att['status'] === 'HADIR'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            <i class="fa-solid fa-check mr-1"></i> HADIR (Tepat Waktu)
                                        </span>
                                    <?php elseif ($att['status'] === 'TERLAMBAT'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                            <i class="fa-solid fa-clock mr-1"></i> TERLAMBAT
                                        </span>
                                    <?php elseif ($att['status'] === 'IZIN'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                            <i class="fa-solid fa-file-signature mr-1"></i> IZIN / SAKIT
                                        </span>
                                    <?php elseif ($att['status'] === 'TIDAK_HADIR'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                            <i class="fa-solid fa-xmark mr-1"></i> TIDAK HADIR
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">
                                            <i class="fa-solid fa-hourglass mr-1"></i> BELUM HADIR
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-300 font-mono">
                                    <?= $att['join_time'] ? date('H:i:s', strtotime($att['join_time'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    <?= $att['duration_minutes'] > 0 ? $att['duration_minutes'] . ' Menit' : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <?php
                                    $msg = "Halo " . $att['crew_name'] . ", Anda diundang ke meeting: " . $meeting['title'] . " pada " . date('d M Y', strtotime($meeting['meeting_date'])) . " " . substr($meeting['start_time'], 0, 5) . " WIB. Link Teams: " . $meeting['teams_link'];
                                    ?>
                                    <button type="button" onclick="openWhatsApp('<?= $att['phone'] ?>', '<?= addslashes($msg) ?>')" 
                                        class="px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 text-[11px] font-semibold border border-emerald-500/30 transition inline-flex items-center space-x-1">
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
