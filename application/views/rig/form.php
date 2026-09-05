<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                <?= $rig ? 'Ubah Data Unit Rig' : 'Tambah Unit Rig Baru' ?>
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Data lokasi rig, target WhatsApp Group, dan penanggung jawab operasional</p>
        </div>
        <a href="<?= base_url('rig') ?>"
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
        <form action="<?= base_url('rig/save') ?>" method="POST" class="space-y-5">
            <?php if ($rig): ?>
                <input type="hidden" name="id" value="<?= $rig['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Nama Unit Rig <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="<?= isset($rig['name']) ? htmlspecialchars($rig['name']) : '' ?>" required 
                        placeholder="Contoh: RIG 04 - Libo Sector"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Kode Rig <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="<?= isset($rig['code']) ? htmlspecialchars($rig['code']) : '' ?>" required 
                        placeholder="Contoh: RIG-04-LB"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Lokasi Lapangan / Blok <span class="text-rose-500">*</span></label>
                    <input type="text" name="location" value="<?= isset($rig['location']) ? htmlspecialchars($rig['location']) : '' ?>" required 
                        placeholder="Contoh: Libo Field Area 2, Riau"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>

                <!-- WhatsApp Group Rig Section -->
                <div class="sm:col-span-2 rounded-lg border p-4 sm:p-5 space-y-3
                    bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm
                                bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-400">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">WhatsApp Group Unit Rig</span>
                                <span class="text-[11px] text-zinc-500 dark:text-zinc-400">Undangan &amp; reminder meeting otomatis dikirimkan ke grup ini</span>
                            </div>
                        </div>
                        <button type="button" onclick="loadBotGroups()" id="btnLoadGroups"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium transition
                                bg-white hover:bg-emerald-50 text-emerald-700 border-emerald-300
                                dark:bg-zinc-900 dark:hover:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800 shadow-sm">
                            <i class="fa-solid fa-arrows-rotate text-[11px]" id="loadGroupsIcon"></i>
                            <span>Pilih dari Grup WA Bot</span>
                        </button>
                    </div>

                    <!-- Dropdown Pemilihan Grup WA -->
                    <div id="groupPickerContainer" class="hidden rounded-lg border p-3 space-y-2
                        bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
                        <label class="block text-[11px] font-medium text-emerald-600 dark:text-emerald-400">Grup WhatsApp yang Diikuti Bot:</label>
                        <div class="flex gap-2">
                            <select id="selectWaGroup"
                                class="w-full px-3 py-1.5 rounded-lg border text-xs
                                    bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                    text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- Pilih Grup WhatsApp --</option>
                            </select>
                            <button type="button" onclick="applySelectedGroup()"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium text-white transition
                                    bg-emerald-600 hover:bg-emerald-500 whitespace-nowrap shadow-sm">
                                Terapkan
                            </button>
                        </div>
                        <p id="groupPickerNotice" class="text-[10px] text-zinc-500 dark:text-zinc-400"></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-700 dark:text-zinc-300 mb-1">ID Group WhatsApp (Target)</label>
                            <input type="text" name="wa_group_id" id="inputWaGroupId" value="<?= isset($rig['wa_group_id']) ? htmlspecialchars($rig['wa_group_id']) : '' ?>" 
                                placeholder="Contoh: 12036304xxxxxxxxxx@g.us"
                                class="w-full px-3 py-1.5 rounded-lg border text-xs font-mono transition
                                    bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                    text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                    focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Format: ID grup berakhiran <code class="text-emerald-600 dark:text-emerald-400">@g.us</code></p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Group WhatsApp</label>
                            <input type="text" name="wa_group_name" id="inputWaGroupName" value="<?= isset($rig['wa_group_name']) ? htmlspecialchars($rig['wa_group_name']) : '' ?>" 
                                placeholder="Contoh: Crew Rig 04 Libo Official"
                                class="w-full px-3 py-1.5 rounded-lg border text-xs transition
                                    bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                    text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                    focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-1">Label pengenal grup yang ditampilkan di sistem</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Penanggung Jawab (PJ Rig) <span class="text-rose-500">*</span></label>
                    <input type="text" name="pj_name" value="<?= isset($rig['pj_name']) ? htmlspecialchars($rig['pj_name']) : '' ?>" required 
                        placeholder="Contoh: Ir. Rahmat Santoso"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Nomor WhatsApp PJ Rig <span class="text-rose-500">*</span></label>
                    <input type="text" name="pj_phone" value="<?= isset($rig['pj_phone']) ? htmlspecialchars($rig['pj_phone']) : '' ?>" required 
                        placeholder="Contoh: 081234567890"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Deskripsi Spesifikasi Rig (Opsional)</label>
                    <textarea name="description" rows="3" placeholder="Informasi kapasitas horsepower, kedalaman pengeboran, dll."
                        class="w-full px-3 py-2 rounded-lg border text-sm transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                            focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500"><?= isset($rig['description']) ? htmlspecialchars($rig['description']) : '' ?></textarea>
                </div>
            </div>

            <div class="pt-2">
                <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?= (!isset($rig['is_active']) || $rig['is_active'] == 1) ? 'checked' : '' ?>
                        class="w-4 h-4 text-sky-600 rounded border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 focus:ring-sky-500">
                    <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Unit Rig Berstatus Aktif Beroperasi</span>
                </label>
            </div>

            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end space-x-2.5">
                <a href="<?= base_url('rig') ?>"
                    class="px-4 py-2 rounded-lg border text-xs font-medium transition
                        bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border-zinc-200
                        dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-700">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition flex items-center space-x-2
                        bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Unit Rig</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
