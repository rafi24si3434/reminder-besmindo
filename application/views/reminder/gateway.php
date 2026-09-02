<div class="space-y-6">

    <!-- Header & Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-tower-broadcast text-2xl text-emerald-400"></i>
                <h1 class="text-2xl font-black text-white tracking-tight">Pusat Layanan WhatsApp Gateway (Fonnte)</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">Integrasi resmi Fonnte API untuk pengiriman broadcast & undangan meeting dengan proteksi anti-ban</p>
        </div>

        <!-- Quick Navigation Tab Bar -->
        <div class="inline-flex p-1 bg-slate-900 border border-slate-800 rounded-xl shadow-lg">
            <a href="<?= base_url('reminder') ?>" class="px-3.5 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Broadcast</span>
            </a>
            <a href="<?= base_url('reminder/gateway') ?>" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold shadow transition flex items-center space-x-1.5">
                <i class="fa-solid fa-tower-broadcast"></i>
                <span>Status Gateway</span>
            </a>
            <a href="<?= base_url('reminder/log') ?>" class="px-3.5 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                <span>Log Outbox</span>
            </a>
        </div>
    </div>

    <!-- Active Information Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-emerald-950/40 to-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-bold text-white">Fonnte WhatsApp Cloud Engine</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        ANTI-BAN BATCH ENGINE
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    Sistem menggunakan endpoint resmi <strong class="text-emerald-400 font-mono">https://api.fonnte.com/send</strong> dengan jeda otomatis (delay) per pesan agar nomor Anda tetap aman.
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2 flex-shrink-0">
            <a href="https://fonnte.com" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square text-sky-400"></i>
                <span>Buka Dashboard Fonnte</span>
            </a>
            <a href="<?= base_url('reminder') ?>" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center space-x-1.5">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Ke Menu Broadcast</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Fonnte API Configuration -->
        <div class="lg:col-span-6 space-y-6">

            <!-- Fonnte Token Setup -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Konfigurasi Token Fonnte</h3>
                            <p class="text-[10px] text-slate-400">Masukkan Token Akun Fonnte Anda</p>
                        </div>
                    </div>
                    <div id="fonnteBadge">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping mr-1.5"></span> Memeriksa...
                        </span>
                    </div>
                </div>

                <form action="<?= base_url('reminder/save_gateway_settings') ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="wa_gateway_provider" value="FONNTE">
                    <input type="hidden" name="wa_api_url" value="https://api.fonnte.com/send">

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                            Fonnte API Token <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="inputFonnteToken" name="wa_api_token" value="<?= htmlspecialchars($apiToken) ?>" required placeholder="Contoh: a1b2c3d4e5f6g7h8..." 
                                class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none pr-10">
                            <button type="button" onclick="toggleTokenVisibility()" class="absolute right-3 top-2.5 text-slate-400 hover:text-white text-xs">
                                <i class="fa-regular fa-eye" id="iconEye"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Dapatkan Token di dashboard Fonnte Anda: <strong>Dashboard &gt; Device &gt; Token</strong>.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                            Nomor Pengirim (Sender Number)
                        </label>
                        <input type="text" name="wa_sender_number" value="<?= htmlspecialchars($senderNumber) ?>" placeholder="Contoh: 085148410891" 
                            class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div class="pt-2 flex items-center space-x-3">
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Token Fonnte</span>
                        </button>
                        <button type="button" onclick="checkFonnteLiveStatus()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition flex items-center space-x-1.5">
                            <i class="fa-solid fa-rotate"></i>
                            <span>Cek Status</span>
                        </button>
                    </div>
                </form>

                <!-- Status Card -->
                <div id="fonnteStatusCard" class="p-4 bg-slate-950 border border-slate-800 rounded-xl space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Status Perangkat Fonnte:</span>
                        <span id="labelDeviceStatus" class="font-bold text-slate-300">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Nomor WhatsApp Terhubung:</span>
                        <span id="labelDeviceNumber" class="font-mono font-bold text-emerald-400">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Nama Perangkat:</span>
                        <span id="labelDeviceName" class="text-slate-300">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Sisa Kuota / Masa Aktif:</span>
                        <span id="labelDeviceQuota" class="text-slate-300">-</span>
                    </div>
                </div>

            </div>

        </div>

        <!-- Right Column: Test Message -->
        <div class="lg:col-span-6 space-y-6">

            <!-- Test Send Message Form -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Uji Coba Kirim Pesan via Fonnte</h3>
                        <p class="text-[10px] text-slate-400">Kirim pesan WhatsApp nyata untuk memastikan token Fonnte aktif</p>
                    </div>
                </div>

                <form id="formTestSend" onsubmit="submitTestSend(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Nomor HP Tujuan (Penerima) <span class="text-rose-400">*</span></label>
                        <input type="text" id="testPhone" name="target_phone" required placeholder="Contoh: 08123456789 atau 085148410891" 
                            class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Isi Pesan Uji Coba <span class="text-rose-400">*</span></label>
                        <textarea id="testMessage" name="message" rows="3" required class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-sky-500 focus:outline-none font-mono">Halo! Ini adalah pesan uji coba pengiriman broadcast via Fonnte API dari Sistem Reminder Meeting Rig PT Besmindo.</textarea>
                    </div>

                    <button type="submit" id="btnTestSend" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Pesan Uji Coba Sekarang</span>
                    </button>
                </form>
            </div>

            <!-- Fonnte Anti-Ban Information -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center space-x-2 text-emerald-400 font-bold">
                    <i class="fa-solid fa-shield-check text-base"></i>
                    <span>Fitur Anti-Ban Otomatis di Fonnte</span>
                </div>
                <ul class="text-slate-400 space-y-1.5 list-disc list-inside text-[11px] leading-relaxed">
                    <li>Sistem otomatis menyisipkan parameter <code class="text-emerald-300 bg-slate-800 px-1 rounded">delay</code> (jeda bertahap) di setiap pesan broadcast.</li>
                    <li>Pengiriman massal diproses secara antrian (queue) oleh server cloud Fonnte.</li>
                    <li>Nomor pengirim tidak melakukan spamming instan, sehingga terhindar dari pemblokiran otomatis WhatsApp.</li>
                </ul>
            </div>

        </div>

    </div>

