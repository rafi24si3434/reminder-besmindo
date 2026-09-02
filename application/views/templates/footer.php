</main>

<footer class="mt-auto py-4 px-6 border-t border-slate-800 bg-slate-900/50 text-center text-xs text-slate-500">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 max-w-7xl mx-auto">
        <div>
            &copy; <?= date('Y') ?> <strong class="text-slate-400">PT Besmindo Oilfield Operations</strong> &bull; Rig Crew Meeting Reminder & Attendance System
        </div>
        <div class="flex items-center space-x-4 text-[11px]">
            <span class="text-slate-400"><i class="fa-solid fa-code-branch text-sky-400 mr-1"></i> CodeIgniter 3.1 &bull; PHP <?= phpversion() ?></span>
            <a href="<?= base_url('install') ?>" class="text-sky-400 hover:underline">Auto-Installer</a>
        </div>
    </div>
</footer>

</div>

<script>
    const BASE_URL = '<?= base_url() ?>';

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
                fetch('<?= base_url('api/check_reminders') ?>')
                    .then(r => r.json())
                    .then(res => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reminder Engine Selesai',
                            html: `<div class="text-left text-sm space-y-1">
                                <p><strong>Status:</strong> ${res.message}</p>
                                <p><strong>Pesan Diproses:</strong> ${res.processed || 0}</p>
                                <p class="text-xs text-slate-400 mt-2">${res.timestamp || ''}</p>
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
</body>
</html>
