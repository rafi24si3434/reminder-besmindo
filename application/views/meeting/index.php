<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2.5">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Jadwal Meeting Crew Rig</h1>
                <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 font-mono">
                    <?= count($meetings) ?> Total
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Kelola jadwal meeting rutin mingguan dan meeting ad-hoc untuk seluruh rig</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="<?= base_url('meeting/create') ?>"
                class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition
                    bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                <i class="fa-solid fa-calendar-plus text-xs"></i>
                <span>Buat Jadwal Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="rounded-xl border p-3.5 shadow-sm transition-colors
        bg-white dark:bg-zinc-900
        border-zinc-200 dark:border-zinc-800">
        <form method="GET" action="<?= base_url('meeting') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                <div>
                    <select name="rig_id" onchange="this.form.submit()"
                        class="text-xs rounded-lg px-3 py-1.5 border transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        <option value="">-- Semua Unit Rig --</option>
                        <?php foreach ($rigs as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($selectedRig == $r['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()"
                        class="text-xs rounded-lg px-3 py-1.5 border transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        <option value="">-- Semua Status --</option>
                        <option value="scheduled" <?= ($selectedStatus === 'scheduled') ? 'selected' : '' ?>>Terjadwal</option>
                        <option value="in_progress" <?= ($selectedStatus === 'in_progress') ? 'selected' : '' ?>>Sedang Berlangsung</option>
                        <option value="completed" <?= ($selectedStatus === 'completed') ? 'selected' : '' ?>>Selesai</option>
                        <option value="cancelled" <?= ($selectedStatus === 'cancelled') ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date" value="<?= htmlspecialchars($selectedDate ?: '') ?>" onchange="this.form.submit()"
                        class="text-xs rounded-lg px-3 py-1.5 border transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>
                <?php if ($selectedRig || $selectedStatus || $selectedDate): ?>
                    <a href="<?= base_url('meeting') ?>" class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 flex items-center space-x-1">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        <span>Reset Filter</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Meetings List -->
    <div class="space-y-3">
        <?php if (empty($meetings)): ?>
            <div class="rounded-xl border p-12 text-center transition-colors
                bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
                <i class="fa-regular fa-calendar-xmark text-4xl mb-3 text-zinc-400 block"></i>
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Belum ada meeting yang sesuai dengan filter.</p>
                <a href="<?= base_url('meeting/create') ?>"
                    class="mt-4 inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition
                        bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Buat Jadwal Baru</span>
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($meetings as $m): ?>
                <div class="rounded-xl border p-5 shadow-sm transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4
                    bg-white dark:bg-zinc-900
                    border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700">
                    <div class="space-y-2 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <?php if (!empty($m['is_joint'])): ?>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 flex items-center">
                                    <i class="fa-solid fa-layer-group mr-1.5 text-[10px]"></i> JOINT: <?= htmlspecialchars($m['joint_summary'] ?: 'Rapat Gabungan Multi-Rig') ?>
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                    <i class="fa-solid fa-tower-observation mr-1 text-[10px]"></i> <?= htmlspecialchars($m['rig_name']) ?>
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    Grup <?= htmlspecialchars($m['group_target'] ?: 'A') ?>
                                </span>
                            <?php endif; ?>

                            <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                <i class="<?= (!empty($m['session_time']) && $m['session_time'] === 'MALAM') ? 'fa-solid fa-moon text-sky-400' : 'fa-solid fa-sun text-amber-500' ?> mr-1 text-[10px]"></i>
                                <?= (!empty($m['session_time']) && $m['session_time'] === 'MALAM') ? 'Sesi Malam' : 'Sesi Siang' ?>
                            </span>

                            <?php if ($m['status'] === 'in_progress'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1.5"></span> SEDANG BERLANGSUNG
                                </span>
                            <?php elseif ($m['status'] === 'completed'): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">SELESAI</span>
                            <?php elseif ($m['status'] === 'cancelled'): ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-400">DIBATALKAN</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">TERJADWAL</span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <a href="<?= base_url('meeting/detail/' . $m['id']) ?>" class="text-sm sm:text-base font-semibold text-zinc-900 dark:text-zinc-100 hover:text-sky-600 dark:hover:text-sky-400 transition">
                                <?= htmlspecialchars($m['title']) ?>
                            </a>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-1 mt-0.5"><?= htmlspecialchars($m['topic']) ?></p>
                        </div>

                        <div class="flex flex-wrap items-center text-xs text-zinc-500 dark:text-zinc-400 gap-x-4 gap-y-1 pt-0.5">
                            <span><i class="fa-regular fa-calendar text-zinc-400 mr-1.5"></i> <?= date('d M Y', strtotime($m['meeting_date'])) ?></span>
                            <span><i class="fa-regular fa-clock text-zinc-400 mr-1.5"></i> <?= substr($m['start_time'], 0, 5) ?> - <?= substr($m['end_time'], 0, 5) ?> WIB</span>
                            <span><i class="fa-solid fa-user-tie text-zinc-400 mr-1.5"></i> PJ: <strong class="text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars($m['pj_name'] ?: '-') ?></strong></span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-zinc-200 dark:border-zinc-800">
                        <a href="<?= htmlspecialchars($m['teams_link']) ?>" target="_blank"
                            class="px-3 py-1.5 rounded-lg border text-xs font-medium flex items-center space-x-1.5 transition
                                bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border-indigo-200
                                dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 dark:text-indigo-300 dark:border-indigo-800">
                            <i class="fa-brands fa-microsoft text-xs"></i>
                            <span>Teams</span>
                        </a>

                        <a href="<?= base_url('attendance/live/' . $m['id']) ?>"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium text-white shadow-sm flex items-center space-x-1.5 transition
                                bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                            <i class="fa-solid fa-video text-xs"></i>
                            <span>Live</span>
                        </a>

                        <a href="<?= base_url('reminder/broadcast/' . $m['id']) ?>"
                            class="px-3 py-1.5 rounded-lg border text-xs font-medium flex items-center space-x-1.5 transition
                                bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200
                                dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 dark:text-emerald-300 dark:border-emerald-800">
                            <i class="fa-brands fa-whatsapp text-xs text-emerald-600 dark:text-emerald-400"></i>
                            <span class="hidden sm:inline">Kirim WA</span>
                        </a>

                        <div class="inline-flex items-center space-x-1 pl-1">
                            <button type="button" onclick="openQuickReschedule(<?= $m['id'] ?>, '<?= htmlspecialchars($m['title']) ?>', '<?= $m['meeting_date'] ?>', '<?= substr($m['start_time'], 0, 5) ?>', '<?= substr($m['end_time'], 0, 5) ?>')" 
                                class="p-1.5 text-zinc-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition" title="Ubah Jadwal Cepat">
                                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                            </button>
                            <a href="<?= base_url('meeting/edit/' . $m['id']) ?>"
                                class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition">
                                <i class="fa-regular fa-pen-to-square text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete(<?= $m['id'] ?>, '<?= htmlspecialchars($m['title']) ?>')"
                                class="p-1.5 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-md transition">
                                <i class="fa-regular fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- Reschedule Modal -->
<div id="rescheduleModal" class="fixed inset-0 z-50 bg-zinc-950/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="rounded-xl border max-w-md w-full p-6 shadow-xl space-y-4 transition-colors
        bg-white dark:bg-zinc-900
        border-zinc-200 dark:border-zinc-800">
        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
                <span>Ubah Jadwal Cepat (Reschedule)</span>
            </h3>
            <button type="button" onclick="closeQuickReschedule()" class="p-1 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="formReschedule" onsubmit="submitQuickReschedule(event)" class="space-y-4">
            <input type="hidden" id="modalMeetingId" name="meeting_id">
            
            <div>
                <p id="modalMeetingTitle" class="text-xs font-medium text-sky-600 dark:text-sky-400 truncate"></p>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Baru</label>
                <input type="date" id="modalDate" name="meeting_date" required
                    class="w-full px-3 py-1.5 rounded-lg border text-xs
                        bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                        text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/30 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Jam Mulai</label>
                    <input type="time" id="modalStartTime" name="start_time" required
                        class="w-full px-3 py-1.5 rounded-lg border text-xs
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/30 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Jam Selesai</label>
                    <input type="time" id="modalEndTime" name="end_time" required
                        class="w-full px-3 py-1.5 rounded-lg border text-xs
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/30 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeQuickReschedule()"
                    class="px-3.5 py-1.5 rounded-lg border text-xs font-medium transition
                        bg-white hover:bg-zinc-100 text-zinc-700 border-zinc-200
                        dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 dark:border-zinc-700">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-1.5 rounded-lg text-xs font-medium text-white shadow-sm transition
                        bg-amber-600 hover:bg-amber-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmDelete(id, title) {
        confirmAction('Hapus Jadwal Meeting?', `Apakah Anda yakin ingin menghapus meeting "${title}"? Data absensi dan log reminder terkait akan dihapus.`, 'Ya, Hapus')
        .then(res => {
            if (res.isConfirmed) {
                window.location.href = `<?= base_url('meeting/delete/') ?>/${id}`;
            }
        });
    }

    function openQuickReschedule(id, title, date, startTime, endTime) {
        document.getElementById('modalMeetingId').value = id;
        document.getElementById('modalMeetingTitle').innerText = title;
        document.getElementById('modalDate').value = date;
        document.getElementById('modalStartTime').value = startTime;
        document.getElementById('modalEndTime').value = endTime;

        const modal = document.getElementById('rescheduleModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeQuickReschedule() {
        const modal = document.getElementById('rescheduleModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitQuickReschedule(e) {
        e.preventDefault();
        const form = document.getElementById('formReschedule');
        const formData = new FormData(form);
        const isDark = document.documentElement.classList.contains('dark');

        fetch('<?= base_url('meeting/quick_reschedule') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Jadwal Diperbarui!',
                    text: res.message,
                    confirmButtonColor: '#0284c7',
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#fafafa' : '#09090b'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Reschedule',
                    text: res.message,
                    confirmButtonColor: '#ef4444',
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#fafafa' : '#09090b'
                });
            }
        });
    }
</script>

