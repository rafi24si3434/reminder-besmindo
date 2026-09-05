<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Master Data Rig Operasional</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Daftar unit Rig Besmindo beserta penanggung jawab dan lokasi lapangan</p>
        </div>
        <a href="<?= base_url('rig/create') ?>"
            class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition
                bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Unit Rig</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <?php foreach ($rigs as $r): ?>
            <div class="rounded-xl border p-5 shadow-sm transition-colors flex flex-col justify-between
                bg-white dark:bg-zinc-900
                border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700">
                <div class="space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg
                            bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 border border-sky-100 dark:border-sky-900">
                            <i class="fa-solid fa-tower-observation"></i>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-medium
                            bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                            <?= htmlspecialchars($r['code']) ?>
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($r['name']) ?></h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 flex items-center">
                            <i class="fa-solid fa-location-dot text-zinc-400 mr-1.5"></i>
                            <?= htmlspecialchars($r['location']) ?>
                        </p>
                    </div>

                    <?php if (!empty($r['description'])): ?>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2"><?= htmlspecialchars($r['description']) ?></p>
                    <?php endif; ?>

                    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                            <span>PJ Rig:</span>
                            <strong class="text-zinc-800 dark:text-zinc-200"><?= htmlspecialchars($r['pj_name']) ?></strong>
                        </div>
                        <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                            <span>WhatsApp PJ:</span>
                            <span class="font-mono text-emerald-600 dark:text-emerald-400">+<?= htmlspecialchars($r['pj_phone']) ?></span>
                        </div>
                        <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                            <span>WhatsApp Group:</span>
                            <?php if (!empty($r['wa_group_id'])): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium
                                    bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 max-w-[140px] truncate" title="<?= htmlspecialchars($r['wa_group_name'] ?: $r['wa_group_id']) ?>">
                                    <i class="fa-brands fa-whatsapp mr-1 text-emerald-600"></i>
                                    <span class="truncate"><?= htmlspecialchars($r['wa_group_name'] ?: 'Terhubung') ?></span>
                                </span>
                            <?php else: ?>
                                <span class="text-zinc-400 text-[11px] italic">Belum diatur</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                            <span>Total Personil:</span>
                            <span class="font-semibold text-sky-600 dark:text-sky-400"><?= $r['total_crew'] ?> Orang</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between mt-4">
                    <a href="<?= base_url('crew?rig_id=' . $r['id']) ?>" class="text-xs font-medium text-sky-600 dark:text-sky-400 hover:underline flex items-center space-x-1">
                        <span>Lihat Crew</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                    <div class="flex items-center space-x-1">
                        <a href="<?= base_url('rig/edit/' . $r['id']) ?>"
                            class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition" title="Edit Rig">
                            <i class="fa-regular fa-pen-to-square text-xs"></i>
                        </a>
                        <button type="button" onclick="confirmDelete(<?= $r['id'] ?>, '<?= htmlspecialchars($r['name']) ?>')"
                            class="p-1.5 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-md transition" title="Hapus Rig">
                            <i class="fa-regular fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

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

