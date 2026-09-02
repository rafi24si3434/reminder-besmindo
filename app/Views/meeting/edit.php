<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Ubah Jadwal Meeting</h1>
            <p class="text-xs text-slate-400 mt-1">Perbarui topik, tanggal, jam, atau status meeting rig</p>
        </div>
        <a href="<?= base_url('meeting/detail/' . $meeting['id']) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="<?= base_url('meeting/save') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $meeting['id'] ?>">

            <div>
                <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-slate-800 pb-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Informasi & Topik Meeting</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama / Judul Meeting <span class="text-rose-400">*</span></label>
                        <input type="text" name="title" value="<?= old('title', $meeting['title']) ?>" required 
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Topik & Agenda Pembahasan</label>
                        <textarea name="topic" rows="2" class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none transition"><?= old('topic', $meeting['topic']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Unit Rig <span class="text-rose-400">*</span></label>
                        <select name="rig_id" required class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                            <?php foreach ($rigs as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= (old('rig_id', $meeting['rig_id']) == $r['id']) ? 'selected' : '' ?>>
                                    <?= esc($r['name']) ?> (<?= esc($r['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Status Meeting</label>
                        <select name="status" class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                            <option value="scheduled" <?= (old('status', $meeting['status']) == 'scheduled') ? 'selected' : '' ?>>Terjadwal (Scheduled)</option>
                            <option value="in_progress" <?= (old('status', $meeting['status']) == 'in_progress') ? 'selected' : '' ?>>Sedang Berlangsung (In Progress)</option>
                            <option value="completed" <?= (old('status', $meeting['status']) == 'completed') ? 'selected' : '' ?>>Selesai (Completed)</option>
                            <option value="cancelled" <?= (old('status', $meeting['status']) == 'cancelled') ? 'selected' : '' ?>>Dibatalkan (Cancelled)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-slate-800 pb-2">
                    <i class="fa-regular fa-clock"></i>
                    <span>Waktu & Media Teams</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Tanggal Meeting <span class="text-rose-400">*</span></label>
                        <input type="date" name="meeting_date" value="<?= old('meeting_date', $meeting['meeting_date']) ?>" required 
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Jam Mulai <span class="text-rose-400">*</span></label>
                        <input type="time" name="start_time" value="<?= old('start_time', substr($meeting['start_time'], 0, 5)) ?>" required 
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Jam Selesai <span class="text-rose-400">*</span></label>
                        <input type="time" name="end_time" value="<?= old('end_time', substr($meeting['end_time'], 0, 5)) ?>" required 
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Link Microsoft Teams</label>
                        <input type="url" name="teams_link" value="<?= old('teams_link', $meeting['teams_link']) ?>" 
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>
                </div>

                <div class="mt-4 p-3.5 bg-slate-800/40 border border-slate-700/60 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-white block">Jadikan Meeting Rutin Mingguan</span>
                        <span class="text-[11px] text-slate-400">Jadwal akan berulang setiap pekan di hari yang sama</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_recurring" value="1" <?= (old('is_recurring', $meeting['is_recurring']) == 1) ? 'checked' : '' ?> class="w-4 h-4 text-sky-600 bg-slate-800 border-slate-700 rounded focus:ring-sky-500">
                    </label>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-2 mb-3">
                    <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider flex items-center space-x-2">
                        <i class="fa-solid fa-users"></i>
                        <span>Peserta Crew Terundang</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 max-h-60 overflow-y-auto p-1 scrollbar-thin">
                    <?php foreach ($crews as $c): ?>
                        <label class="flex items-center space-x-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/60 hover:bg-slate-800 cursor-pointer transition">
                            <input type="checkbox" name="crew_ids[]" value="<?= $c['id'] ?>" <?= in_array($c['id'], $participantIds) ? 'checked' : '' ?> class="w-4 h-4 text-sky-600 bg-slate-900 border-slate-700 rounded focus:ring-sky-500">
                            <div class="truncate">
                                <span class="text-xs font-bold text-white block truncate"><?= esc($c['name']) ?></span>
                                <span class="text-[10px] text-slate-400 truncate block"><?= esc($c['position']) ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-5 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="<?= base_url('meeting/detail/' . $meeting['id']) ?>" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-lg shadow-sky-600/30 transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
