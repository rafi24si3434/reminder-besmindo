<div class="space-y-6">

    <!-- Header & Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-brands fa-whatsapp text-2xl text-emerald-400"></i>
                <h1 class="text-2xl font-black text-white tracking-tight">Pusat Layanan Undangan & Broadcast WhatsApp</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Distribusi undangan resmi dan pengingat bertingkat operasional rig ke seluruh personil crew</p>
        </div>

        <!-- Quick Navigation Tab Bar -->
        <div class="inline-flex p-1 bg-slate-900 border border-slate-800 rounded-xl shadow-lg">
            <a href="<?= base_url('reminder') ?>" class="px-3.5 py-1.5 rounded-lg bg-sky-600 text-white text-xs font-semibold shadow transition flex items-center space-x-1.5">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Broadcast</span>
            </a>
            <a href="<?= base_url('reminder/gateway') ?>" class="px-3.5 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-tower-broadcast text-sky-400"></i>
                <span>Status Gateway</span>
            </a>
            <a href="<?= base_url('reminder/log') ?>" class="px-3.5 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                <span>Log Outbox</span>
            </a>
        </div>
    </div>

    <!-- Active Sender Device Status Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800/80 to-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-sim-card"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-white uppercase tracking-wider">Perangkat Pengirim Resmi:</span>
                    <span class="font-mono font-bold text-emerald-400 text-xs">+62 <?= substr($senderNumber, 1) ?></span>
                </div>
                <div class="flex items-center space-x-2 mt-0.5 text-[11px] text-slate-400" id="gatewayLiveStatus">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Memeriksa koneksi perangkat WhatsApp...</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="<?= base_url('reminder/gateway') ?>" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-gear text-sky-400"></i>
                <span>Kelola Koneksi Perangkat</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Column -->
        <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <form action="<?= base_url('reminder/send') ?>" method="POST" id="formBroadcast" class="space-y-5">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Pilih Jadwal Meeting Rig Target <span class="text-rose-400">*</span></label>
                    <select name="meeting_id" id="meetingSelect" required onchange="window.location.href='<?= base_url('reminder?meeting_id=') ?>' + this.value" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                        <option value="">-- Pilih Jadwal Meeting --</option>
                        <?php foreach ($meetings as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($selectedMeeting && $selectedMeeting['id'] == $m['id']) ? 'selected' : '' ?>>
                                [<?= htmlspecialchars($m['rig_code']) ?>] <?= htmlspecialchars($m['title']) ?> (<?= date('d M Y', strtotime($m['meeting_date'])) ?> &bull; <?= substr($m['start_time'], 0, 5) ?> WIB)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($selectedMeeting): ?>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Tipe Pesan Broadcast <span class="text-rose-400">*</span></label>
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
                                <span class="text-[10px] text-slate-400">Panggilan darurat</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Pilih Penerima Crew (<?= count($participants) ?> Personil Terdaftar)</label>
                            <div class="flex items-center space-x-2 text-xs">
                                <button type="button" onclick="toggleAllCrews(true)" class="text-sky-400 hover:underline">Pilih Semua</button>
                                <span class="text-slate-600">&bull;</span>
                                <button type="button" onclick="toggleAllCrews(false)" class="text-slate-400 hover:underline">Batal</button>
                            </div>
                        </div>

                        <?php if (empty($participants)): ?>
                            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-center text-xs text-slate-400">
                                Belum ada personil crew yang ditambahkan pada meeting ini. <a href="<?= base_url('meeting/edit/' . $selectedMeeting['id']) ?>" class="text-sky-400 hover:underline">Tambah Personil &rarr;</a>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-52 overflow-y-auto p-1 scrollbar-thin border border-slate-800 rounded-xl bg-slate-950/40">
                                <?php foreach ($participants as $p): ?>
                                    <label class="flex items-center space-x-2.5 p-2 rounded-lg hover:bg-slate-800 cursor-pointer transition text-xs">
                                        <input type="checkbox" name="crew_ids[]" value="<?= $p['crew_id'] ?>" checked class="crew-checkbox w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 rounded focus:ring-emerald-500">
                                        <div class="truncate">
                                            <span class="font-bold text-white block truncate"><?= htmlspecialchars($p['crew_name']) ?></span>
                                            <span class="text-[10px] text-slate-400 font-mono block truncate">+<?= htmlspecialchars($p['phone']) ?> (<?= htmlspecialchars($p['position']) ?>)</span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-2 text-xs text-slate-400">
                            <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                            <span>Pengiriman Otentik via WhatsApp Server Besmindo</span>
                        </div>
                        <button type="submit" <?= empty($participants) ? 'disabled' : '' ?> class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Broadcast Sekarang</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 text-xs text-slate-500 border border-dashed border-slate-800 rounded-xl">
                        <i class="fa-regular fa-calendar-check text-3xl mb-2 text-slate-600 block"></i>
                        Silakan pilih jadwal meeting di atas untuk memulai broadcast undangan ke personil.
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Preview Column -->
        <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-white block">Preview Pesan WhatsApp</span>
                        <span class="text-[10px] text-slate-400">Format resmi yang diterima personil</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">LIVE TEMPLATE</span>
            </div>

            <div class="flex-1 bg-slate-950 rounded-xl p-4 border border-slate-800 flex flex-col justify-end space-y-3 font-sans">
                <div class="bg-emerald-950/80 border border-emerald-800/60 rounded-2xl rounded-tl-none p-3.5 text-xs text-emerald-100 shadow-md space-y-2 whitespace-pre-wrap font-mono leading-relaxed" id="waMessagePreview">
                    <?php if ($selectedMeeting):
                        $prvName = !empty($participants) ? $participants[0]['crew_name'] : '[Nama Crew]';
                        $prvPos  = !empty($participants) ? $participants[0]['position']  : 'Jabatan';
                        $prvPj   = !empty($selectedMeeting['pj_name']) ? $selectedMeeting['pj_name'] : 'Management Besmindo';
                        $prvTopik = !empty($selectedMeeting['topic'])  ? $selectedMeeting['topic']   : 'Operasional Rutin Rig';
                    ?>
*[UNDANGAN RESMI MEETING CREW RIG]*

Yth. *<?= htmlspecialchars($prvName) ?>* (<?= htmlspecialchars($prvPos) ?>)
Unit Rig: *<?= htmlspecialchars($selectedMeeting['rig_name']) ?>*

Anda diundang untuk menghadiri pertemuan rutin:
📋 *Agenda:* <?= htmlspecialchars($selectedMeeting['title']) ?>

📝 *Topik:* <?= htmlspecialchars($prvTopik) ?>

📅 *Tanggal:* <?= date('d M Y', strtotime($selectedMeeting['meeting_date'])) ?>

⏰ *Waktu:* <?= substr($selectedMeeting['start_time'], 0, 5) ?> - <?= substr($selectedMeeting['end_time'], 0, 5) ?> WIB
👤 *PJ Rig:* <?= htmlspecialchars($prvPj) ?>

🔗 *Tautan Microsoft Teams:* 
<?= htmlspecialchars($selectedMeeting['teams_link']) ?>

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

<script>
    const selectedMeeting = <?= json_encode($selectedMeeting) ?>;
    // Use first participant for preview (real name from DB)
    const previewCrew = {
        name: '<?= !empty($participants) ? addslashes($participants[0]['crew_name']) : "[Nama Crew]" ?>',
        position: '<?= !empty($participants) ? addslashes($participants[0]['position']) : "Jabatan" ?>'
    };

    function toggleAllCrews(checked) {
        document.querySelectorAll('.crew-checkbox').forEach(cb => cb.checked = checked);
    }

    function checkGatewayLiveStatus() {
        fetch('<?= base_url('reminder/fonnte_status') ?>')
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('gatewayLiveStatus');
                if (data.success && data.connected) {
                    el.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-emerald-400 font-semibold">Status: Fonnte Online &bull; ${data.deviceNumber ? 'No: ' + data.deviceNumber : 'Siap Kirim Broadcast'}</span>
                    `;
                } else if (data.success && !data.connected) {
                    el.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span class="text-amber-400">Status: Fonnte Perlu Scan QR &bull; <a href="<?= base_url('reminder/gateway') ?>" class="underline font-semibold hover:text-white">Buka Gateway</a></span>
                    `;
                } else {
                    el.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span class="text-amber-400">Status: Token Fonnte Belum Diisi &bull; <a href="<?= base_url('reminder/gateway') ?>" class="underline font-semibold hover:text-white">Masukkan Token di Sini</a></span>
                    `;
                }
            })
            .catch(err => {
                const el = document.getElementById('gatewayLiveStatus');
                el.innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span class="text-slate-400">Fonnte Cloud Gateway &bull; <a href="<?= base_url('reminder/gateway') ?>" class="underline font-semibold hover:text-white">Pengaturan</a></span>
                `;
            });
    }

    function updateMessagePreview() {
        if (!selectedMeeting) return;

        const type = document.querySelector('input[name="reminder_type"]:checked').value;
        const previewEl = document.getElementById('waMessagePreview');

        // Format date properly
        const rawDate = selectedMeeting.meeting_date ? selectedMeeting.meeting_date.substring(0, 10) : '';
        const dateObj = rawDate ? new Date(rawDate + 'T00:00:00') : null;
        const tanggal = dateObj ? dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
        const jam = selectedMeeting.start_time.substring(0, 5) + ' - ' + selectedMeeting.end_time.substring(0, 5) + ' WIB';
        const pj    = selectedMeeting.pj_name   || 'Management Besmindo';
        const topik = selectedMeeting.topic      || 'Operasional Rutin Rig';

        let text = '';
        if (type === 'H-1') {
            text = `*[REMINDER H-1 MEETING CREW RIG BESMINDO]*\n\n`
                 + `Halo Rekan *${previewCrew.name}* (${previewCrew.position}),\n`
                 + `Mengingatkan bahwa besok akan diadakan pertemuan operasional:\n\n`
                 + `📌 *Meeting:* ${selectedMeeting.title}\n`
                 + `🏢 *Unit Rig:* ${selectedMeeting.rig_name}\n`
                 + `📅 *Hari/Tgl:* ${tanggal}\n`
                 + `⏰ *Waktu:* ${jam}\n`
                 + `🔗 *Link Microsoft Teams:* ${selectedMeeting.teams_link}\n\n`
                 + `Mohon mempersiapkan laporan harian dan bergabung tepat waktu.\n`
                 + `_Management PT Besmindo Oilfield Operations_`;
        } else if (type === 'H-1_JAM') {
            text = `*[REMINDER 1 JAM SEBELUM MEETING]*\n\n`
                 + `Halo *${previewCrew.name}* (${previewCrew.position}),\n`
                 + `Meeting *${selectedMeeting.title}* – Rig *${selectedMeeting.rig_name}*\n`
                 + `akan dimulai dalam *1 jam* pukul ${selectedMeeting.start_time.substring(0, 5)} WIB.\n\n`
                 + `Pastikan koneksi internet dan perangkat Microsoft Teams Anda siap:\n`
                 + `👉 ${selectedMeeting.teams_link}\n\n`
                 + `_PT Besmindo Oilfield Operations_`;
        } else if (type === 'H-15_MIN') {
            text = `*[SEGERA BERGABUNG – 15 MENIT LAGI]*\n\n`
                 + `Halo *${previewCrew.name}* (${previewCrew.position}),\n`
                 + `Ruang Microsoft Teams untuk meeting *${selectedMeeting.title}* – Rig *${selectedMeeting.rig_name}* telah dibuka.\n\n`
                 + `Bergabunglah sekarang:\n`
                 + `👉 ${selectedMeeting.teams_link}\n\n`
                 + `Kehadiran Anda dicatat otomatis oleh sistem.\n`
                 + `_Management PT Besmindo Oilfield Operations_`;
        } else {
            text = `*[UNDANGAN RESMI MEETING CREW RIG]*\n\n`
                 + `Yth. *${previewCrew.name}* (${previewCrew.position})\n`
                 + `Unit Rig: *${selectedMeeting.rig_name}*\n\n`
                 + `Anda diundang untuk menghadiri pertemuan rutin:\n`
                 + `📋 *Agenda:* ${selectedMeeting.title}\n`
                 + `📝 *Topik:* ${topik}\n`
                 + `📅 *Tanggal:* ${tanggal}\n`
                 + `⏰ *Waktu:* ${jam}\n`
                 + `👤 *PJ Rig:* ${pj}\n\n`
                 + `🔗 *Tautan Microsoft Teams:* \n${selectedMeeting.teams_link}\n\n`
                 + `Harap konfirmasi kehadiran dan hadir 5 menit sebelum meeting dimulai.\n`
                 + `_Management PT Besmindo Oilfield Operations_`;
        }

        previewEl.innerText = text;
    }

    document.addEventListener('DOMContentLoaded', () => {
        checkGatewayLiveStatus();
        setInterval(checkGatewayLiveStatus, 5000);
        updateMessagePreview();
    });
</script>
