<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">
                <?= $rig ? 'Ubah Data Unit Rig' : 'Tambah Unit Rig Baru' ?>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Data lokasi rig, kode identifikasi dan penanggung jawab operasional</p>
        </div>
        <a href="<?= base_url('rig') ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="<?= base_url('rig/save') ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>
            <?php if ($rig): ?>
                <input type="hidden" name="id" value="<?= $rig['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Unit Rig <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" value="<?= old('name', $rig['name'] ?? '') ?>" required 
                        placeholder="Contoh: RIG 04 - Libo Sector"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kode Rig <span class="text-rose-400">*</span></label>
                    <input type="text" name="code" value="<?= old('code', $rig['code'] ?? '') ?>" required 
                        placeholder="Contoh: RIG-04-LB"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Lokasi Lapangan / Blok <span class="text-rose-400">*</span></label>
                    <input type="text" name="location" value="<?= old('location', $rig['location'] ?? '') ?>" required 
                        placeholder="Contoh: Libo Field Area 2, Riau"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Penanggung Jawab (PJ Rig) <span class="text-rose-400">*</span></label>
                    <input type="text" name="pj_name" value="<?= old('pj_name', $rig['pj_name'] ?? '') ?>" required 
                        placeholder="Contoh: Ir. Rahmat Santoso"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nomor WhatsApp PJ Rig <span class="text-rose-400">*</span></label>
                    <input type="text" name="pj_phone" value="<?= old('pj_phone', $rig['pj_phone'] ?? '') ?>" required 
                        placeholder="Contoh: 081234567890"
                        class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Deskripsi Spesifikasi Rig (Opsional)</label>
                    <textarea name="description" rows="3" placeholder="Informasi kapasitas horsepower, kedalaman pengeboran, dll." class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 transition"><?= old('description', $rig['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?= (old('is_active', $rig['is_active'] ?? 1) == 1) ? 'checked' : '' ?> class="w-4 h-4 text-sky-600 bg-slate-800 border-slate-700 rounded focus:ring-sky-500">
                    <span class="text-xs font-semibold text-slate-300">Unit Rig Berstatus Aktif Beroperasi</span>
                </label>
            </div>

            <div class="pt-5 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="<?= base_url('rig') ?>" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-lg shadow-sky-600/30 transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Unit Rig</span>
                </button>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>
