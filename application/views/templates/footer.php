</main>

<footer class="mt-auto py-4 px-6 border-t text-center text-xs transition-colors
    border-zinc-200 dark:border-zinc-800
    bg-white/60 dark:bg-zinc-900/60
    text-zinc-500 dark:text-zinc-400">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 max-w-7xl mx-auto">
        <div>
            &copy; <?= date('Y') ?> <strong class="font-medium text-zinc-700 dark:text-zinc-200">PT. Besmindo Materi Sewatama</strong> &bull; Rig Crew Meeting Reminder &amp; Attendance System
        </div>
        <div class="flex items-center space-x-3 text-[11px]">
            <a href="https://besmindoms.com/id/" target="_blank" class="text-sky-600 dark:text-sky-400 hover:underline inline-flex items-center space-x-1">
                <i class="fa-solid fa-globe"></i>
                <span>besmindoms.com</span>
            </a>
            <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
            <span class="text-zinc-400 dark:text-zinc-500">CI 3.1 &bull; PHP <?= phpversion() ?></span>
        </div>
    </div>
</footer>

</div>

<script>
    const BASE_URL = '<?= base_url() ?>';

    // Theme Switcher Logic
    function isDark() {
        return document.documentElement.classList.contains('dark');
    }

    function syncThemeIcons() {
        const sun = document.getElementById('themeIconSun');
        const moon = document.getElementById('themeIconMoon');
        if (!sun || !moon) return;
        if (isDark()) {
            sun.classList.remove('hidden');
            moon.classList.add('hidden');
        } else {
            sun.classList.add('hidden');
            moon.classList.remove('hidden');
        }
    }

    function toggleTheme() {
        if (isDark()) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('besmindo_theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('besmindo_theme', 'dark');
        }
        syncThemeIcons();
    }

    // Run icon sync immediately
    syncThemeIcons();
    document.addEventListener('DOMContentLoaded', syncThemeIcons);

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        sidebar.classList.toggle('-translate-x-full');
        backdrop.classList.toggle('hidden');
    }

    setInterval(() => {
        const el = document.getElementById('liveClock');
        if (el) {
            const now = new Date();
            el.innerText = now.toLocaleTimeString('id-ID', { hour12: false });
        }
    }, 1000);

    function triggerBackgroundReminders() {
        const dark = isDark();
        Swal.fire({
            title: 'Memeriksa Jadwal Meeting...',
            text: 'Sistem sedang mengevaluasi reminder H-1, H-1 Jam, dan H-15 Menit untuk semua crew...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            background: dark ? '#18181b' : '#ffffff',
            color: dark ? '#fafafa' : '#09090b',
            didOpen: () => {
                Swal.showLoading();
                fetch('<?= base_url('api/check_reminders') ?>')
                    .then(r => r.json())
                    .then(res => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reminder Engine Selesai',
                            html: `<div class="text-left text-sm space-y-1">
                                <p><strong>Status:</strong> ${res.message}</p>
                                <p><strong>Pesan Diproses:</strong> ${res.processed || 0}</p>
                                <p class="text-xs text-zinc-400 mt-2">${res.timestamp || ''}</p>
                            </div>`,
                            confirmButtonColor: '#0284c7',
                            background: isDark() ? '#18181b' : '#ffffff',
                            color: isDark() ? '#fafafa' : '#09090b'
                        });
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Trigger Reminder',
                            text: err.message,
                            confirmButtonColor: '#ef4444',
                            background: isDark() ? '#18181b' : '#ffffff',
                            color: isDark() ? '#fafafa' : '#09090b'
                        });
                    });
            }
        });
    }
</script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>

