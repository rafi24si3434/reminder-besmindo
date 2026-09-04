<div class="space-y-6">

    <!-- Header & Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-brands fa-whatsapp text-2xl text-emerald-400"></i>
                <h1 class="text-2xl font-black text-white tracking-tight">Pusat Layanan Undangan & Broadcast WhatsApp</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Distribusi undangan resmi dan pengingat bertingkat ke WhatsApp Group Rig & personil crew</p>
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
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-white uppercase tracking-wider">Engine WhatsApp Gateway:</span>
                    <span class="font-mono font-bold text-emerald-400 text-xs">Gateway Mandiri</span>
                </div>
                <div class="flex items-center space-x-2 mt-0.5 text-[11px] text-slate-400" id="gatewayLiveStatus">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Memeriksa status Gateway Mandiri...</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="<?= base_url('reminder/gateway') ?>" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-gear text-sky-400"></i>
                <span>Pengaturan Gateway</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Form Column -->
        <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <form action="<?= base_url('reminder/send') ?>" method="POST" id="formBroadcast" class="space-y-5">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Pilih Jadwal Meeting Rig Target <span class="text-rose-400">*</span></label>
                    <input type="hidden" name="meeting_id" value="<?= $selectedMeeting ? $selectedMeeting['id'] : '' ?>">
                    <div class="flex flex-wrap items-center gap-3">
                        <?php $this->load->view('components/meeting_switcher', array(
                            'meetings'        => $meetings,
                            'current_meeting' => $selectedMeeting,
                            'target_route'    => 'reminder?meeting_id=',
                            'mode'            => 'query_param'
                        )); ?>
                        <?php if (!$selectedMeeting): ?>
                            <span class="text-xs text-amber-400 animate-pulse flex items-center space-x-1 font-semibold">
                                <i class="fa-solid fa-hand-pointer"></i>
                                <span>Klik tombol di atas untuk memilih jadwal meeting target</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($selectedMeeting): ?>

                    <?php if ($selectedMeeting['status'] === 'completed'): ?>
                        <div class="bg-gradient-to-r from-emerald-950/70 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-2xl p-4 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 shrink-0">
                                    <i class="fa-solid fa-lock text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Sesi Rapat Resmi Telah Ditutup</h4>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-mono">
                                            <?= !empty($selectedMeeting['closed_at']) ? date('d M Y, H:i', strtotime($selectedMeeting['closed_at'])) . ' WIB' : 'Completed' ?>
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Seluruh data absensi telah dikunci &amp; direkap. Anda dapat melihat hasil notulensi atau mencetak laporan resmi.
                                    </p>
                                </div>
                            </div>
                            <a href="<?= base_url('attendance/rekap/' . $selectedMeeting['id']) ?>" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center space-x-1.5 shrink-0">
                                <i class="fa-solid fa-file-signature"></i>
                                <span>Buka Rekap Absensi</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="bg-slate-950/40 border border-slate-800 rounded-xl p-3 flex items-center justify-between text-xs text-slate-300">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                <span>Status Rapat: <strong class="text-white">Aktif (In Progress)</strong></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="<?= base_url('attendance/rekap/' . $selectedMeeting['id']) ?>" class="text-xs font-semibold text-sky-400 hover:text-sky-300 underline flex items-center space-x-1">
                                    <i class="fa-solid fa-file-lines"></i>
                                    <span>Tutup Sesi &amp; Rekap Absensi &rarr;</span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Target Broadcast Mode (Group vs Personal) -->
                    <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-white uppercase tracking-wider">
                                Mode Tujuan Pengiriman Broadcast
                            </label>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                🛡️ PROTEKSI ANTI-BAN
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <label class="flex flex-col p-3 rounded-xl border border-emerald-500/50 bg-emerald-950/20 hover:bg-emerald-950/40 cursor-pointer transition text-xs">
                                <div class="flex items-center space-x-2 mb-1">
                                    <input type="radio" name="target_mode" value="GROUP" checked onchange="handleTargetModeChange(this.value)" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700">
                                    <span class="font-bold text-emerald-300">WhatsApp Group Rig</span>
                                </div>
                                <span class="text-[10px] text-slate-400 leading-tight">100% Anti-Ban Aman. Kirim langsung ke grup unit Rig.</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 cursor-pointer transition text-xs">
                                <div class="flex items-center space-x-2 mb-1">
                                    <input type="radio" name="target_mode" value="PERSONAL" onchange="handleTargetModeChange(this.value)" class="w-4 h-4 text-sky-500 bg-slate-900 border-slate-700">
                                    <span class="font-bold text-white">Japri Personal Crew</span>
                                </div>
                                <span class="text-[10px] text-slate-400 leading-tight">Kirim 1 per 1 dengan jeda delay anti-ban.</span>
                            </label>

                            <label class="flex flex-col p-3 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 cursor-pointer transition text-xs">
                                <div class="flex items-center space-x-2 mb-1">
                                    <input type="radio" name="target_mode" value="BOTH" onchange="handleTargetModeChange(this.value)" class="w-4 h-4 text-purple-500 bg-slate-900 border-slate-700">
                                    <span class="font-bold text-white">Group + Japri</span>
                                </div>
                                <span class="text-[10px] text-slate-400 leading-tight">Kirim ke grup Rig sekaligus japri ke crew.</span>
                            </label>
                        </div>

                        <!-- Info Group Status of Selected Rig -->
                        <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-2">
                                <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                                <span class="text-slate-400">Grup Unit Rig [<?= htmlspecialchars($selectedMeeting['rig_name']) ?>]:</span>
                                <?php if (!empty($selectedMeeting['wa_group_id'])): ?>
                                    <strong class="text-emerald-400 font-mono"><?= htmlspecialchars($selectedMeeting['wa_group_name'] ?: $selectedMeeting['wa_group_id']) ?></strong>
                                <?php else: ?>
                                    <span class="text-amber-400 italic">Belum disetting</span>
                                <?php endif; ?>
                            </div>
                            <?php if (empty($selectedMeeting['wa_group_id'])): ?>
                                <a href="<?= base_url('rig/edit/' . $selectedMeeting['rig_id']) ?>" class="text-[11px] text-sky-400 hover:underline font-semibold flex items-center space-x-1">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Atur ID Grup di Master Rig</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

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

                    <!-- Crew selection for personal / tagging -->
                    <div id="crewSelectionBox">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Daftar Personil Crew (<?= count($participants) ?> Terdaftar)</label>
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-1 scrollbar-thin border border-slate-800 rounded-xl bg-slate-950/40">
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
                        <div class="flex items-center space-x-2.5 text-xs text-slate-400">
                            <i class="fa-solid fa-shield-heart text-emerald-400 text-lg"></i>
                            <div>
                                <span class="text-emerald-400 font-semibold block">Proteksi Anti-Ban Aktif</span>
                                <span class="text-[10px] text-slate-500">Jeda acak 15–35 detik &bull; Simulasi ketik &bull; Personalisasi nama personil</span>
                            </div>
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Broadcast Sekarang</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 text-xs text-slate-500 border border-dashed border-slate-800 rounded-xl">
                        <i class="fa-regular fa-calendar-check text-3xl mb-2 text-slate-600 block"></i>
                        Silakan pilih jadwal meeting di atas untuk memulai broadcast undangan ke WhatsApp.
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Right Column: Trimmed Preview & Live Queue Card -->
        <div class="lg:col-span-5 space-y-4 flex flex-col">

            <!-- Card 1: Preview Pesan WhatsApp (Dipangkas & Ringkas) -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5 mb-2.5">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-white block">Preview Pesan WhatsApp</span>
                            <span class="text-[10px] text-slate-400" id="previewModeLabel">Format Pesan WhatsApp Group</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[9px] font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">RINGKASAN</span>
                        <button type="button" onclick="togglePreviewBody()" class="text-slate-400 hover:text-white text-xs px-1.5 py-0.5 rounded hover:bg-slate-800 transition" title="Ciutkan / Buka">
                            <i id="togglePreviewIcon" class="fa-solid fa-chevron-up text-xs"></i>
                        </button>
                    </div>
                </div>

                <div id="previewBodyContainer" class="space-y-2">
                    <div class="bg-emerald-950/70 border border-emerald-800/50 rounded-xl rounded-tl-none p-3 text-[11px] text-emerald-100 shadow-inner whitespace-pre-wrap font-mono leading-relaxed max-h-40 overflow-y-auto scrollbar-thin" id="waMessagePreview">
                        Pilih meeting untuk melihat format pesan WhatsApp.
                    </div>
                    <div class="text-[10px] text-right text-slate-500 flex items-center justify-end space-x-1">
                        <span><?= date('H:i') ?> WIB</span>
                        <i class="fa-solid fa-check-double text-sky-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Status Antrian & Log Pengiriman Nomor (Live Queue & Delivery Tracker) -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl flex-1 flex flex-col space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-white">Status Antrian & Pengiriman</h3>
                            <p class="text-[10px] text-slate-400">Pantau nomor yang sedang dikirim, antrian, dan sukses</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping mr-1"></span> LIVE
                        </span>
                        <button type="button" onclick="pollQueueData(true)" class="p-1 text-slate-400 hover:text-white text-xs transition" title="Refresh Sekarang">
                            <i class="fa-solid fa-rotate"></i>
                        </button>
                    </div>
                </div>

                <!-- Live Summary Counters -->
                <div class="grid grid-cols-3 gap-2 text-center text-[10px]">
                    <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-2">
                        <span class="text-slate-400 block text-[9px]">Terkirim</span>
                        <span class="font-bold text-emerald-400 text-sm font-mono" id="statSentCount">0</span>
                    </div>
                    <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-2">
                        <span class="text-slate-400 block text-[9px]">Sedang Diproses</span>
                        <span class="font-bold text-amber-400 text-sm font-mono" id="statActiveCount">0</span>
                    </div>
                    <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-2">
                        <span class="text-slate-400 block text-[9px]">Dalam Antrian</span>
                        <span class="font-bold text-slate-300 text-sm font-mono" id="statWaitingCount">0</span>
                    </div>
                </div>

                <!-- Active Processing Banner (Highlight saat sedang mengetik / jeda) -->
                <div id="activeBannerBox" class="hidden bg-amber-500/10 border border-amber-500/30 rounded-xl p-2.5 text-xs text-amber-300 space-y-1">
                    <div class="flex items-center justify-between font-bold text-[11px]">
                        <span class="flex items-center space-x-1.5">
                            <i class="fa-solid fa-spinner fa-spin text-amber-400"></i>
                            <span id="activeBannerTitle">Sedang Mengirim Pesan...</span>
                        </span>
                        <span class="font-mono text-[9px] bg-amber-500/20 px-1.5 py-0.5 rounded text-amber-300" id="activeBannerBadge">Ketik 4s</span>
                    </div>
                    <p class="text-[10px] text-amber-200/80 truncate" id="activeBannerDesc">Tujuan: Personil Rig</p>
                </div>

                <!-- Detailed List of Numbers & Recipients -->
                <div class="space-y-1.5 flex-1 max-h-72 overflow-y-auto pr-1 scrollbar-thin" id="queueItemsContainer">
                    <div class="text-center py-8 text-xs text-slate-500 border border-dashed border-slate-800 rounded-xl">
                        <i class="fa-solid fa-inbox text-2xl mb-1.5 text-slate-600 block"></i>
                        Memuat data antrian pengiriman...
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-[10px] text-slate-400">
                    <span id="queueLastUpdated"><i class="fa-solid fa-shield-halved text-emerald-400 mr-1"></i> Proteksi Anti-Ban 15–35s Aktif</span>
                    <a href="<?= base_url('reminder/log' . (!empty($selectedMeeting) ? '?meeting_id=' . $selectedMeeting['id'] : '')) ?>" class="text-sky-400 hover:underline font-semibold flex items-center space-x-1">
                        <span>Log Lengkap Outbox</span>
                        <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

