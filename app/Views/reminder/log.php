<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-2xl text-sky-400"></i>
                <h1 class="text-2xl font-black text-white tracking-tight">Log Outbox WhatsApp Reminder</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Riwayat seluruh pesan undangan dan pengingat bertingkat yang dikirim ke nomor personil</p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="triggerBackgroundReminders()" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs transition">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                <span>Jalankan Scan Reminder</span>
            </button>
            <a href="<?= base_url('reminder') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition">
                <i class="fa-brands fa-whatsapp"></i>
                <span>Kirim Broadcast Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg">
        <form method="GET" action="<?= base_url('reminder/log') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <select name="meeting_id" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">-- Semua Jadwal Meeting --</option>
                    <?php foreach ($meetings as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= ($selectedMeetingId == $m['id']) ? 'selected' : '' ?>>
                            [<?= esc($m['rig_code']) ?>] <?= esc($m['title']) ?> (<?= date('d/m/Y', strtotime($m['meeting_date'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($selectedMeetingId): ?>
                    <a href="<?= base_url('reminder/log') ?>" class="text-xs text-slate-400 hover:text-white flex items-center space-x-1">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset Filter</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Outbox Log Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[11px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Waktu Kirim</th>
                        <th class="px-5 py-3.5">Penerima Crew</th>
                        <th class="px-5 py-3.5">Meeting Terkait</th>
                        <th class="px-5 py-3.5 text-center">Tipe Reminder</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                Belum ada riwayat pengiriman pesan WhatsApp.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $l): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5 font-mono text-slate-400">
                                    <div class="text-slate-200"><?= date('d M Y', strtotime($l['sent_at'])) ?></div>
                                    <div class="text-[10px] text-slate-500"><?= date('H:i:s', strtotime($l['sent_at'])) ?> WIB</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-white"><?= esc($l['crew_name'] ?? 'Crew') ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono">+<?= esc($l['target_phone']) ?></div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-slate-200 truncate max-w-xs"><?= esc($l['meeting_title'] ?? '-') ?></div>
                                    <div class="text-[10px] text-sky-400"><?= esc($l['rig_name'] ?? '-') ?></div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <?php if ($l['reminder_type'] === 'UNDANGAN'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">UNDANGAN</span>
                                    <?php elseif ($l['reminder_type'] === 'H-1'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">H-1</span>
                                    <?php elseif ($l['reminder_type'] === 'H-1_JAM'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">1 JAM</span>
                                    <?php elseif ($l['reminder_type'] === 'H-15_MIN'): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">15 MENIT</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/20 text-purple-300">BELUM HADIR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-check-double mr-1 text-[9px]"></i> Terkirim
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <button type="button" onclick="openWhatsApp('<?= $l['target_phone'] ?>', '<?= esc($l['message_body'], 'js') ?>')" 
                                        class="px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 text-[11px] font-semibold border border-emerald-500/30 transition inline-flex items-center space-x-1" title="Buka / Kirim Ulang di WhatsApp">
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
<?= $this->endSection() ?>
