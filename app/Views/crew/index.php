<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Manajemen Crew Rig</h1>
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">
                    <?= count($crews) ?> Total
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Daftar personil rig yang berhak diundang dan dipantau kehadirannya</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= base_url('crew/create') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-lg shadow-sky-600/30 transition">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah Crew</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg">
        <form method="GET" action="<?= base_url('crew') ?>" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div>
                    <select name="rig_id" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">-- Semua Unit Rig --</option>
                        <?php foreach ($rigs as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= $selectedRig == $r['id'] ? 'selected' : '' ?>>
                                <?= esc($r['name']) ?> (<?= esc($r['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select name="active" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">-- Semua Status --</option>
                        <option value="1" <?= $activeFilter === '1' ? 'selected' : '' ?>>Hanya Crew Aktif</option>
                        <option value="0" <?= $activeFilter === '0' ? 'selected' : '' ?>>Non-Aktif</option>
                    </select>
                </div>
                <?php if ($selectedRig || $activeFilter !== null && $activeFilter !== ''): ?>
                    <a href="<?= base_url('crew') ?>" class="text-xs text-slate-400 hover:text-white flex items-center space-x-1">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset Filter</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Crew Table Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-semibold text-[11px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">NIK & Nama Crew</th>
                        <th class="px-5 py-3.5">Jabatan / Posisi</th>
                        <th class="px-5 py-3.5">Penempatan Rig</th>
                        <th class="px-5 py-3.5">Kontak WhatsApp</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($crews)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                                Tidak ada data crew yang sesuai filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($crews as $c): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sky-400 font-bold text-xs">
                                            <?= strtoupper(substr($c['name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white"><?= esc($c['name']) ?></div>
                                            <div class="text-[10px] text-slate-500 font-mono"><?= esc($c['nik']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-200">
                                    <?= esc($c['position']) ?>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-500/10 text-sky-300 border border-sky-500/20">
                                        <?= esc($c['rig_name'] ?? '-') ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono text-slate-300">+<?= esc($c['phone']) ?></span>
                                        <a href="https://wa.me/<?= esc($c['phone']) ?>" target="_blank" title="Kirim Chat WA Langsung" 
                                            class="w-6 h-6 rounded-md bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 flex items-center justify-center transition">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <?php if ($c['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-400">
                                            Non-Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="inline-flex items-center space-x-1.5">
                                        <a href="<?= base_url('crew/edit/' . $c['id']) ?>" title="Edit Data" 
                                            class="p-1.5 text-slate-400 hover:text-sky-400 hover:bg-slate-800 rounded-lg transition">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <a href="<?= base_url('crew/toggleStatus/' . $c['id']) ?>" title="<?= $c['is_active'] ? 'Nonaktifkan Crew' : 'Aktifkan Crew' ?>" 
                                            class="p-1.5 <?= $c['is_active'] ? 'text-amber-400 hover:text-amber-300' : 'text-emerald-400 hover:text-emerald-300' ?> hover:bg-slate-800 rounded-lg transition">
                                            <i class="fa-solid <?= $c['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                        </a>
                                        <button type="button" onclick="confirmDelete(<?= $c['id'] ?>, '<?= esc($c['name']) ?>')" title="Hapus Crew" 
                                            class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id, name) {
        confirmAction('Hapus Data Crew?', `Apakah Anda yakin ingin menghapus data crew "${name}"? Tindakan ini tidak dapat dibatalkan.`, 'Ya, Hapus Data')
        .then(result => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('crew/delete/') ?>/${id}`;
            }
        });
    }
</script>
<?= $this->endSection() ?>
