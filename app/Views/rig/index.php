<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Master Data Rig Operasional</h1>
            <p class="text-xs text-slate-400 mt-1">Daftar unit Rig Besmindo beserta penanggung jawab dan lokasi lapangan</p>
        </div>
        <a href="<?= base_url('rig/create') ?>" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-lg shadow-sky-600/30 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Unit Rig</span>
        </a>
    </div>

    <!-- Rig Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($rigs as $r): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between hover:border-slate-700 transition">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center border border-sky-500/20 text-xl font-bold">
                            <i class="fa-solid fa-oil-well"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-mono font-bold bg-slate-800 text-sky-300 border border-slate-700">
                            <?= esc($r['code']) ?>
                        </span>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-white"><?= esc($r['name']) ?></h3>
                        <p class="text-xs text-slate-400 mt-1 flex items-center">
                            <i class="fa-solid fa-location-dot text-slate-500 mr-1.5"></i>
                            <?= esc($r['location']) ?>
                        </p>
                    </div>

                    <?php if (!empty($r['description'])): ?>
                        <p class="text-xs text-slate-500 line-clamp-2"><?= esc($r['description']) ?></p>
                    <?php endif; ?>

                    <div class="pt-3 border-t border-slate-800/80 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Penanggung Jawab (PJ):</span>
                            <strong class="text-slate-200"><?= esc($r['pj_name']) ?></strong>
                        </div>
                        <div class="flex items-center justify-between text-slate-400">
                            <span>WhatsApp PJ:</span>
                            <span class="font-mono text-emerald-400">+<?= esc($r['pj_phone']) ?></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Jumlah Personil Crew:</span>
                            <span class="font-bold text-sky-400"><?= $r['total_crew'] ?> Orang</span>
                        </div>
                    </div>
                </div>

                <div class="pt-5 border-t border-slate-800/80 flex items-center justify-between mt-4">
                    <a href="<?= base_url('crew?rig_id=' . $r['id']) ?>" class="text-xs font-semibold text-sky-400 hover:text-sky-300 flex items-center space-x-1">
                        <span>Lihat Daftar Crew</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                    <div class="flex items-center space-x-2">
                        <a href="<?= base_url('rig/edit/' . $r['id']) ?>" class="p-1.5 text-slate-400 hover:text-sky-400 hover:bg-slate-800 rounded-lg transition" title="Edit Rig">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <button type="button" onclick="confirmDelete(<?= $r['id'] ?>, '<?= esc($r['name']) ?>')" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition" title="Hapus Rig">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id, name) {
        confirmAction('Hapus Data Rig?', `Apakah Anda yakin ingin menghapus "${name}"? Semua data crew dan jadwal terkait rig ini akan terpengaruh.`, 'Ya, Hapus Rig')
        .then(result => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('rig/delete/') ?>/${id}`;
            }
        });
    }
</script>
<?= $this->endSection() ?>
