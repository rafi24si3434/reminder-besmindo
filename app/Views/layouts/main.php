<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - Besmindo Rig Meeting Reminder</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        besmindo: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            800: '#0c4a6e',
                            900: '#082f49',
                            dark: '#0f172a',
                            gold: '#f59e0b'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
</head>
<body class="h-full text-slate-100 flex flex-col antialiased selection:bg-sky-500 selection:text-white bg-slate-950">

    <!-- Sidebar Component -->
    <?= $this->include('layouts/sidebar') ?>

    <!-- Mobile Backdrop -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 z-30 bg-slate-950/70 backdrop-blur-sm hidden lg:hidden"></div>

    <!-- Main Content Area -->
    <div class="lg:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Top Navbar -->
        <?= $this->include('layouts/navbar') ?>

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Notification Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center justify-between animate-fadeIn">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                        <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400/60 hover:text-emerald-300">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 flex items-center justify-between animate-fadeIn">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg"></i>
                        <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-400/60 hover:text-rose-300">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer -->
        <?= $this->include('layouts/footer') ?>
    </div>

    <!-- Global Scripts -->
    <script>
        const BASE_URL = '<?= base_url() ?>';

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        // Live Clock
        setInterval(() => {
            const el = document.getElementById('liveClock');
            if (el) {
                const now = new Date();
                el.innerText = now.toLocaleTimeString('id-ID', { hour12: false });
            }
        }, 1000);

        // Global Scan Reminders Trigger
        function triggerBackgroundReminders() {
            Swal.fire({
                title: 'Memeriksa Jadwal Meeting...',
                text: 'Sistem sedang mengevaluasi reminder H-1, H-1 Jam, dan H-15 Menit untuk semua crew...',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#1e293b',
                color: '#f8fafc',
                didOpen: () => {
                    Swal.showLoading();
                    fetch('<?= base_url('api/check-reminders') ?>')
                        .then(r => r.json())
                        .then(res => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Reminder Engine Selesai',
                                html: `<div class="text-left text-sm space-y-1">
                                    <p><strong>Status:</strong> ${res.message}</p>
                                    <p><strong>Pesan Diproses:</strong> ${res.processed ?? 0}</p>
                                    <p class="text-xs text-slate-400 mt-2">${res.timestamp ?? ''}</p>
                                </div>`,
                                confirmButtonColor: '#0284c7',
                                background: '#1e293b',
                                color: '#f8fafc'
                            });
                        })
                        .catch(err => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Trigger Reminder',
                                text: err.message,
                                confirmButtonColor: '#ef4444',
                                background: '#1e293b',
                                color: '#f8fafc'
                            });
                        });
                }
            });
        }
    </script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
