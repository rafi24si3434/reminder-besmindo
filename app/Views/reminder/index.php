<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-brands fa-whatsapp text-2xl text-emerald-400"></i>
                <h1 class="text-2xl font-black text-white tracking-tight">Broadcast Undangan & Reminder WhatsApp</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Kirimkan undangan resmi dan pengingat bertingkat langsung ke nomor WhatsApp personil crew</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= base_url('reminder/log') ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 transition">
                <i class="fa-solid fa-clock-rotate-left text-sky-400"></i>
                <span>Lihat Outbox Log</span>
            </a>
        </div>
    </div>

    <!-- Main Grid: Form Left, Preview Right -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Left (7 Cols) -->
        <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <form action="<?= base_url('reminder/send') ?>" method="POST" id="formBroadcast" class="space-y-5">
                <?= csrf_field() ?>

                <!-- Pilih Jadwal Meeting -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Pilih Jadwal Meeting <span class="text-rose-400">*</span></label>
                    <select name="meeting_id" id="meetingSelect" required onchange="window.location.href='<?= base_url('reminder?meeting_id=') ?>' + this.value" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                        <option value="">-- Pilih Jadwal Meeting --</option>
                        <?php foreach ($meetings as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($selectedMeeting && $selectedMeeting['id'] == $m['id']) ? 'selected' : '' ?>>
                                [<?= esc($m['rig_code']) ?>] <?= esc($m['title']) ?> (<?= date('d M', strtotime($m['meeting_date'])) ?> <?= substr($m['start_time'], 0, 5) ?> WIB)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($selectedMeeting): ?>
                    <!-- Jenis Reminder -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Tipe Pesan Reminder <span class="text-rose-400">*</span></label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <label class="flex flex-col items-center p-3 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 cursor-pointer transition text-center text-xs">
                                <input type="radio" name="reminder_type" value="UNDANGAN" checked onchange="updateMessagePreview()" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 mb-1">
                                <span class="font-bold text-white">Undangan</span>
                                <span class="text-[10px] text-slate-400">Pemberitahuan awal</span>
                            </label>
                            <label class="flex flex-col items-center p-3 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 cursor-pointer transition text-center text-xs">
                                <input type="radio" name="reminder_type" value="H-1" onchange="updateMessagePreview()" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 mb-1">
                                <span class="font-bold text-white">H-1</span>
                                <span class="text-[10px] text-slate-400">1 hari sebelum</span>
                            </label>
                            <label class="flex flex-col items-center p-3 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 cursor-pointer transition text-center text-xs">
                                <input type="radio" name="reminder_type" value="H-1_JAM" onchange="updateMessagePreview()" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 mb-1">
                                <span class="font-bold text-white">1 Jam</span>
                                <span class="text-[10px] text-slate-400">60 menit sebelum</span>
                            </label>
                            <label class="flex flex-col items-center p-3 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 cursor-pointer transition text-center text-xs">
                                <input type="radio" name="reminder_type" value="H-15_MIN" onchange="updateMessagePreview()" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 mb-1">
                                <span class="font-bold text-white">15 Menit</span>
                                <span class="text-[10px] text-slate-400">Segera gabung</span>
                            </label>
                        </div>
                    </div>

                    <!-- Target Penerima Crew -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Target Penerima (<?= count($participants) ?> Crew Terdaftar)</label>
                            <div class="flex items-center space-x-2 text-xs">
                                <button type="button" onclick="toggleAllCrews(true)" class="text-sky-400 hover:underline">Pilih Semua</button>
                                <span class="text-slate-600">&bull;</span>
                                <button type="button" onclick="toggleAllCrews(false)" class="text-slate-400 hover:underline">Batal</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-52 overflow-y-auto p-1 scrollbar-thin border border-slate-800 rounded-xl bg-slate-950/40">
                            <?php foreach ($participants as $p): ?>
                                <label class="flex items-center space-x-2.5 p-2 rounded-lg hover:bg-slate-800 cursor-pointer transition text-xs">
                                    <input type="checkbox" name="crew_ids[]" value="<?= $p['crew_id'] ?>" checked class="crew-checkbox w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 rounded focus:ring-emerald-500">
                                    <div class="truncate">
                                        <span class="font-bold text-white block truncate"><?= esc($p['crew_name']) ?></span>
                                        <span class="text-[10px] text-slate-400 font-mono block truncate">+<?= esc($p['phone']) ?> (<?= esc($p['position']) ?>)</span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-xs text-slate-400">
                            <i class="fa-solid fa-bolt text-amber-400"></i>
                            <span>Engine: WhatsApp Simulator & Gateway</span>
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center space-x-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Broadcast Sekarang</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-10 text-xs text-slate-500">
                        Pilih meeting di atas terlebih dahulu untuk memproses broadcast.
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Preview Right: WhatsApp Chat UI (5 Cols) -->
        <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-white block">Preview Pesan WhatsApp</span>
                        <span class="text-[10px] text-slate-400">Format yang diterima crew</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">LIVE TEMPLATE</span>
            </div>

            <!-- WhatsApp Chat Mockup -->
            <div class="flex-1 bg-slate-950 rounded-xl p-4 border border-slate-800 flex flex-col justify-end space-y-3 font-sans">
                <!-- Incoming Message Bubble -->
                <div class="bg-emerald-950/80 border border-emerald-800/60 rounded-2xl rounded-tl-none p-3.5 text-xs text-emerald-100 shadow-md space-y-2 whitespace-pre-wrap font-mono leading-relaxed" id="waMessagePreview">
                    <?php if ($selectedMeeting): ?>
*[UNDANGAN RESMI MEETING CREW RIG]*

Yth. *Agus Kurniawan* (Toolpusher)
Unit Rig: *<?= esc($selectedMeeting['rig_name']) ?>*

Anda diundang untuk menghadiri pertemuan rutin:
📋 *Agenda:* <?= esc($selectedMeeting['title']) ?>
📝 *Topik:* <?= esc($selectedMeeting['topic']) ?>
📅 *Tanggal:* <?= date('d M Y', strtotime($selectedMeeting['meeting_date'])) ?>
⏰ *Waktu:* <?= substr($selectedMeeting['start_time'], 0, 5) ?> - <?= substr($selectedMeeting['end_time'], 0, 5) ?> WIB
👤 *PJ Rig:* <?= esc($selectedMeeting['pj_name']) ?>

🔗 *Tautan Microsoft Teams:* 
<?= esc($selectedMeeting['teams_link']) ?>

Harap konfirmasi kehadiran dan hadir 5 menit sebelum meeting dimulai.
_Management PT Besmindo Oilfield Operations_
                    <?php else: ?>
Pilih meeting untuk melihat format pesan WhatsApp.
                    <?php endif; ?>
                </div>
                <div class="text-[10px] text-right text-slate-500">
                    <span><?= date('H:i') ?> WIB &bull; </span>
                    <i class="fa-solid fa-check-double text-sky-400"></i>
                </div>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const selectedMeeting = <?= json_encode($selectedMeeting) ?>;

    function toggleAllCrews(checked) {
        document.querySelectorAll('.crew-checkbox').forEach(cb => cb.checked = checked);
    }

    function updateMessagePreview() {
        if (!selectedMeeting) return;

        const type = document.querySelector('input[name="reminder_type"]:checked').value;
        const previewEl = document.getElementById('waMessagePreview');
        const tanggal = new Date(selectedMeeting.meeting_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        const jam = selectedMeeting.start_time.substring(0, 5) + ' - ' + selectedMeeting.end_time.substring(0, 5) + ' WIB';

        let text = '';
        if (type === 'H-1') {
            text = `*[REMINDER H-1 MEETING CREW RIG BESMINDO]*\n\n`
                 + `Halo Rekan *Agus Kurniawan* (Toolpusher),\n`
                 + `Mengingatkan bahwa besok akan diadakan pertemuan operasional:\n\n`
                 + `📌 *Meeting:* ${selectedMeeting.title}\n`
                 + `🏢 *Unit Rig:* ${selectedMeeting.rig_name}\n`
                 + `📅 *Hari/Tgl:* ${tanggal}\n`
                 + `⏰ *Waktu:* ${jam}\n`
                 + `🔗 *Link Microsoft Teams:* ${selectedMeeting.teams_link}\n\n`
                 + `Mohon mempersiapkan laporan harian dan bergabung tepat waktu. Terima kasih.`;
        } else if (type === 'H-1_JAM') {
            text = `*[REMINDER 1 JAM SEBELUM MEETING]*\n\n`
                 + `Halo *Agus Kurniawan*,\n`
                 + `Meeting *${selectedMeeting.title}* (${selectedMeeting.rig_name}) akan dimulai dalam *1 jam* (Pukul ${selectedMeeting.start_time.substring(0, 5)} WIB).\n\n`
                 + `Silakan pastikan koneksi internet dan perangkat Teams Anda siap:\n`
                 + `👉 ${selectedMeeting.teams_link}\n\n`
                 + `_PT Besmindo Oilfield Operations_`;
        } else if (type === 'H-15_MIN') {
            text = `*[PANGGILAN SEGERA BERGABUNG - 15 MENIT LAGI]*\n\n`
                 + `Halo *Agus Kurniawan*,\n`
                 + `Ruang meeting Microsoft Teams untuk *${selectedMeeting.title}* telah dibuka.\n\n`
                 + `Segera bergabung sekarang melalui link:\n`
                 + `👉 ${selectedMeeting.teams_link}\n\n`
                 + `Kehadiran Anda akan dicatat otomatis oleh sistem.`;
        } else {
            text = `*[UNDANGAN RESMI MEETING CREW RIG]*\n\n`
                 + `Yth. *Agus Kurniawan* (Toolpusher)\n`
                 + `Unit Rig: *${selectedMeeting.rig_name}*\n\n`
                 + `Anda diundang untuk menghadiri pertemuan rutin:\n`
                 + `📋 *Agenda:* ${selectedMeeting.title}\n`
                 + `📝 *Topik:* ${selectedMeeting.topic || 'Review Operasional Rig'}\n`
                 + `📅 *Tanggal:* ${tanggal}\n`
                 + `⏰ *Waktu:* ${jam}\n`
                 + `👤 *PJ Rig:* ${selectedMeeting.pj_name || '-'}\n\n`
                 + `🔗 *Tautan Microsoft Teams:* \n${selectedMeeting.teams_link}\n\n`
                 + `Harap konfirmasi kehadiran dan hadir 5 menit sebelum meeting dimulai.\n`
                 + `_Management PT Besmindo Oilfield Operations_`;
        }

        previewEl.innerText = text;
    }
</script>
<?= $this->endSection() ?>
