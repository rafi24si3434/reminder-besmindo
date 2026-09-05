<div class="space-y-6">

    <!-- Header & Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-tower-broadcast text-2xl text-emerald-600 dark:text-emerald-400"></i>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Pusat Layanan WhatsApp Gateway Mandiri</h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Koneksi WhatsApp Engine Mandiri (Node.js Baileys) untuk pengiriman broadcast & reminder resmi</p>
        </div>

        <!-- Quick Navigation Tab Bar -->
        <div class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-xs">
            <a href="<?= base_url('reminder') ?>" class="px-3 py-1.5 rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-white dark:hover:bg-zinc-800 text-xs font-medium transition flex items-center space-x-1.5">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Broadcast</span>
            </a>
            <a href="<?= base_url('reminder/gateway') ?>" class="px-3 py-1.5 rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-xs font-semibold shadow-xs transition flex items-center space-x-1.5">
                <i class="fa-solid fa-tower-broadcast text-emerald-600 dark:text-emerald-400"></i>
                <span>Status Gateway</span>
            </a>
            <a href="<?= base_url('reminder/log') ?>" class="px-3 py-1.5 rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-white dark:hover:bg-zinc-800 text-xs font-medium transition flex items-center space-x-1.5">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Log Outbox</span>
            </a>
        </div>
    </div>

    <!-- Active Information Banner -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl flex-shrink-0 border border-emerald-500/20">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">WhatsApp Gateway Mandiri (Server Internal)</span>
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        ANTI-BAN QUEUE ACTIVE
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    Berjalan mandiri di server lokal (<strong class="text-emerald-600 dark:text-emerald-400 font-mono">http://localhost:3000</strong>) dengan antrian pengiriman dan delay otomatis.
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2 flex-shrink-0">
            <a href="<?= base_url('reminder') ?>" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition flex items-center space-x-1.5">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Mulai Broadcast</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: QR Code & Live Connection Status -->
        <div class="lg:col-span-6 space-y-6">

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-5">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Otorisasi WhatsApp Pengirim</h3>
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400">Scan QR Code dengan WhatsApp nomor pengirim</p>
                        </div>
                    </div>
                    <div id="connectionBadge">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping mr-1.5"></span> Memeriksa...
                        </span>
                    </div>
                </div>

                <!-- QR Display & Connection States -->
                <div class="flex flex-col items-center justify-center p-6 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-200 dark:border-zinc-800 min-h-[300px]">
                    
                    <!-- Loading State -->
                    <div id="qrLoading" class="text-center space-y-3">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-sky-500"></i>
                        <p class="text-xs text-zinc-900 dark:text-zinc-100 font-semibold">Menghubungkan ke Service Gateway...</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Memeriksa status sambungan di port 3000</p>
                    </div>

                    <!-- QR Code Ready -->
                    <div id="qrContainer" class="hidden flex-col items-center space-y-4">
                        <div class="bg-white p-3 rounded-xl shadow-lg inline-block border border-zinc-200">
                            <img id="qrImage" src="" alt="WhatsApp QR Code" class="w-56 h-56">
                        </div>
                        <div class="text-xs text-zinc-600 dark:text-zinc-300 space-y-2 text-center">
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">Scan dengan WhatsApp di HP Pengirim</p>
                            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 text-left max-w-xs mx-auto shadow-xs">
                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider mb-1.5">Cara Tautkan Perangkat:</p>
                                <ol class="text-[11px] text-zinc-600 dark:text-zinc-300 list-decimal list-inside space-y-1">
                                    <li>Buka <strong>WhatsApp</strong> di HP Anda</li>
                                    <li>Ketuk menu <strong>⋮ (Titik Tiga) / Setelan</strong></li>
                                    <li>Pilih <strong>Perangkat Tertaut (Linked Devices)</strong></li>
                                    <li>Ketuk tombol <strong>Tautkan Perangkat</strong></li>
                                    <li>Arahkan kamera ke QR Code di atas</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Connected State -->
                    <div id="connectedContainer" class="hidden flex-col items-center space-y-4 text-center">
                        <div class="w-16 h-16 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl border border-emerald-500/20 shadow-sm">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">WhatsApp Gateway Terhubung & Aktif!</h4>
                            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-mono font-bold" id="connectedNumberLabel">Nomor Pengirim: <?= htmlspecialchars($senderNumber) ?></p>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 max-w-xs mt-1">
                                Sistem siap menyebarkan notifikasi dan undangan meeting secara otomatis ke nomor crew / grup rig.
                            </p>
                        </div>
                        <div class="pt-2 flex items-center space-x-2">
                            <a href="<?= base_url('reminder') ?>" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-xs transition">
                                <i class="fa-solid fa-paper-plane mr-1.5"></i> Masuk Menu Broadcast
                            </a>
                            <button type="button" onclick="logoutWhatsApp()" class="px-3.5 py-2 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-semibold border border-rose-500/20 transition">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Putuskan Sesi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Anti-Ban Live Metrics -->
                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800/80 pb-2.5">
                        <div class="flex items-center space-x-2 text-xs font-semibold text-zinc-900 dark:text-zinc-100">
                            <i class="fa-solid fa-shield-halved text-emerald-600 dark:text-emerald-400"></i>
                            <span>Proteksi Anti-Ban (Bebas Skors 5 Jam)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            100% AKTIF
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <div class="bg-white dark:bg-zinc-900 p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-800">
                            <span class="text-zinc-500 dark:text-zinc-400 block text-[10px]">Jeda Antar Pesan Pribadi</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold font-mono text-xs" id="metricDelay">15 – 35 Detik</span>
                        </div>
                        <div class="bg-white dark:bg-zinc-900 p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-800">
                            <span class="text-zinc-500 dark:text-zinc-400 block text-[10px]">Simulasi Ketik Manusia</span>
                            <span class="text-sky-600 dark:text-sky-400 font-semibold font-mono text-xs">3 – 6.5 Detik</span>
                        </div>
                        <div class="bg-white dark:bg-zinc-900 p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-800">
                            <span class="text-zinc-500 dark:text-zinc-400 block text-[10px]">Pesan Personal Jam Ini</span>
                            <span class="text-amber-600 dark:text-amber-400 font-semibold font-mono text-xs" id="metricHourly">0 / 25 Pesan</span>
                        </div>
                        <div class="bg-white dark:bg-zinc-900 p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-800">
                            <span class="text-zinc-500 dark:text-zinc-400 block text-[10px]">Antrian Berjalan Saat Ini</span>
                            <span class="text-zinc-900 dark:text-zinc-100 font-semibold font-mono text-xs" id="metricQueue">0 Pesan</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-zinc-500 dark:text-zinc-400 italic">
                        <i class="fa-solid fa-circle-info mr-1"></i> Setiap pesan menyertakan nama dan jabatan personil secara otomatis agar tidak dianggap spam oleh WhatsApp.
                    </p>
                </div>

                <!-- Settings Form -->
                <form action="<?= base_url('reminder/save_gateway_settings') ?>" method="POST" class="space-y-4 pt-2">
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">
                            Nomor WhatsApp Pengirim
                        </label>
                        <input type="text" name="wa_sender_number" value="<?= htmlspecialchars($senderNumber) ?>" placeholder="Contoh: 081234567890" 
                            class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">
                            Endpoint Service Gateway Mandiri
                        </label>
                        <input type="text" name="wa_api_url" value="<?= htmlspecialchars($apiUrl) ?>" placeholder="http://localhost:3000/send-message" 
                            class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:outline-none transition">
                    </div>

                    <div class="pt-1 flex items-center space-x-3">
                        <button type="submit" class="flex-1 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Pengaturan</span>
                        </button>
                        <button type="button" onclick="checkGatewayStatus()" class="px-4 py-2.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition flex items-center space-x-1.5">
                            <i class="fa-solid fa-rotate"></i>
                            <span>Refresh Status</span>
                        </button>
                    </div>
                </form>

            </div>

        </div>

        <!-- Right Column: Test Send & Guide -->
        <div class="lg:col-span-6 space-y-6">

            <!-- Test Send Message Form -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
                <div class="flex items-center space-x-2 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Uji Coba Kirim Pesan WhatsApp</h3>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400">Tes kirim pesan nyata melalui nomor pengirim yang sedang terhubung</p>
                    </div>
                </div>

                <form id="formTestSend" onsubmit="submitTestSend(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">Nomor HP Tujuan (Penerima) <span class="text-rose-500">*</span></label>
                        <input type="text" id="testPhone" name="target_phone" required placeholder="Contoh: 081234567890" 
                            class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none font-mono transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">Isi Pesan Uji Coba <span class="text-rose-500">*</span></label>
                        <textarea id="testMessage" name="message" rows="3" required class="w-full px-3.5 py-2 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none font-mono transition">Halo! Ini adalah pesan uji coba dari Sistem Reminder Meeting Rig PT. Besmindo Materi Sewatama via WhatsApp Gateway Mandiri.</textarea>
                    </div>

                    <button type="submit" id="btnTestSend" class="w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Pesan Uji Coba Sekarang</span>
                    </button>
                </form>
            </div>

            <!-- Service Control & How-to Guide -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-xs space-y-3 text-xs">
                <div class="flex items-center space-x-2 text-sky-600 dark:text-sky-400 font-bold">
                    <i class="fa-solid fa-terminal text-base"></i>
                    <span>Petunjuk Menjalankan Gateway Mandiri di Server / Laptop</span>
                </div>
                <p class="text-zinc-600 dark:text-zinc-400 text-[11px] leading-relaxed">
                    Jika service gateway belum aktif atau komputer baru saja dinyalakan ulang, jalankan perintah berikut di Command Prompt (cmd) / PowerShell:
                </p>
                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 space-y-1 font-mono text-[11px]">
                    <div class="text-zinc-400"># Masuk ke folder gateway</div>
                    <div class="text-emerald-600 dark:text-emerald-400">cd "c:\xampp\htdocs\Project Besmindo Reminder\whatsapp-gateway"</div>
                    <div class="text-zinc-400 mt-2"># Jalankan service</div>
                    <div class="text-sky-600 dark:text-sky-400">node server.js</div>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    let pollInterval = null;

    function checkGatewayStatus() {
        fetch('http://localhost:3000/status')
            .then(r => r.json())
            .then(data => {
                const qrLoading          = document.getElementById('qrLoading');
                const qrContainer        = document.getElementById('qrContainer');
                const connectedContainer = document.getElementById('connectedContainer');
                const qrImage            = document.getElementById('qrImage');
                const badge              = document.getElementById('connectionBadge');

                if (data.antiBan) {
                    const delayEl = document.getElementById('metricDelay');
                    const hourlyEl = document.getElementById('metricHourly');
                    const queueEl = document.getElementById('metricQueue');
                    if (delayEl) delayEl.textContent = `${data.antiBan.minDelaySec} – ${data.antiBan.maxDelaySec} Detik`;
                    if (hourlyEl) hourlyEl.textContent = `${data.antiBan.sentThisHour} / ${data.antiBan.maxPerHour} Pesan`;
                    if (queueEl) {
                        queueEl.innerHTML = data.antiBan.queueLength > 0 
                            ? `<span class="text-amber-500 font-bold animate-pulse">${data.antiBan.queueLength} Pesan (Proses)</span>` 
                            : `<span class="text-emerald-600 dark:text-emerald-400">0 Pesan (Kosong)</span>`;
                    }
                }

                if (data.connected) {
                    // TERHUBUNG
                    qrLoading.classList.add('hidden');
                    qrContainer.classList.add('hidden');
                    qrContainer.classList.remove('flex');
                    connectedContainer.classList.remove('hidden');
                    connectedContainer.classList.add('flex');

                    const numLabel = document.getElementById('connectedNumberLabel');
                    if (numLabel && data.senderNumber) {
                        let num = data.senderNumber.toString().replace(/[^0-9]/g, '');
                        if (num.startsWith('62')) num = '0' + num.substring(2);
                        numLabel.textContent = 'Nomor Pengirim Aktif: ' + num;
                    }

                    badge.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <i class="fa-solid fa-circle-check mr-1.5"></i> Terhubung (${data.senderNumber || '<?= htmlspecialchars($senderNumber) ?>'})
                        </span>`;
                } else if (data.qr) {
                    // QR CODE READY
                    qrLoading.classList.add('hidden');
                    connectedContainer.classList.add('hidden');
                    connectedContainer.classList.remove('flex');
                    qrContainer.classList.remove('hidden');
                    qrContainer.classList.add('flex');
                    qrImage.src = data.qr;
                    badge.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping mr-1.5"></span> Scan QR Sekarang
                        </span>`;
                } else {
                    // INITIALIZING
                    qrContainer.classList.add('hidden');
                    qrContainer.classList.remove('flex');
                    connectedContainer.classList.add('hidden');
                    connectedContainer.classList.remove('flex');
                    qrLoading.classList.remove('hidden');
                    qrLoading.innerHTML = `
                        <div class="text-center space-y-3">
                            <i class="fa-solid fa-spinner fa-spin text-3xl text-sky-500"></i>
                            <p class="text-xs text-zinc-900 dark:text-zinc-100 font-semibold">Menghubungkan ke Server WhatsApp...</p>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">QR Code akan muncul otomatis dalam beberapa detik.</p>
                        </div>`;
                    badge.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-ping mr-1.5"></span> Menginisialisasi...
                        </span>`;
                }
            })
            .catch(() => {
                const qrLoading = document.getElementById('qrLoading');
                const badge     = document.getElementById('connectionBadge');
                qrLoading.classList.remove('hidden');
                qrLoading.innerHTML = `
                    <div class="text-center space-y-3">
                        <i class="fa-solid fa-triangle-exclamation text-3xl text-rose-500"></i>
                        <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold">Service Gateway Tidak Aktif di Port 3000</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Jalankan perintah ini di Command Prompt:</p>
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg px-3 py-2 text-left max-w-xs mx-auto shadow-xs">
                            <code class="text-emerald-600 dark:text-emerald-400 text-[11px] font-mono block">cd "c:\\xampp\\htdocs\\Project Besmindo Reminder\\whatsapp-gateway"</code>
                            <code class="text-emerald-600 dark:text-emerald-400 text-[11px] font-mono block mt-1">node server.js</code>
                        </div>
                    </div>`;
                badge.innerHTML = `
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                        <i class="fa-solid fa-circle-xmark mr-1.5"></i> Service Offline
                    </span>`;
            });
    }

    function logoutWhatsApp() {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: 'Putuskan Sesi WhatsApp?',
            text: 'Sesi WhatsApp akan di-logout dan memerlukan scan QR ulang.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Putuskan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#e11d48',
            background: isDark ? '#18181b' : '#ffffff',
            color: isDark ? '#f4f4f5' : '#18181b'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('http://localhost:3000/logout', { method: 'POST' })
                    .then(r => r.json())
                    .then(res => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sesi Diputuskan',
                            text: res.message,
                            background: isDark ? '#18181b' : '#ffffff',
                            color: isDark ? '#f4f4f5' : '#18181b',
                            confirmButtonColor: '#059669'
                        });
                        checkGatewayStatus();
                    });
            }
        });
    }

    function submitTestSend(e) {
        e.preventDefault();
        const isDark = document.documentElement.classList.contains('dark');
        const btn = document.getElementById('btnTestSend');
        const phone = document.getElementById('testPhone').value;
        const msg = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Mengirim via Gateway Mandiri...';

        fetch('<?= base_url('reminder/test_send') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `target_phone=${encodeURIComponent(phone)}&message=${encodeURIComponent(msg)}`
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Kirim Pesan Uji Coba Sekarang';

            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pesan Terkirim!',
                    text: res.message,
                    confirmButtonColor: '#059669',
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Kirim Pesan',
                    text: res.message,
                    confirmButtonColor: '#ef4444',
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b'
                });
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Kirim Pesan Uji Coba Sekarang';
            Swal.fire({
                icon: 'error',
                title: 'Error Jaringan',
                text: err.message,
                confirmButtonColor: '#ef4444',
                background: isDark ? '#18181b' : '#ffffff',
                color: isDark ? '#f4f4f5' : '#18181b'
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        checkGatewayStatus();
        pollInterval = setInterval(checkGatewayStatus, 1500);
    });
</script>
