<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - Sistem Reminder Meeting Crew Rig</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('besmindo_theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans min-h-screen flex items-center justify-center p-4 antialiased transition-colors">
    <div class="max-w-xl w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl overflow-hidden relative">
        <!-- Theme Switcher floating top right -->
        <div class="absolute top-4 right-4 z-10">
            <button type="button" onclick="toggleTheme()" class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-semibold shadow-xs transition" title="Ganti Tema">
                <i class="fa-solid fa-moon hidden dark:inline"></i>
                <i class="fa-solid fa-sun inline dark:hidden text-amber-500"></i>
            </button>
        </div>

        <div class="p-6 text-center border-b border-zinc-100 dark:border-zinc-800">
            <div class="flex items-center justify-center mb-3">
                <!-- Light mode logo -->
                <img src="assets/images/logo_besmindo_light.png" alt="PT Besmindo Materi Sewatama" class="h-16 w-auto object-contain dark:hidden">
                <!-- Dark mode logo -->
                <img src="assets/images/logo_besmindo.png" alt="PT Besmindo Materi Sewatama" class="h-12 w-auto object-contain hidden dark:block">
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">BESMINDO REMINDER</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">Sistem Reminder &amp; Monitoring Kehadiran Meeting Crew Rig</p>
        </div>

        <div class="p-6 space-y-6">
            <div class="space-y-3">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Pemeriksaan Lingkungan Database (PHP <?= phpversion() ?>)</h3>
                
                <div class="bg-zinc-50 dark:bg-zinc-950/60 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400 text-xs"><i class="fa-solid fa-server w-5 text-zinc-400"></i> Host / Port</span>
                        <span class="font-mono text-zinc-900 dark:text-zinc-100 text-xs"><?= htmlspecialchars($config['hostname']) ?>:<?= htmlspecialchars(isset($config['port']) ? $config['port'] : 3306) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400 text-xs"><i class="fa-solid fa-database w-5 text-zinc-400"></i> Database Target</span>
                        <span class="font-mono text-sky-600 dark:text-sky-400 font-semibold text-xs"><?= htmlspecialchars($config['database']) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400 text-xs"><i class="fa-solid fa-circle-check w-5 text-zinc-400"></i> Status Koneksi Server</span>
                        <?php if ($status['can_connect_server']): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-check mr-1"></i> Terhubung
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                <i class="fa-solid fa-xmark mr-1"></i> Gagal Konek
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400 text-xs"><i class="fa-solid fa-table-list w-5 text-zinc-400"></i> Status Tabel &amp; Data</span>
                        <?php if ($status['tables_created']): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                <i class="fa-solid fa-check mr-1"></i> Tabel Siap
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                <i class="fa-solid fa-clock mr-1"></i> Belum Diinstal
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($status['message'])): ?>
                    <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-xs text-rose-600 dark:text-rose-400 flex items-start space-x-2">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-rose-500 flex-shrink-0"></i>
                        <span><?= htmlspecialchars($status['message']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-950/60 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-600 dark:text-zinc-300 space-y-2">
                <div class="font-semibold text-sky-600 dark:text-sky-400 flex items-center">
                    <i class="fa-solid fa-key mr-1.5"></i> Akun Login Default Manager:
                </div>
                <div class="grid grid-cols-2 gap-2 text-zinc-500 dark:text-zinc-400 font-mono text-xs">
                    <div>Username: <strong class="text-zinc-900 dark:text-zinc-100">admin</strong></div>
                    <div>Password: <strong class="text-zinc-900 dark:text-zinc-100">admin123</strong></div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="button" id="btnInstall" onclick="runInstall()" 
                    class="flex-1 bg-zinc-900 dark:bg-zinc-50 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 font-semibold py-2.5 px-4 rounded-lg shadow-xs transition flex items-center justify-center space-x-2 text-xs">
                    <i class="fa-solid fa-bolt"></i>
                    <span><?= $status['tables_created'] ? 'Reset & Inisialisasi Ulang Data' : 'Inisialisasi Database Sekarang' ?></span>
                </button>

                <?php if ($status['tables_created']): ?>
                    <a href="<?= base_url('auth/login') ?>" 
                        class="bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold py-2.5 px-5 rounded-lg border border-zinc-200 dark:border-zinc-700 text-xs transition flex items-center justify-center space-x-2">
                        <span>Buka Login</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-zinc-50 dark:bg-zinc-950/80 px-6 py-3 border-t border-zinc-100 dark:border-zinc-800 text-center text-xs text-zinc-400 dark:text-zinc-500">
            PT Besmindo Oilfield Operations &copy; <?= date('Y') ?> &bull; CodeIgniter 3.1 &bull; PHP 7.4+ Compatible
        </div>
    </div>

    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('besmindo_theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('besmindo_theme', 'dark');
            }
        }

        function runInstall() {
            const btn = document.getElementById('btnInstall');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Sedang Menginstal Database...';

            const isDark = document.documentElement.classList.contains('dark');

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
                        background: isDark ? '#18181b' : '#ffffff',
                        color: isDark ? '#f4f4f5' : '#18181b'
                    }).then(() => {
                        window.location.href = '<?= base_url('auth/login') ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Setup Database',
                        text: data.message,
                        confirmButtonColor: '#ef4444',
                        background: isDark ? '#18181b' : '#ffffff',
                        color: isDark ? '#f4f4f5' : '#18181b'
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
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b'
                });
            });
        }
    </script>
</body>
</html>
