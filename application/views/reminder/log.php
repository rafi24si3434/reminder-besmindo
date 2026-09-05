<div class="space-y-6">

    <!-- Header & Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-2xl text-sky-600 dark:text-sky-400"></i>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Log Outbox WhatsApp Reminder</h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Riwayat seluruh pesan undangan dan pengingat bertingkat yang dikirim ke nomor personil</p>
        </div>

        <!-- Quick Navigation Tab Bar -->
        <div class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-xs">
            <a href="<?= base_url('reminder') ?>" class="px-3 py-1.5 rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-white dark:hover:bg-zinc-800 text-xs font-medium transition flex items-center space-x-1.5">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Broadcast</span>
            </a>
            <a href="<?= base_url('reminder/gateway') ?>" class="px-3 py-1.5 rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-white dark:hover:bg-zinc-800 text-xs font-medium transition flex items-center space-x-1.5">
                <i class="fa-solid fa-tower-broadcast"></i>
                <span>Status Gateway</span>
            </a>
            <a href="<?= base_url('reminder/log') ?>" class="px-3 py-1.5 rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-xs font-semibold shadow-xs transition flex items-center space-x-1.5">
                <i class="fa-solid fa-clock-rotate-left text-sky-600 dark:text-sky-400"></i>
                <span>Log Outbox</span>
            </a>
        </div>
    </div>

    <!-- Filter & Action Controls -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="<?= base_url('reminder/log') ?>" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <select name="meeting_id" onchange="this.form.submit()" class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                <option value="">-- Semua Jadwal Meeting --</option>
                <?php foreach ($meetings as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($selectedMeetingId == $m['id']) ? 'selected' : '' ?>>
                        [<?= htmlspecialchars($m['rig_code']) ?>] <?= htmlspecialchars($m['title']) ?> (<?= date('d/m/Y', strtotime($m['meeting_date'])) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($selectedMeetingId): ?>
                <a href="<?= base_url('reminder/log') ?>" class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 flex items-center space-x-1 font-medium transition">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset Filter</span>
                </a>
            <?php endif; ?>
        </form>

        <div class="flex items-center space-x-2">
            <button type="button" onclick="triggerBackgroundReminders()" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 font-semibold text-xs shadow-xs transition">
                <i class="fa-solid fa-bolt text-amber-500"></i>
                <span>Jalankan Scan Reminder</span>
            </button>
            <a href="<?= base_url('reminder') ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition">
                <i class="fa-brands fa-whatsapp"></i>
                <span>Kirim Broadcast Baru</span>
            </a>
        </div>
    </div>

    <!-- Table Outbox Card -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                <thead class="bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400 uppercase font-semibold text-[11px] tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-5 py-3.5">Waktu Kirim</th>
                        <th class="px-5 py-3.5">Penerima Crew</th>
                        <th class="px-5 py-3.5">Meeting Terkait</th>
                        <th class="px-5 py-3.5 text-center">Tipe Reminder</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                Belum ada riwayat pengiriman pesan WhatsApp.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $l): ?>
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-5 py-3.5 font-mono text-zinc-500 dark:text-zinc-400">
                                    <div class="text-zinc-900 dark:text-zinc-100 font-medium"><?= date('d M Y', strtotime($l['sent_at'])) ?></div>
                                    <div class="text-[10px] text-zinc-400 dark:text-zinc-500"><?= date('H:i:s', strtotime($l['sent_at'])) ?> WIB</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($l['crew_name'] ?: 'Crew') ?></div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono">+<?= htmlspecialchars($l['target_phone']) ?></div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate max-w-xs"><?= htmlspecialchars($l['meeting_title'] ?: '-') ?></div>
                                    <div class="text-[10px] text-sky-600 dark:text-sky-400 font-medium"><?= htmlspecialchars($l['rig_name'] ?: '-') ?></div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <?php if ($l['reminder_type'] === 'UNDANGAN'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-500/10 dark:bg-sky-500/20 text-sky-600 dark:text-sky-400 border border-sky-500/20">UNDANGAN</span>
                                    <?php elseif ($l['reminder_type'] === 'H-1'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">H-1</span>
                                    <?php elseif ($l['reminder_type'] === 'H-1_JAM'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20">1 JAM</span>
                                    <?php elseif ($l['reminder_type'] === 'H-15_MIN'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20">15 MENIT</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 border border-purple-500/20">BELUM HADIR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <i class="fa-solid fa-check-double mr-1 text-[9px]"></i> Terkirim
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <button type="button" onclick="openWhatsApp('<?= $l['target_phone'] ?>', '<?= addslashes($l['message_body']) ?>')" 
                                        class="px-2.5 py-1 rounded-md bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold border border-emerald-500/20 transition inline-flex items-center space-x-1" title="Buka / Kirim Ulang di WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <span>Buka WA</span>
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