<script>
    const selectedMeeting = <?= json_encode($selectedMeeting) ?>;
    const participantsList = <?= json_encode($participants) ?>;
    let currentTargetMode = 'GROUP';

    const previewCrew = {
        name: '<?= !empty($participants) ? addslashes($participants[0]['crew_name']) : "[Nama Crew]" ?>',
        position: '<?= !empty($participants) ? addslashes($participants[0]['position']) : "Jabatan" ?>'
    };

    function toggleAllCrews(checked) {
        document.querySelectorAll('.crew-checkbox').forEach(cb => cb.checked = checked);
    }

    function handleTargetModeChange(mode) {
        currentTargetMode = mode;
        const label = document.getElementById('previewModeLabel');
        if (mode === 'GROUP') {
            label.textContent = 'Format Pesan WhatsApp Group (Anti-Ban)';
        } else if (mode === 'PERSONAL') {
            label.textContent = 'Format Pesan Japri Personal';
        } else {
            label.textContent = 'Format Pesan Group & Japri';
        }
        updateMessagePreview();
    }

    function checkGatewayLiveStatus() {
        fetch('http://localhost:3000/status')
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('gatewayLiveStatus');
                if (data.connected) {
                    const queueInfo = data.antiBan && data.antiBan.queueLength > 0 
                        ? ` &bull; <span class="text-amber-400 font-bold"><i class="fa-solid fa-hourglass-half"></i> Antrian: ${data.antiBan.queueLength}</span>` 
                        : '';
                    const delayInfo = data.antiBan ? ` &bull; Jeda Aman: ${data.antiBan.minDelaySec}–${data.antiBan.maxDelaySec}s` : '';
                    el.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-emerald-400 font-semibold">Status: Online ${data.senderNumber ? '(' + data.senderNumber + ')' : ''}${delayInfo}${queueInfo}</span>
                    `;
                } else if (data.qr) {
                    el.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span class="text-amber-400">Status: Menunggu Scan QR &bull; <a href="<?= base_url('reminder/gateway') ?>" class="underline font-semibold hover:text-white">Scan Sekarang</a></span>
                    `;
                } else {
                    el.innerHTML = `
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span class="text-amber-400">Status: Menginisialisasi... &bull; <a href="<?= base_url('reminder/gateway') ?>" class="underline font-semibold hover:text-white">Buka Gateway</a></span>
                    `;
                }
            })
            .catch(err => {
                const el = document.getElementById('gatewayLiveStatus');
                el.innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                    <span class="text-rose-400">Service Gateway Mandiri Offline &bull; <a href="<?= base_url('reminder/gateway') ?>" class="underline font-semibold hover:text-white">Panduan Service</a></span>
                `;
            });
    }

    function updateMessagePreview() {
        if (!selectedMeeting) return;

        const type = document.querySelector('input[name="reminder_type"]:checked').value;
        const previewEl = document.getElementById('waMessagePreview');

        const rawDate = selectedMeeting.meeting_date ? selectedMeeting.meeting_date.substring(0, 10) : '';
        const dateObj = rawDate ? new Date(rawDate + 'T00:00:00') : null;
        const tanggal = dateObj ? dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
        const jam = selectedMeeting.start_time.substring(0, 5) + ' - ' + selectedMeeting.end_time.substring(0, 5) + ' WIB';
        const pj = selectedMeeting.pj_name || 'Management Besmindo';
        const topik = selectedMeeting.topic || 'Operasional Rutin Rig';

        // Deteksi salam waktu
        const curHour = new Date().getHours();
        let salam = 'Selamat Pagi';
        if (curHour >= 11 && curHour < 15) salam = 'Selamat Siang';
        else if (curHour >= 15 && curHour < 18) salam = 'Selamat Sore';
        else if (curHour >= 18 || curHour < 4) salam = 'Selamat Malam';

        let text = '';

        if (currentTargetMode === 'GROUP' || currentTargetMode === 'BOTH') {
            // Group Message Template
            let crewTags = '';
            if (participantsList && participantsList.length > 0) {
                crewTags = '\n👥 *Daftar Personil Diundang:*\n';
                participantsList.forEach((p, idx) => {
                    crewTags += `${idx + 1}. *${p.crew_name}* (${p.position})\n`;
                });
            }

            if (type === 'H-1') {
                text = `*[REMINDER H-1 MEETING CREW RIG]*\n`
                     + `🏢 *Unit Rig:* ${selectedMeeting.rig_name}\n\n`
                     + `Pemberitahuan kepada seluruh personil crew rig bahwa besok akan diselenggarakan pertemuan operasional:\n\n`
                     + `📋 *Agenda:* ${selectedMeeting.title}\n`
                     + `📝 *Topik:* ${topik}\n`
                     + `📅 *Hari/Tgl:* ${tanggal}\n`
                     + `⏰ *Waktu:* ${jam}\n`
                     + `👤 *PJ Rig:* ${pj}\n`
                     + `${crewTags}\n`
                     + `🔗 *Link Microsoft Teams:* \n${selectedMeeting.teams_link}\n\n`
                     + `Harap seluruh personil mempersiapkan laporan harian dan hadir tepat waktu.\n`
                     + `_Management PT. Besmindo Materi Sewatama_`;
            } else if (type === 'H-1_JAM') {
                text = `*[PENGINGAT 1 JAM SEBELUM MEETING]*\n`
                     + `🏢 *Unit Rig:* ${selectedMeeting.rig_name}\n\n`
                     + `Pertemuan operasional *${selectedMeeting.title}* akan dimulai dalam *1 jam* (Pukul ${selectedMeeting.start_time.substring(0, 5)} WIB).\n\n`
                     + `Pastikan perangkat Microsoft Teams Anda siap:\n`
                     + `👉 ${selectedMeeting.teams_link}\n\n`
                     + `_PT. Besmindo Materi Sewatama_`;
            } else if (type === 'H-15_MIN') {
                text = `*[PANGGILAN BERGABUNG - 15 MENIT LAGI]*\n`
                     + `🏢 *Unit Rig:* ${selectedMeeting.rig_name}\n\n`
                     + `Ruang meeting Microsoft Teams untuk *${selectedMeeting.title}* telah dibuka.\n`
                     + `Seluruh personil crew dimohon segera bergabung:\n`
                     + `👉 ${selectedMeeting.teams_link}\n\n`
                     + `Kehadiran Anda dicatat otomatis oleh sistem.`;
            } else {
                text = `*[UNDANGAN RESMI OPERASIONAL MEETING]*\n`
                     + `🏢 *Unit Rig:* ${selectedMeeting.rig_name}\n\n`
                     + `Kepada seluruh rekan crew unit ${selectedMeeting.rig_name}, Anda diundang untuk menghadiri pertemuan rutin:\n\n`
                     + `📋 *Agenda:* ${selectedMeeting.title}\n`
                     + `📝 *Topik:* ${topik}\n`
                     + `📅 *Tanggal:* ${tanggal}\n`
                     + `⏰ *Waktu:* ${jam}\n`
                     + `👤 *PJ Rig:* ${pj}\n`
                     + `${crewTags}\n`
                     + `🔗 *Tautan Microsoft Teams:* \n${selectedMeeting.teams_link}\n\n`
                     + `Harap seluruh personil hadir 5 menit sebelum meeting dimulai.\n`
                     + `_Management PT. Besmindo Materi Sewatama_`;
            }
        } else {
            // Personal Message Template (Anti-Ban Hyper Personalized)
            const opener = `${salam} Bapak/Rekan *${previewCrew.name}* (${previewCrew.position}),`;
            if (type === 'H-1') {
                text = `*[REMINDER H-1 MEETING CREW RIG BESMINDO]*\n\n`
                     + `${opener}\n`
                     + `Mengingatkan bahwa besok akan diselenggarakan pertemuan operasional crew:\n\n`
                     + `📌 *Agenda:* ${selectedMeeting.title}\n`
                     + `🏢 *Unit Rig:* ${selectedMeeting.rig_name}\n`
                     + `📝 *Topik:* ${topik}\n`
                     + `📅 *Hari/Tgl:* ${tanggal}\n`
                     + `⏰ *Waktu:* ${jam}\n`
                     + `👤 *PJ Rig:* ${pj}\n\n`
                     + `🔗 *Link Microsoft Teams:* \n${selectedMeeting.teams_link}\n\n`
                     + `Mohon mempersiapkan laporan harian operasional dan hadir tepat waktu.\n`
                     + `_Management PT. Besmindo Materi Sewatama_\n`
                     + `_(Ref: #BSM-7E91C4)_`;
            } else if (type === 'H-1_JAM') {
                text = `*[PENGINGAT 1 JAM SEBELUM MEETING]*\n\n`
                     + `${opener}\n`
                     + `Meeting operasional *${selectedMeeting.title}* untuk Unit Rig *${selectedMeeting.rig_name}* akan dimulai dalam *1 jam* (Pukul ${selectedMeeting.start_time.substring(0, 5)} WIB).\n\n`
                     + `Pastikan koneksi internet dan perangkat Microsoft Teams Anda siap:\n`
                     + `👉 ${selectedMeeting.teams_link}\n\n`
                     + `Kehadiran Bapak/Rekan *${previewCrew.name}* sangat diharapkan tepat waktu.\n`
                     + `_PT. Besmindo Materi Sewatama_\n`
                     + `_(Ref: #BSM-7E91C4)_`;
            } else if (type === 'H-15_MIN') {
                text = `*[PANGGILAN BERGABUNG – 15 MENIT LAGI]*\n\n`
                     + `${opener}\n`
                     + `Ruang meeting Microsoft Teams untuk *${selectedMeeting.title}* – Rig *${selectedMeeting.rig_name}* telah dibuka.\n\n`
                     + `Mohon segera bergabung:\n`
                     + `👉 ${selectedMeeting.teams_link}\n\n`
                     + `Kehadiran Bapak/Rekan dicatat otomatis oleh sistem.\n`
                     + `_Management PT. Besmindo Materi Sewatama_\n`
                     + `_(Ref: #BSM-7E91C4)_`;
            } else {
                text = `*[UNDANGAN RESMI OPERASIONAL MEETING]*\n\n`
                     + `${opener}\n\n`
                     + `Bapak/Rekan diundang untuk menghadiri pertemuan rutin operasional Rig *${selectedMeeting.rig_name}*:\n\n`
                     + `📋 *Agenda:* ${selectedMeeting.title}\n`
                     + `📝 *Topik:* ${topik}\n`
                     + `📅 *Tanggal:* ${tanggal}\n`
                     + `⏰ *Waktu:* ${jam}\n`
                     + `👤 *PJ Rig:* ${pj}\n\n`
                     + `🔗 *Tautan Microsoft Teams:* \n${selectedMeeting.teams_link}\n\n`
                     + `Harap konfirmasi kehadiran dan hadir 5 menit sebelum meeting dimulai.\n`
                     + `_Management PT. Besmindo Materi Sewatama_\n`
                     + `_(Ref: #BSM-7E91C4)_`;
            }
        }

        previewEl.innerText = text;
    }

    const initialRecentLogs = <?= json_encode(!empty($recentLogs) ? $recentLogs : array()) ?>;
    let isPreviewCollapsed = false;

    function togglePreviewBody() {
        const container = document.getElementById('previewBodyContainer');
        const icon = document.getElementById('togglePreviewIcon');
        isPreviewCollapsed = !isPreviewCollapsed;
        if (isPreviewCollapsed) {
            container.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        } else {
            container.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    }

    function pollQueueData(manual = false) {
        fetch('http://localhost:3000/status')
            .then(r => r.json())
            .then(data => {
                renderQueueCard(data);
                if (manual && typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        background: '#1e293b',
                        color: '#f8fafc'
                    });
                    Toast.fire({ icon: 'success', title: 'Data antrian disinkronkan' });
                }
            })
            .catch(() => {
                renderDbLogsFallback();
            });
    }

    function renderQueueCard(data) {
        const queueData = data.queue || {};
        const currentlyProcessing = queueData.currentlyProcessing || null;
        const waitingItems = queueData.waiting || [];
        const historyItems = queueData.history || [];

        // 1. Update Counters
        const statSent = document.getElementById('statSentCount');
        const statActive = document.getElementById('statActiveCount');
        const statWaiting = document.getElementById('statWaitingCount');

        const totalSentCount = (historyItems.filter(h => h.status === 'sent').length) || (initialRecentLogs.filter(l => l.status === 'sent').length);
        if (statSent) statSent.textContent = totalSentCount;
        if (statActive) statActive.textContent = currentlyProcessing ? '1' : '0';
        if (statWaiting) statWaiting.textContent = waitingItems.length;

        // 2. Active Banner
        const activeBox = document.getElementById('activeBannerBox');
        const activeTitle = document.getElementById('activeBannerTitle');
        const activeBadge = document.getElementById('activeBannerBadge');
        const activeDesc = document.getElementById('activeBannerDesc');

        if (currentlyProcessing) {
            activeBox.classList.remove('hidden');
            activeTitle.textContent = currentlyProcessing.statusText || 'Sedang memproses...';
            activeDesc.textContent = `Tujuan: ${currentlyProcessing.recipientName} (+${currentlyProcessing.number})`;
            activeBadge.textContent = currentlyProcessing.status === 'waiting_delay' ? `Jeda ${currentlyProcessing.delaySec || 25}s` : 'Ketik WA';
        } else {
            activeBox.classList.add('hidden');
        }

        // 3. Render List of items
        const container = document.getElementById('queueItemsContainer');
        let html = '';

        // Item 1: Currently active / processing
        if (currentlyProcessing) {
            html += `
                <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/40 shadow-sm transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            <span class="font-bold text-amber-300 text-xs">${escapeHtml(currentlyProcessing.recipientName)}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            ${currentlyProcessing.status === 'waiting_delay' ? '⏳ JEDA AMAN' : '⌨️ MENGETIK'}
                        </span>
                    </div>
                    <div class="mt-1 flex items-center justify-between text-[10px] text-slate-400">
                        <span class="font-mono text-slate-300">+${escapeHtml(currentlyProcessing.number)}</span>
                        <span class="text-amber-200/90 font-medium">${currentlyProcessing.statusText}</span>
                    </div>
                </div>
            `;
        }

        // Item 2: Waiting queue items
        if (waitingItems.length > 0) {
            waitingItems.forEach(item => {
                html += `
                    <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                <span class="font-semibold text-white text-xs">${escapeHtml(item.recipientName)}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-mono bg-slate-800 text-slate-300 border border-slate-700">
                                ANTRIAN #${item.position}
                            </span>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-[10px] text-slate-400">
                            <span class="font-mono text-slate-400">+${escapeHtml(item.number)}</span>
                            <span class="text-slate-500">Est. Kirim: ~${item.estWaitSec || 25} detik</span>
                        </div>
                    </div>
                `;
            });
        }

        // Item 3: History sent items (from current session)
        if (historyItems.length > 0) {
            historyItems.forEach(item => {
                const isSent = item.status === 'sent';
                const statusLabel = isSent ? 'TERKIRIM' : 'GAGAL';
                html += `
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-emerald-900/40 hover:border-emerald-700/50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                                <span class="font-semibold text-white text-xs">${escapeHtml(item.recipientName)}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                ✅ ${statusLabel}
                            </span>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-[10px] text-slate-400">
                            <span class="font-mono text-slate-300">+${escapeHtml(item.number)}</span>
                            <span class="text-slate-500 font-mono">Pukul ${item.time || '-'} WIB</span>
                        </div>
                    </div>
                `;
            });
        }

        // Fallback: If no gateway memory items, show recent logs from DB
        if (!currentlyProcessing && waitingItems.length === 0 && historyItems.length === 0) {
            renderDbLogsFallback();
            return;
        }

        container.innerHTML = html;
    }

    function renderDbLogsFallback() {
        const container = document.getElementById('queueItemsContainer');
        const statSent = document.getElementById('statSentCount');
        const statActive = document.getElementById('statActiveCount');
        const statWaiting = document.getElementById('statWaitingCount');

        if (statActive) statActive.textContent = '0';
        if (statWaiting) statWaiting.textContent = '0';

        if (!initialRecentLogs || initialRecentLogs.length === 0) {
            if (statSent) statSent.textContent = '0';
            container.innerHTML = `
                <div class="text-center py-8 text-xs text-slate-500 border border-dashed border-slate-800 rounded-xl">
                    <i class="fa-solid fa-inbox text-2xl mb-1.5 text-slate-600 block"></i>
                    Belum ada antrian atau riwayat pengiriman.<br>
                    <span class="text-[10px] text-slate-400 mt-1 inline-block">Klik <strong>"Kirim Broadcast Sekarang"</strong> untuk memulai.</span>
                </div>
            `;
            return;
        }

        if (statSent) statSent.textContent = initialRecentLogs.filter(l => l.status === 'sent').length;

        let html = '';
        initialRecentLogs.forEach(l => {
            const isSent = l.status === 'sent';
            const name = l.crew_name || (l.reminder_type && l.reminder_type.indexOf('GROUP') !== -1 ? 'WhatsApp Group Rig' : 'Personil');
            const pos = l.position ? `(${l.position})` : '';
            const jam = l.sent_at ? l.sent_at.substring(11, 19) : '';
            html += `
                <div class="p-2.5 rounded-xl bg-slate-950/80 border border-slate-800/80 hover:border-slate-700 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 truncate">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                            <span class="font-semibold text-white text-xs truncate">${escapeHtml(name)} <span class="text-[10px] text-slate-400 font-normal">${escapeHtml(pos)}</span></span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold ${isSent ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'}">
                            ${isSent ? '✅ TERKIRIM' : '❌ GAGAL'}
                        </span>
                    </div>
                    <div class="mt-1 flex items-center justify-between text-[10px] text-slate-400">
                        <span class="font-mono text-slate-300">+${escapeHtml(l.target_phone)}</span>
                        <span class="text-slate-500 font-mono">${jam} WIB</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.addEventListener('DOMContentLoaded', () => {
        checkGatewayLiveStatus();
        setInterval(checkGatewayLiveStatus, 8000);
        updateMessagePreview();

        pollQueueData();
        setInterval(pollQueueData, 2500);
    });
</script>
