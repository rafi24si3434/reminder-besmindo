<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-gamepad text-2xl text-amber-500"></i>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Microsoft Teams Attendance Simulator & CSV Sync</h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Uji coba simulasi absensi Microsoft Teams atau unggah file laporan kehadiran CSV resmi</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= base_url('attendance/live' . ($meeting ? '/' . $meeting['id'] : '')) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-xs transition">
                <i class="fa-solid fa-desktop"></i>
                <span>Buka Live Monitor Absensi</span>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs">
        <form method="GET" action="<?= base_url('attendance/simulator') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <label class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Pilih Meeting Target:</label>
                <?php $this->load->view('components/meeting_switcher', array(
                    'meetings'        => $meetings,
                    'current_meeting' => $meeting,
                    'target_route'    => 'attendance/simulator?meeting_id=',
                    'mode'            => 'query_param'
                )); ?>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-7 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                        <i class="fa-solid fa-user-gear text-sky-600 dark:text-sky-400"></i>
                        <span>Simulasi Aksi Crew Join Teams</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Klik tombol untuk menyimulasikan personil bergabung on-time atau terlambat</p>
                </div>
            </div>

            <?php if ($meeting && !empty($attendances)): ?>
                <div class="space-y-2.5 max-h-[480px] overflow-y-auto pr-1 scrollbar-thin">
                    <?php foreach ($attendances as $att): ?>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-lg flex items-center justify-between gap-3">
                            <div class="truncate">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100 text-xs truncate"><?= htmlspecialchars($att['crew_name']) ?></div>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate"><?= htmlspecialchars($att['position']) ?> &bull; Status: <strong class="text-sky-600 dark:text-sky-400"><?= $att['status'] ?></strong></div>
                            </div>

                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <button type="button" onclick="runSim(<?= $att['id'] ?>, 'on_time')" 
                                    class="px-2.5 py-1.5 rounded-md bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[11px] font-semibold border border-emerald-500/20 transition flex items-center space-x-1">
                                    <i class="fa-solid fa-check text-xs"></i>
                                    <span>Hadir Tepat</span>
                                </button>
                                <button type="button" onclick="runSim(<?= $att['id'] ?>, 'late')" 
                                    class="px-2.5 py-1.5 rounded-md bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-[11px] font-semibold border border-amber-500/20 transition flex items-center space-x-1">
                                    <i class="fa-solid fa-clock text-xs"></i>
                                    <span>Terlambat</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-10 text-xs text-zinc-500 dark:text-zinc-400">
                    Pilih jadwal meeting di atas.
                </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                    <i class="fa-solid fa-file-csv text-emerald-600 dark:text-emerald-400"></i>
                    <span>Import File Teams Attendance (.CSV)</span>
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Unggah file laporan absensi yang di-download langsung dari Microsoft Teams</p>
            </div>

            <form action="<?= base_url('attendance/import_csv') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="meeting_id" value="<?= $meeting['id'] ?? '' ?>">

                <div class="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-sky-500 dark:hover:border-sky-500 rounded-xl p-6 text-center cursor-pointer transition bg-zinc-50/50 dark:bg-zinc-950/40">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-sky-600 dark:text-sky-400 mb-2 block"></i>
                    <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Pilih File CSV Kehadiran Teams</p>
                    <p class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Format standard Microsoft Teams Attendance Report</p>
                    <input type="file" name="teams_csv" accept=".csv" required class="w-full mt-3 text-xs text-zinc-600 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-900 dark:file:bg-zinc-100 file:text-white dark:file:text-zinc-900 hover:file:bg-zinc-800 dark:hover:file:bg-zinc-200 cursor-pointer">
                </div>

                <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-600 dark:text-zinc-300 space-y-1">
                    <span class="font-semibold text-sky-600 dark:text-sky-400 block"><i class="fa-solid fa-circle-info mr-1"></i> Format Kolom CSV:</span>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono">Full Name, Join Time, Leave Time, Duration</p>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-lg bg-zinc-900 dark:bg-zinc-50 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 font-semibold text-xs shadow-xs transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-upload"></i>
                    <span>Proses &amp; Sinkronisasi Kehadiran</span>
                </button>
            </form>
        </div>

    </div>

</div>

<script>
    function runSim(attId, type) {
        const isDark = document.documentElement.classList.contains('dark');
        fetch('<?= base_url('attendance/simulate_join') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `attendance_id=${attId}&join_type=${type}`
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Simulasi Terpasang',
                    text: res.message,
                    timer: 1000,
                    showConfirmButton: false,
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b'
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }
</script>
