<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - Sistem Reminder Meeting Crew Rig</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        besmindo: {
                            50: '#f0f7ff',
                            500: '#0284c7',
                            800: '#075985',
                            900: '#0c4a6e',
                            dark: '#0f172a',
                            accent: '#f59e0b'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-besmindo-900 via-sky-900 to-slate-900 p-6 text-center border-b border-slate-700">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-500/20 text-sky-400 mb-3 border border-sky-500/30">
                <i class="fa-solid fa-oil-well text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">BESMINDO REMINDER</h1>
            <p class="text-sky-300 text-sm mt-1">Sistem Reminder & Monitoring Kehadiran Meeting Crew Rig</p>
        </div>

        <div class="p-6 space-y-6">
            <div class="space-y-3">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400">Pemeriksaan Lingkungan Database</h3>
                
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300"><i class="fa-solid fa-server w-5 text-slate-400"></i> Host / Port</span>
                        <span class="font-mono text-slate-200"><?= esc($config['hostname']) ?>:<?= esc($config['port'] ?? 3306) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300"><i class="fa-solid fa-database w-5 text-slate-400"></i> Database Target</span>
                        <span class="font-mono text-sky-400 font-semibold"><?= esc($config['database']) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300"><i class="fa-solid fa-circle-check w-5 text-slate-400"></i> Status Koneksi Server</span>
                        <?php if ($status['can_connect_server']): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <i class="fa-solid fa-check mr-1.5"></i> Terhubung
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                <i class="fa-solid fa-xmark mr-1.5"></i> Gagal Konek
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300"><i class="fa-solid fa-table-list w-5 text-slate-400"></i> Status Tabel & Data</span>
                        <?php if ($status['tables_created']): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <i class="fa-solid fa-check mr-1.5"></i> Tabel Siap
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                <i class="fa-solid fa-clock mr-1.5"></i> Belum Diinstal
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($status['message'])): ?>
                    <div class="p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-xl text-xs text-rose-300 flex items-start space-x-2">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-rose-400 flex-shrink-0"></i>
                        <span><?= esc($status['message']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-700/60 text-xs text-slate-300 space-y-2">
                <div class="font-semibold text-sky-400 flex items-center">
                    <i class="fa-solid fa-key mr-1.5"></i> Akun Login Default Manager:
                </div>
                <div class="grid grid-cols-2 gap-2 text-slate-400 font-mono">
                    <div>Username: <strong class="text-white">admin</strong></div>
                    <div>Password: <strong class="text-white">admin123</strong></div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="button" id="btnInstall" onclick="runInstall()" 
                    class="flex-1 bg-gradient-to-r from-sky-500 to-besmindo-500 hover:from-sky-600 hover:to-besmindo-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg transition duration-150 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-bolt"></i>
                    <span><?= $status['tables_created'] ? 'Reset & Inisialisasi Ulang Data' : 'Inisialisasi Database Sekarang' ?></span>
                </button>

                <?php if ($status['tables_created']): ?>
                    <a href="<?= base_url('auth/login') ?>" 
                        class="bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold py-3 px-5 rounded-xl text-center transition flex items-center justify-center space-x-2">
                        <span>Buka Login</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-slate-900/80 px-6 py-3 border-t border-slate-700 text-center text-xs text-slate-500">
            PT Besmindo Oilfield Operations &copy; <?= date('Y') ?> &bull; Rig Meeting & Attendance System
        </div>
    </div>

    <script>
        function runInstall() {
            const btn = document.getElementById('btnInstall');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Sedang Menginstal Database...';

            fetch('<?= base_url('install/run') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonText: 'Lanjut ke Login',
                        confirmButtonColor: '#0284c7',
                        background: '#1e293b',
                        color: '#f8fafc'
                    }).then(() => {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Setup Database',
                        text: data.message,
                        confirmButtonColor: '#ef4444',
                        background: '#1e293b',
                        color: '#f8fafc'
                    });
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Tidak dapat menghubungi server: ' + err.message,
                    confirmButtonColor: '#ef4444',
                    background: '#1e293b',
                    color: '#f8fafc'
                });
            });
        }
    </script>
</body>
</html>
