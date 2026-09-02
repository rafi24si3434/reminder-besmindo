<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Jadwal Meeting Crew Rig</h1>
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">
                    <?= count($meetings) ?> Total
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Kelola jadwal meeting rutin mingguan dan meeting ad-hoc untuk seluruh rig</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= base_url('meeting/create') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-lg shadow-sky-600/30 transition">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Buat Jadwal Baru</span>
            </a>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg">
        <form method="GET" action="<?= base_url('meeting') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div>
                    <select name="rig_id" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">-- Semua Unit Rig --</option>
                        <?php foreach ($rigs as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($selectedRig == $r['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">-- Semua Status --</option>
                        <option value="scheduled" <?= ($selectedStatus === 'scheduled') ? 'selected' : '' ?>>Terjadwal</option>
                        <option value="in_progress" <?= ($selectedStatus === 'in_progress') ? 'selected' : '' ?>>Sedang Berlangsung</option>
                        <option value="completed" <?= ($selectedStatus === 'completed') ? 'selected' : '' ?>>Selesai</option>
                        <option value="cancelled" <?= ($selectedStatus === 'cancelled') ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date" value="<?= htmlspecialchars($selectedDate ?: '') ?>" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <?php if ($selectedRig || $selectedStatus || $selectedDate): ?>
                    <a href="<?= base_url('meeting') ?>" class="text-xs text-slate-400 hover:text-white flex items-center space-x-1">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset Filter</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="space-y-3">
        <?php if (empty($meetings)): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center text-slate-500">
                <i class="fa-regular fa-calendar-xmark text-4xl mb-3 text-slate-600 block"></i>
                <p class="text-sm font-semibold text-slate-300">Belum ada meeting yang sesuai dengan filter.</p>
                <a href="<?= base_url('meeting/create') ?>" class="mt-4 inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-sky-600 text-white text-xs font-semibold">
                    <i class="fa-solid fa-plus"></i>
                    <span>Buat Jadwal Baru</span>
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($meetings as $m): ?>
                <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-2xl p-5 shadow-xl transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-2 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                <i class="fa-solid fa-oil-well mr-1"></i> <?= htmlspecialchars($m['rig_name']) ?>
                            </span>

                            <?php if ($m['is_recurring']): ?>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                    <i class="fa-solid fa-repeat mr-1"></i> Rutin Tiap <?= htmlspecialchars($m['recurring_day'] ?: 'Minggu') ?>
                                </span>
                            <?php else: ?>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-800 text-slate-400">
                                    Sekali Jalan
                                </span>
                            <?php endif; ?>

                            <?php if ($m['status'] === 'in_progress'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping mr-1.5"></span> SEDANG BERLANGSUNG
                                </span>
                            <?php elseif ($m['status'] === 'completed'): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400">SELESAI</span>
                            <?php elseif ($m['status'] === 'cancelled'): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400">DIBATALKAN</span>
                            <?php else: ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">TERJADWAL</span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <a href="<?= base_url('meeting/detail/' . $m['id']) ?>" class="text-base font-bold text-white hover:text-sky-400 transition">
                                <?= htmlspecialchars($m['title']) ?>
                            </a>
                            <p class="text-xs text-slate-400 line-clamp-1 mt-0.5"><?= htmlspecialchars($m['topic']) ?></p>
                        </div>

                        <div class="flex flex-wrap items-center text-xs text-slate-400 gap-x-5 gap-y-1 pt-1">
                            <span><i class="fa-regular fa-calendar text-slate-500 mr-1.5"></i> <?= date('d M Y', strtotime($m['meeting_date'])) ?></span>
                            <span><i class="fa-regular fa-clock text-slate-500 mr-1.5"></i> <?= substr($m['start_time'], 0, 5) ?> - <?= substr($m['end_time'], 0, 5) ?> WIB</span>
                            <span><i class="fa-solid fa-user-tie text-slate-500 mr-1.5"></i> PJ: <strong class="text-slate-200"><?= htmlspecialchars($m['pj_name'] ?: '-') ?></strong></span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-slate-800">
                        <a href="<?= htmlspecialchars($m['teams_link']) ?>" target="_blank" class="px-3 py-2 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 text-xs font-semibold flex items-center space-x-1.5 transition">
                            <i class="fa-brands fa-microsoft"></i>
                            <span>Teams</span>
                        </a>

                        <a href="<?= base_url('attendance/live/' . $m['id']) ?>" class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold flex items-center space-x-1.5 shadow-md shadow-sky-600/20 transition">
                            <i class="fa-solid fa-video"></i>
                            <span>Live Attendance</span>
                        </a>

                        <a href="<?= base_url('reminder/broadcast/' . $m['id']) ?>" class="px-3 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 text-xs font-semibold flex items-center space-x-1.5 transition">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span class="hidden sm:inline">Kirim WA</span>
                        </a>

                        <div class="inline-flex items-center space-x-1 pl-1">
                            <button type="button" onclick="openQuickReschedule(<?= $m['id'] ?>, '<?= htmlspecialchars($m['title']) ?>', '<?= $m['meeting_date'] ?>', '<?= substr($m['start_time'], 0, 5) ?>', '<?= substr($m['end_time'], 0, 5) ?>')" 
                                class="p-2 text-slate-400 hover:text-amber-400 hover:bg-slate-800 rounded-lg transition" title="Ubah Jadwal Cepat">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </button>
                            <a href="<?= base_url('meeting/edit/' . $m['id']) ?>" class="p-2 text-slate-400 hover:text-sky-400 hover:bg-slate-800 rounded-lg transition">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <button type="button" onclick="confirmDelete(<?= $m['id'] ?>, '<?= htmlspecialchars($m['title']) ?>')" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- Reschedule Modal -->
<div id="rescheduleModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-amber-400"></i>
                <span>Ubah Jadwal Cepat (Reschedule)</span>
            </h3>
            <button type="button" onclick="closeQuickReschedule()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formReschedule" onsubmit="submitQuickReschedule(event)" class="space-y-4">
            <input type="hidden" id="modalMeetingId" name="meeting_id">
            
            <div>
                <p id="modalMeetingTitle" class="text-xs font-semibold text-sky-400 truncate"></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Tanggal Baru</label>
                <input type="date" id="modalDate" name="meeting_date" required class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Jam Mulai</label>
                    <input type="time" id="modalStartTime" name="start_time" required class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Jam Selesai</label>
                    <input type="time" id="modalEndTime" name="end_time" required class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeQuickReschedule()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold shadow-lg transition">
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
                    background: '#1e293b',
                    color: '#f8fafc'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Reschedule',
                    text: res.message,
                    confirmButtonColor: '#ef4444',
                    background: '#1e293b',
                    color: '#f8fafc'
                });
            }
        });
    }
</script>
