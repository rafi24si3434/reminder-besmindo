<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Ubah Jadwal Meeting</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Perbarui topik, tanggal, jam, atau status meeting rig</p>
        </div>
        <a href="<?= base_url('meeting/detail/' . $meeting['id']) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold border border-zinc-200 dark:border-zinc-800 shadow-xs transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 sm:p-8 shadow-xs">
        <form action="<?= base_url('meeting/save') ?>" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= $meeting['id'] ?>">

            <div>
                <h3 class="text-xs font-semibold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Informasi & Topik Meeting</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Nama / Judul Meeting <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="<?= htmlspecialchars($meeting['title']) ?>" required 
                            class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Topik & Agenda Pembahasan</label>
                        <textarea name="topic" rows="2" class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition"><?= htmlspecialchars($meeting['topic']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Unit Rig <span class="text-rose-500">*</span></label>
                        <select name="rig_id" required class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                            <?php foreach ($rigs as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= ($meeting['rig_id'] == $r['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Status Meeting</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                            <option value="scheduled" <?= ($meeting['status'] == 'scheduled') ? 'selected' : '' ?>>Terjadwal (Scheduled)</option>
                            <option value="in_progress" <?= ($meeting['status'] == 'in_progress') ? 'selected' : '' ?>>Sedang Berlangsung (In Progress)</option>
                            <option value="completed" <?= ($meeting['status'] == 'completed') ? 'selected' : '' ?>>Selesai (Completed)</option>
                            <option value="cancelled" <?= ($meeting['status'] == 'cancelled') ? 'selected' : '' ?>>Dibatalkan (Cancelled)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-semibold text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                    <i class="fa-regular fa-clock"></i>
                    <span>Waktu & Media Teams</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Tanggal Meeting <span class="text-rose-500">*</span></label>
                        <input type="date" name="meeting_date" value="<?= $meeting['meeting_date'] ?>" required 
                            class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" value="<?= substr($meeting['start_time'], 0, 5) ?>" required 
                            class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                        <input type="time" name="end_time" value="<?= substr($meeting['end_time'], 0, 5) ?>" required 
                            class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Link Microsoft Teams</label>
                        <input type="url" name="teams_link" value="<?= htmlspecialchars($meeting['teams_link']) ?>" 
                            class="w-full px-3.5 py-2.5 bg-zinc-50/50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 focus:outline-none transition">
                    </div>
                </div>

                <div class="mt-4 p-3.5 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-lg flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">Jadikan Meeting Rutin Mingguan</span>
                        <span class="text-[11px] text-zinc-500 dark:text-zinc-400">Jadwal akan berulang setiap pekan di hari yang sama</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_recurring" value="1" <?= ($meeting['is_recurring'] == 1) ? 'checked' : '' ?> class="w-4 h-4 text-sky-600 bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700 rounded focus:ring-sky-500/20 focus:ring-2">
                    </label>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-2 mb-3">
                    <h3 class="text-xs font-semibold text-sky-600 dark:text-sky-400 uppercase tracking-wider flex items-center space-x-2">
                        <i class="fa-solid fa-users"></i>
                        <span>Peserta Crew Terundang</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 max-h-60 overflow-y-auto p-1 scrollbar-thin">
                    <?php foreach ($crews as $c): ?>
                        <label class="flex items-center space-x-3 p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200 dark:border-zinc-800/80 hover:bg-zinc-100/70 dark:hover:bg-zinc-900 cursor-pointer transition">
                            <input type="checkbox" name="crew_ids[]" value="<?= $c['id'] ?>" <?= in_array($c['id'], $participantIds) ? 'checked' : '' ?> class="w-4 h-4 text-sky-600 bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700 rounded focus:ring-sky-500/20">
                            <div class="truncate">
                                <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100 block truncate"><?= htmlspecialchars($c['name']) ?></span>
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate block"><?= htmlspecialchars($c['position']) ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-5 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-end space-x-3">
                <a href="<?= base_url('meeting/detail/' . $meeting['id']) ?>" class="px-4 py-2.5 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold border border-zinc-200 dark:border-zinc-800 shadow-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-zinc-900 dark:bg-zinc-50 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-semibold shadow-xs transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
