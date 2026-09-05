<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                <?= $crew ? 'Ubah Data Crew Rig' : 'Tambah Personil Crew Baru' ?>
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Lengkapi informasi personil crew untuk pengelolaan undangan &amp; kehadiran</p>
        </div>
        <a href="<?= base_url('crew') ?>"
            class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg border text-xs font-medium transition
                bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border-zinc-200
                dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-700">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="rounded-xl border p-6 sm:p-8 shadow-sm transition-colors
        bg-white dark:bg-zinc-900
        border-zinc-200 dark:border-zinc-800">
        <form action="<?= base_url('crew/save') ?>" method="POST" class="space-y-5">
            <?php if ($crew): ?>
                <input type="hidden" name="id" value="<?= $crew['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">NIK / ID Pegawai <span class="text-rose-500">*</span></label>
                    <input type="text" name="nik" value="<?= isset($crew['nik']) ? htmlspecialchars($crew['nik']) : '' ?>" required 
                        placeholder="Contoh: BSM-0108"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Nama Lengkap Crew <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="<?= isset($crew['name']) ? htmlspecialchars($crew['name']) : '' ?>" required 
                        placeholder="Contoh: Muhammad Ilham"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Jabatan / Peran di Rig <span class="text-rose-500">*</span></label>
                    <input type="text" name="position" value="<?= isset($crew['position']) ? htmlspecialchars($crew['position']) : '' ?>" required 
                        placeholder="Contoh: Driller / Floorman / Derrickman / HSE"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Penempatan Unit Rig <span class="text-rose-500">*</span></label>
                    <select name="rig_id" required
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        <option value="">-- Pilih Unit Rig --</option>
                        <?php foreach ($rigs as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (isset($crew['rig_id']) && $crew['rig_id'] == $r['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-500 text-sm">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <input type="text" name="phone" value="<?= isset($crew['phone']) ? htmlspecialchars($crew['phone']) : '' ?>" required 
                            placeholder="Contoh: 08123456789 atau 628123456789"
                            class="w-full pl-9 pr-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Alamat Email (Opsional)</label>
                    <input type="email" name="email" value="<?= isset($crew['email']) ? htmlspecialchars($crew['email']) : '' ?>" 
                        placeholder="Contoh: crew@besmindo.co.id"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?= (!isset($crew['is_active']) || $crew['is_active'] == 1) ? 'checked' : '' ?>
                        class="w-4 h-4 text-sky-600 rounded border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 focus:ring-sky-500">
                    <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Crew Berstatus Aktif</span>
                </label>
            </div>

            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end space-x-2.5">
                <a href="<?= base_url('crew') ?>"
                    class="px-4 py-2 rounded-lg border text-xs font-medium transition
                        bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border-zinc-200
                        dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-700">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition flex items-center space-x-2
                        bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Data Crew</span>
                </button>
            </div>
        </form>
    </div>

</div>