</div>

<script>
    function toggleTokenVisibility() {
        const input = document.getElementById('inputFonnteToken');
        const icon = document.getElementById('iconEye');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function checkFonnteLiveStatus() {
        const badge = document.getElementById('fonnteBadge');
        badge.innerHTML = `
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping mr-1.5"></span> Memeriksa...
            </span>`;

        fetch('<?= base_url('reminder/fonnte_status') ?>')
            .then(r => r.json())
            .then(data => {
                const statusLabel = document.getElementById('labelDeviceStatus');
                const numberLabel = document.getElementById('labelDeviceNumber');
                const nameLabel = document.getElementById('labelDeviceName');
                const quotaLabel = document.getElementById('labelDeviceQuota');

                if (data.success && data.connected) {
                    badge.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            <i class="fa-solid fa-circle-check mr-1.5"></i> Fonnte Terhubung
                        </span>`;
                    statusLabel.innerHTML = '<span class="text-emerald-400 font-bold">Terhubung (Online)</span>';
                    numberLabel.textContent = data.deviceNumber || '<?= htmlspecialchars($senderNumber) ?>';
                    nameLabel.textContent = data.name || 'Perangkat Fonnte';
                    quotaLabel.textContent = (data.quota ? data.quota + ' pesan' : '-') + (data.expired ? ' (Exp: ' + data.expired + ')' : '');
                } else if (data.success && !data.connected) {
                    badge.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Perlu Scan di Fonnte
                        </span>`;
                    statusLabel.innerHTML = '<span class="text-amber-400 font-bold">Perangkat Belum Scan QR di Fonnte</span>';
                    numberLabel.textContent = data.deviceNumber || '-';
                    nameLabel.textContent = data.name || '-';
                    quotaLabel.textContent = data.quota || '-';
                } else {
                    badge.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                            <i class="fa-solid fa-circle-xmark mr-1.5"></i> Token Tidak Valid
                        </span>`;
                    statusLabel.innerHTML = `<span class="text-rose-400 font-bold">${data.message || 'Token Salah'}</span>`;
                    numberLabel.textContent = '-';
                    nameLabel.textContent = '-';
                    quotaLabel.textContent = '-';
                }
            })
            .catch(err => {
                badge.innerHTML = `
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        <i class="fa-solid fa-circle-xmark mr-1.5"></i> Gagal Cek Fonnte
                    </span>`;
            });
    }

    function submitTestSend(e) {
        e.preventDefault();
        const btn = document.getElementById('btnTestSend');
        const phone = document.getElementById('testPhone').value;
        const msg = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Mengirim via Fonnte...';

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
                    title: 'Pesan Terkirim via Fonnte!',
                    text: res.message,
                    confirmButtonColor: '#059669',
                    background: '#1e293b',
                    color: '#f8fafc'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Kirim via Fonnte',
                    text: res.message,
                    confirmButtonColor: '#ef4444',
                    background: '#1e293b',
                    color: '#f8fafc'
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
                background: '#1e293b',
                color: '#f8fafc'
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        checkFonnteLiveStatus();
    });
</script>
