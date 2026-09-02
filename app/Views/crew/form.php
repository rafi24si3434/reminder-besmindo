<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">
                <?= $crew ? 'Ubah Data Crew Rig' : 'Tambah Personil Crew Baru' ?>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Lengkapi informasi personil crew untuk pengelolaan undangan & kehadiran</p>
        </div>
        <a href="<?= base_url('crew') ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="<?= base_url('crew/save') ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>
            <?php if ($crew): ?>
                <input type="hidden" name="id" value="<?= $crew['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- NIK -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">NIK / ID Pegawai <span class="text-rose-400">*</span></label>
                    <input type="text" name="nik" value="<?= old('nik', $crew['nik'] ?? '') ?>" required 
                        placeholder="Contoh: BSM-0108"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap Crew <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" value="<?= old('name', $crew['name'] ?? '') ?>" required 
                        placeholder="Contoh: Muhammad Ilham"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>

                <!-- Jabatan / Posisi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Jabatan / Peran di Rig <span class="text-rose-400">*</span></label>
                    <input type="text" name="position" value="<?= old('position', $crew['position'] ?? '') ?>" required 
                        placeholder="Contoh: Driller / Floorman / Derrickman / HSE"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>

                <!-- Penempatan Rig -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Penempatan Unit Rig <span class="text-rose-400">*</span></label>
                    <select name="rig_id" required class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                        <option value="">-- Pilih Unit Rig --</option>
                        <?php foreach ($rigs as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (old('rig_id', $crew['rig_id'] ?? '') == $r['id']) ? 'selected' : '' ?>>
                                <?= esc($r['name']) ?> (<?= esc($r['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Nomor WhatsApp -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nomor WhatsApp Aktif <span class="text-rose-400">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-400 text-sm">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <input type="text" name="phone" value="<?= old('phone', $crew['phone'] ?? '') ?>" required 
                            placeholder="Contoh: 08123456789 atau 628123456789"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Digunakan untuk menerima notifikasi undangan & reminder meeting</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Alamat Email (Opsional)</label>
                    <input type="email" name="email" value="<?= old('email', $crew['email'] ?? '') ?>" 
                        placeholder="Contoh: crew@besmindo.co.id"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>
            </div>

            <!-- Status Aktif Toggle -->
            <div class="pt-2">
                <label class="inline-flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?= (old('is_active', $crew['is_active'] ?? 1) == 1) ? 'checked' : '' ?> class="w-4 h-4 text-sky-600 bg-slate-800 border-slate-700 rounded focus:ring-sky-500">
                    <span class="text-xs font-semibold text-slate-300">Crew Berstatus Aktif (Dapat diundang ke meeting rutin)</span>
                </label>
            </div>

            <div class="pt-5 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="<?= base_url('crew') ?>" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-lg shadow-sky-600/30 transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Data Crew</span>
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