let botGroupsData = [];

async function loadBotGroups() {
    const icon = document.getElementById('loadGroupsIcon');
    const container = document.getElementById('groupPickerContainer');
    const select = document.getElementById('selectWaGroup');
    const notice = document.getElementById('groupPickerNotice');

    icon.classList.add('fa-spin');
    notice.innerHTML = '<span class="text-sky-600 dark:text-sky-400">Sedang membaca daftar grup dari WhatsApp Gateway...</span>';
    container.classList.remove('hidden');

    try {
        const res = await fetch('<?= base_url('rig/api_groups') ?>');
        const json = await res.json();
        icon.classList.remove('fa-spin');

        if (!json.success || !json.data || json.data.length === 0) {
            notice.innerHTML = `<span class="text-amber-600 dark:text-amber-400"><i class="fa-solid fa-triangle-exclamation mr-1"></i>${json.message || 'Tidak ada grup ditemukan atau WhatsApp belum tersambung di http://localhost:3000.'}</span>`;
            return;
        }

        botGroupsData = json.data;
        select.innerHTML = '<option value="">-- Pilih salah satu Grup WhatsApp (' + json.data.length + ' grup ditemukan) --</option>';
        json.data.forEach((grp) => {
            const opt = document.createElement('option');
            opt.value = grp.id;
            opt.textContent = `${grp.name} (${grp.participantsCount || '?'} anggota)`;
            select.appendChild(opt);
        });

        notice.innerHTML = '<span class="text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-check mr-1"></i>Ditemukan ' + json.data.length + ' grup WhatsApp. Pilih grup lalu klik tombol Terapkan.</span>';
    } catch (err) {
        icon.classList.remove('fa-spin');
        notice.innerHTML = '<span class="text-rose-600 dark:text-rose-400"><i class="fa-solid fa-circle-exclamation mr-1"></i>Gagal memuat grup. Pastikan Gateway WhatsApp aktif di http://localhost:3000.</span>';
    }
}

function applySelectedGroup() {
    const select = document.getElementById('selectWaGroup');
    const selectedId = select.value;
    if (!selectedId) {
        alert('Silakan pilih salah satu grup terlebih dahulu dari pilihan di atas.');
        return;
    }

    const found = botGroupsData.find(g => g.id === selectedId);
    if (found) {
        document.getElementById('inputWaGroupId').value = found.id;
        document.getElementById('inputWaGroupName').value = found.name;
        
        const inputId = document.getElementById('inputWaGroupId');
        const inputName = document.getElementById('inputWaGroupName');
        inputId.classList.add('ring-2', 'ring-emerald-400');
        inputName.classList.add('ring-2', 'ring-emerald-400');
        setTimeout(() => {
            inputId.classList.remove('ring-2', 'ring-emerald-400');
            inputName.classList.remove('ring-2', 'ring-emerald-400');
        }, 1500);
    }
}
</script>

