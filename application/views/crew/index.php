<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2.5">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Manajemen Crew Rig</h1>
                <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 font-mono">
                    <?= count($crews) ?> Total
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Daftar personil rig yang berhak diundang dan dipantau kehadirannya</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <button type="button" onclick="openWaImportModal()"
                class="inline-flex items-center space-x-2 px-3 py-2 rounded-lg border text-xs font-medium transition shadow-sm
                    bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200
                    dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 dark:text-emerald-300 dark:border-emerald-800">
                <i class="fa-brands fa-whatsapp text-sm text-emerald-600 dark:text-emerald-400"></i>
                <span>Tarik Kontak dari WA Group</span>
            </button>
            <a href="<?= base_url('crew/create') ?>"
                class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition
                    bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Crew</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="rounded-xl border p-3.5 shadow-sm transition-colors
        bg-white dark:bg-zinc-900
        border-zinc-200 dark:border-zinc-800">
        <form method="GET" action="<?= base_url('crew') ?>" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                <div>
                    <select name="rig_id" onchange="this.form.submit()"
                        class="text-xs rounded-lg px-3 py-1.5 border transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        <option value="">-- Semua Unit Rig --</option>
                        <?php foreach ($rigs as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($selectedRig == $r['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select name="group" onchange="this.form.submit()"
                        class="text-xs rounded-lg px-3 py-1.5 border font-medium transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        <option value="">-- Semua Grup --</option>
                        <option value="A" <?= (isset($selectedGroup) && $selectedGroup === 'A') ? 'selected' : '' ?>>Grup A</option>
                        <option value="B" <?= (isset($selectedGroup) && $selectedGroup === 'B') ? 'selected' : '' ?>>Grup B</option>
                        <option value="C" <?= (isset($selectedGroup) && $selectedGroup === 'C') ? 'selected' : '' ?>>Grup C</option>
                    </select>
                </div>
                <div>
                    <select name="active" onchange="this.form.submit()"
                        class="text-xs rounded-lg px-3 py-1.5 border transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                            text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        <option value="">-- Semua Status --</option>
                        <option value="1" <?= ($activeFilter === '1') ? 'selected' : '' ?>>Hanya Crew Aktif</option>
                        <option value="0" <?= ($activeFilter === '0') ? 'selected' : '' ?>>Non-Aktif</option>
                    </select>
                </div>
                <?php if ($selectedRig || !empty($selectedGroup) || ($activeFilter !== null && $activeFilter !== '')): ?>
                    <a href="<?= base_url('crew') ?>" class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 flex items-center space-x-1">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        <span>Reset Filter</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="rounded-xl border shadow-sm overflow-hidden transition-colors
        bg-white dark:bg-zinc-900
        border-zinc-200 dark:border-zinc-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-600 dark:text-zinc-300">
                <thead class="uppercase font-medium text-[11px] tracking-wider border-b transition-colors
                    bg-zinc-50 dark:bg-zinc-950/80 text-zinc-500 dark:text-zinc-400
                    border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-5 py-3">Nama &amp; ID Crew</th>
                        <th class="px-5 py-3">Jabatan / Posisi</th>
                        <th class="px-5 py-3">Unit Rig &amp; Grup</th>
                        <th class="px-5 py-3">Kontak WhatsApp</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    <?php if (empty($crews)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-zinc-400 text-xs">
                                Tidak ada data crew yang sesuai filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($crews as $c): ?>
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center font-semibold text-[11px]
                                            bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                            <?= strtoupper(substr($c['name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100"><?= htmlspecialchars($c['name']) ?></div>
                                            <div class="text-[10px] text-zinc-400 font-mono"><?= htmlspecialchars($c['nik']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 font-medium text-zinc-700 dark:text-zinc-300">
                                    <?= htmlspecialchars($c['position']) ?>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium
                                            bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                            <?= htmlspecialchars($c['rig_name'] ?: '-') ?>
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold
                                            bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Grup <?= htmlspecialchars($c['group_code'] ?: 'A') ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono text-zinc-600 dark:text-zinc-300">+<?= htmlspecialchars($c['phone']) ?></span>
                                        <a href="https://wa.me/<?= htmlspecialchars($c['phone']) ?>" target="_blank" title="Kirim Chat WA Langsung" 
                                            class="w-6 h-6 rounded-md flex items-center justify-center transition
                                                bg-emerald-50 hover:bg-emerald-100 text-emerald-600
                                                dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 dark:text-emerald-400">
                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <?php if ($c['is_active']): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                            bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                            bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                            Non-Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex items-center space-x-1">
                                        <a href="<?= base_url('crew/edit/' . $c['id']) ?>" title="Edit Data" 
                                            class="p-1.5 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition">
                                            <i class="fa-regular fa-pen-to-square text-xs"></i>
                                        </a>
                                        <a href="<?= base_url('crew/toggle_status/' . $c['id']) ?>" title="<?= $c['is_active'] ? 'Nonaktifkan Crew' : 'Aktifkan Crew' ?>" 
                                            class="p-1.5 <?= $c['is_active'] ? 'text-amber-500 hover:text-amber-600' : 'text-emerald-500 hover:text-emerald-600' ?> hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition">
                                            <i class="fa-solid <?= $c['is_active'] ? 'fa-user-slash' : 'fa-user-check' ?> text-xs"></i>
                                        </a>
                                        <button type="button" onclick="confirmDelete(<?= $c['id'] ?>, '<?= htmlspecialchars($c['name']) ?>')" title="Hapus Crew" 
                                            class="p-1.5 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-md transition">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
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

<!-- MODAL TARIK KONTAK DARI WHATSAPP GROUP -->
<div id="waImportModal" class="fixed inset-0 z-50 hidden bg-zinc-950/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="rounded-xl border w-full max-w-4xl max-h-[92vh] flex flex-col shadow-xl overflow-hidden transition-colors
        bg-white dark:bg-zinc-900
        border-zinc-200 dark:border-zinc-800">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b flex items-center justify-between
            bg-zinc-50 dark:bg-zinc-950/50 border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-base
                    bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Tarik Anggota dari WhatsApp Group</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Deteksi nomor kontak dari grup WA &amp; lengkapi nama manual tanpa perlu ketik nomor HP</p>
                </div>
            </div>
            <button type="button" onclick="closeWaImportModal()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 space-y-5 overflow-y-auto flex-1">
            
            <!-- Step 1: Filter / Pemilihan Grup -->
            <div class="rounded-lg border p-4 space-y-3
                bg-zinc-50 dark:bg-zinc-950/50 border-zinc-200 dark:border-zinc-800">
                <div class="text-xs font-medium text-zinc-700 dark:text-zinc-300 flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-semibold
                        bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800">1</span>
                    <span>Pilih Target Grup WhatsApp &amp; Penempatan Rig</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-5">
                        <label class="block text-[11px] font-medium text-zinc-600 dark:text-zinc-400 mb-1">Grup WhatsApp Target:</label>
                        <select id="importGroupSelect" onchange="onGroupSelectChange()"
                            class="w-full px-3 py-1.5 rounded-lg border text-xs
                                bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Pilih Grup WhatsApp --</option>
                            <?php foreach ($rigs as $r): ?>
                                <?php if (!empty($r['wa_group_id'])): ?>
                                    <option value="<?= htmlspecialchars($r['wa_group_id']) ?>" data-rig-id="<?= $r['id'] ?>" data-rig-name="<?= htmlspecialchars($r['name']) ?>">
                                        <?= htmlspecialchars($r['wa_group_name'] ?: $r['name']) ?> (<?= htmlspecialchars($r['name']) ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-medium text-zinc-600 dark:text-zinc-400 mb-1">Default Unit Rig Tujuan:</label>
                        <select id="importTargetRigSelect"
                            class="w-full px-3 py-1.5 rounded-lg border text-xs
                                bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                            <option value="">-- Pilih Unit Rig --</option>
                            <?php foreach ($rigs as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= ($selectedRig == $r['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="sm:col-span-3 flex items-end">
                        <button type="button" id="btnFetchGroupMembers" onclick="fetchGroupMembers()"
                            class="w-full py-1.5 px-3 rounded-lg text-xs font-medium text-white shadow-sm transition flex items-center justify-center space-x-2
                                bg-emerald-600 hover:bg-emerald-500">
                            <i class="fa-solid fa-users-viewfinder text-xs" id="fetchMembersIcon"></i>
                            <span>Tarik Kontak</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 text-[11px] text-zinc-500 dark:text-zinc-400 flex-wrap gap-2">
                    <span id="groupFetchHint"><i class="fa-solid fa-circle-info mr-1 text-sky-500"></i>Pilih grup rig di atas, atau muat daftar grup langsung dari bot WhatsApp.</span>
                    <button type="button" onclick="refreshBotGroupsList()" class="text-emerald-600 dark:text-emerald-400 hover:underline flex items-center space-x-1">
                        <i class="fa-solid fa-arrows-rotate text-[10px]" id="iconRefreshBot"></i>
                        <span>Muat Semua Grup Bot</span>
                    </button>
                </div>
            </div>

            <!-- Step 2: Info Bar & Statistik Peserta -->
            <div id="importStatsBar" class="hidden rounded-lg border p-3 flex flex-wrap items-center justify-between gap-3
                bg-zinc-50 dark:bg-zinc-800/40 border-zinc-200 dark:border-zinc-800">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-md bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-medium">
                        Total Anggota: <strong id="statTotalMembers">0</strong>
                    </span>
                    <span class="px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-medium">
                        <i class="fa-solid fa-check mr-1"></i>Sudah Terdaftar: <strong id="statRegisteredMembers">0</strong>
                    </span>
                    <span class="px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 font-medium">
                        <i class="fa-solid fa-user-plus mr-1"></i>Belum Terdaftar: <strong id="statNewMembers">0</strong>
                    </span>
                </div>

                <div class="flex items-center space-x-2">
                    <button type="button" onclick="copyAllPhoneNumbers()"
                        class="px-3 py-1.5 rounded-lg border text-xs font-medium transition flex items-center space-x-1.5
                            bg-white hover:bg-zinc-50 text-zinc-700 border-zinc-200
                            dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700" title="Salin semua nomor ke clipboard">
                        <i class="fa-regular fa-copy text-xs"></i>
                        <span id="copyBtnText">Salin Semua Nomor</span>
                    </button>
                    <label class="inline-flex items-center space-x-1.5 cursor-pointer text-xs text-zinc-700 dark:text-zinc-300 ml-2">
                        <input type="checkbox" id="checkOnlyUnregistered" onchange="filterMembersTable()" checked
                            class="rounded border-zinc-300 dark:border-zinc-700 text-emerald-600 focus:ring-emerald-500">
                        <span>Hanya Belum Terdaftar</span>
                    </label>
                </div>
            </div>

            <!-- Step 3: Tabel Input Personil -->
            <div id="importTableContainer" class="hidden space-y-2">
                <div class="text-xs font-medium text-zinc-700 dark:text-zinc-300 flex items-center justify-between flex-wrap gap-1">
                    <div class="flex items-center space-x-2">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-semibold
                            bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">2</span>
                        <span>Daftar Kontak &amp; Input Nama Personil</span>
                    </div>
                    <span class="text-[11px] text-zinc-500 dark:text-zinc-400 font-normal">Nomor HP terisi otomatis &amp; aman dari typo.</span>
                </div>

                <div class="rounded-lg border overflow-hidden max-h-80 overflow-y-auto
                    bg-white dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800">
                    <table class="w-full text-left text-xs text-zinc-600 dark:text-zinc-300">
                        <thead class="sticky top-0 z-10 uppercase font-medium text-[10px] tracking-wider border-b
                            bg-zinc-50 dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="px-3 py-2 w-10 text-center">
                                    <input type="checkbox" id="checkAllImport" onchange="toggleSelectAllImport(this.checked)" checked
                                        class="rounded border-zinc-300 dark:border-zinc-700 text-emerald-600 focus:ring-emerald-500">
                                </th>
                                <th class="px-3 py-2 w-36">Nomor WhatsApp</th>
                                <th class="px-3 py-2">Nama Lengkap Personil <span class="text-rose-500">*</span></th>
                                <th class="px-3 py-2 w-44">Jabatan</th>
                                <th class="px-3 py-2 w-32">Draft NIK</th>
                                <th class="px-3 py-2 w-24 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="importMembersTableBody" class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            <!-- Rows rendered via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t flex items-center justify-between flex-wrap gap-3
            bg-zinc-50 dark:bg-zinc-950/60 border-zinc-200 dark:border-zinc-800">
            <span id="importSelectionCount" class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">0 personil dipilih untuk disimpan</span>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="closeWaImportModal()"
                    class="px-4 py-2 rounded-lg border text-xs font-medium transition
                        bg-white hover:bg-zinc-100 text-zinc-700 border-zinc-200
                        dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-300 dark:border-zinc-700">
                    Batal
                </button>
                <button type="button" id="btnSaveImportedCrews" onclick="saveImportedCrews()" disabled
                    class="px-4 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition flex items-center space-x-2
                        bg-emerald-600 hover:bg-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span id="btnSaveText">Simpan Personil ke Master Data</span>
                </button>
            </div>
        </div>

</div>
</div>

<script>
    const RIG_OPTIONS = <?= json_encode(array_map(function($r) {

        return ['id' => $r['id'], 'name' => $r['name'], 'code' => $r['code']];
    }, $rigs)) ?>;

    const POSITIONS = [
        'Toolpusher',
        'Rig Superintendent',
        'Night Toolpusher',
        'Driller',
        'Assistant Driller',
        'Derrickman',
        'Floorman / Roughneck',
        'Roustabout',
        'Chief Mechanic',
        'Mechanic',
        'Chief Electrician',
        'Electrician',
        'HSE Officer / Safety',
        'Welder',
        'Mud Engineer',
        'Rig Clerk / Admin Rig',
        'Storekeeper / Toolman',
        'Crane Operator',
        'Crew Lapangan'
    ];

    let currentGroupMembers = [];

    function openWaImportModal() {
        document.getElementById('waImportModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeWaImportModal() {
        document.getElementById('waImportModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function onGroupSelectChange() {
        const select = document.getElementById('importGroupSelect');
        const selectedOpt = select.options[select.selectedIndex];
        const rigId = selectedOpt.getAttribute('data-rig-id');
        if (rigId) {
            document.getElementById('importTargetRigSelect').value = rigId;
        }
    }

    async function refreshBotGroupsList() {
        const icon = document.getElementById('iconRefreshBot');
        const select = document.getElementById('importGroupSelect');
        icon.classList.add('fa-spin');

        try {
            const res = await fetch('<?= base_url('crew/api_groups') ?>');
            const json = await res.json();
            icon.classList.remove('fa-spin');

            if (!json.success || !json.data || json.data.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Grup',
                    text: json.message || 'Bot belum terhubung atau belum dimasukkan ke grup mana pun.',
                    background: '#0f172a',
                    color: '#fff'
                });
                return;
            }

            select.innerHTML = '<option value="">-- Pilih salah satu dari ' + json.data.length + ' Grup Bot --</option>';
            json.data.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.id;
                opt.textContent = `${g.name} (${g.participantsCount || '?'} anggota)`;
                select.appendChild(opt);
            });

            document.getElementById('groupFetchHint').innerHTML = `<span class="text-emerald-400 font-semibold"><i class="fa-solid fa-circle-check mr-1"></i>${json.data.length} grup WhatsApp bot berhasil dimuat.</span>`;
        } catch (err) {
            icon.classList.remove('fa-spin');
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Pastikan WhatsApp Gateway aktif di http://localhost:3000.',
                background: '#0f172a',
                color: '#fff'
            });
        }
    }

    async function fetchGroupMembers() {
        const groupId = document.getElementById('importGroupSelect').value;
        const rigId = document.getElementById('importTargetRigSelect').value;
        const btn = document.getElementById('btnFetchGroupMembers');
        const icon = document.getElementById('fetchMembersIcon');

        if (!groupId) {
            Swal.fire({
                icon: 'info',
                title: 'Pilih Grup WhatsApp',
                text: 'Silakan pilih grup WhatsApp target terlebih dahulu.',
                background: '#0f172a',
                color: '#fff'
            });
            return;
        }

        if (!rigId) {
            Swal.fire({
                icon: 'info',
                title: 'Pilih Rig Tujuan',
                text: 'Silakan pilih Unit Rig penempatan untuk crew yang akan diimpor.',
                background: '#0f172a',
                color: '#fff'
            });
            return;
        }

        icon.className = 'fa-solid fa-spinner fa-spin';
        btn.disabled = true;

        try {
            const res = await fetch(`<?= base_url('crew/api_group_members') ?>?group_id=${encodeURIComponent(groupId)}`);
            const rawText = await res.text();
            let json;
            try {
                json = JSON.parse(rawText);
            } catch (parseErr) {
                throw new Error('Server mengembalikan respons tidak terduga.');
            }

            icon.className = 'fa-solid fa-users-viewfinder';
            btn.disabled = false;

            if (!json.success || !json.participants) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menarik Kontak',
                    text: json.message || 'Tidak dapat membaca kontak dari grup tersebut.',
                    background: '#0f172a',
                    color: '#fff'
                });
                return;
            }

            currentGroupMembers = json.participants;

            // Render stats
            document.getElementById('statTotalMembers').textContent = json.total;
            document.getElementById('statRegisteredMembers').textContent = json.registeredCount;
            document.getElementById('statNewMembers').textContent = json.unregisteredCount;
            document.getElementById('importStatsBar').classList.remove('hidden');
            document.getElementById('importTableContainer').classList.remove('hidden');

            renderMembersTable();

        } catch (err) {
            icon.className = 'fa-solid fa-users-viewfinder';
            btn.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Error Jaringan',
                text: err.message,
                background: '#0f172a',
                color: '#fff'
            });
        }
    }

    function renderMembersTable() {
        const tbody = document.getElementById('importMembersTableBody');
        const onlyUnregistered = document.getElementById('checkOnlyUnregistered').checked;
        const defaultRigId = document.getElementById('importTargetRigSelect').value;
        tbody.innerHTML = '';

        let shownCount = 0;

        currentGroupMembers.forEach((m, idx) => {
            if (onlyUnregistered && m.isRegistered) return;
            shownCount++;

            const tr = document.createElement('tr');
            tr.className = m.isRegistered ? 'bg-zinc-100/50 dark:bg-zinc-900/40 opacity-70' : 'hover:bg-zinc-50 dark:hover:bg-zinc-850 transition';

            // Auto NIK default draft
            const last4 = m.number.slice(-4);
            const defaultNik = 'CRW-' + last4 + '-' + (idx + 101);

            let posOptionsHtml = '';
            POSITIONS.forEach(p => {
                posOptionsHtml += `<option value="${p}">${p}</option>`;
            });

            tr.innerHTML = `
                <td class="px-3 py-2 text-center">
                    <input type="checkbox" class="crew-import-checkbox rounded border-zinc-300 dark:border-zinc-700 text-emerald-600 focus:ring-emerald-500" 
                        data-index="${idx}" ${m.isRegistered ? 'disabled' : 'checked'} onchange="updateSelectionCounter()">
                </td>
                <td class="px-3 py-2">
                    <div class="flex items-center space-x-1.5 font-mono text-xs">
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">${m.phoneFormatted}</span>
                        <button type="button" onclick="copySinglePhone('${m.phoneFormatted}')" title="Salin nomor" class="text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition p-0.5">
                            <i class="fa-regular fa-copy text-[11px]"></i>
                        </button>
                    </div>
                    ${m.isAdmin ? '<span class="text-[9px] text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wider block">Admin Grup</span>' : ''}
                </td>
                <td class="px-3 py-2">
                    ${m.isRegistered 
                        ? `<span class="text-zinc-500 dark:text-zinc-400 font-medium">${m.existingCrew.name} <small class="text-zinc-400 dark:text-zinc-500 block text-[10px]">${m.existingCrew.position} &bull; ${m.existingCrew.rig_name}</small></span>`
                        : `<input type="text" id="crew_name_${idx}" placeholder="Ketik nama personil..." 
                            class="w-full px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:border-emerald-500 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-1 focus:ring-emerald-500 focus:outline-none">`
                    }
                </td>
                <td class="px-3 py-2">
                    ${m.isRegistered 
                        ? `<span class="text-zinc-500 dark:text-zinc-400">${m.existingCrew.position}</span>`
                        : `<select id="crew_pos_${idx}" class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                            ${posOptionsHtml}
                           </select>`
                    }
                </td>
                <td class="px-3 py-2">
                    ${m.isRegistered 
                        ? `<span class="text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">${m.existingCrew.nik}</span>`
                        : `<input type="text" id="crew_nik_${idx}" value="${defaultNik}" 
                            class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs font-mono text-zinc-700 dark:text-zinc-300 focus:outline-none">`
                    }
                </td>
                <td class="px-3 py-2 text-center">
                    ${m.isRegistered
                        ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Terdaftar</span>`
                        : `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Baru</span>`
                    }
                </td>
            `;

            tbody.appendChild(tr);
        });

        if (shownCount === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400 text-xs">Semua anggota di grup ini sudah terdaftar di sistem.</td></tr>`;
        }

        updateSelectionCounter();
    }


    function filterMembersTable() {
        renderMembersTable();
    }

    function toggleSelectAllImport(checked) {
        document.querySelectorAll('.crew-import-checkbox:not(:disabled)').forEach(cb => {
            cb.checked = checked;
        });
        updateSelectionCounter();
    }

    function updateSelectionCounter() {
        const checked = document.querySelectorAll('.crew-import-checkbox:checked:not(:disabled)');
        const count = checked.length;
        document.getElementById('importSelectionCount').textContent = `${count} personil dipilih untuk disimpan`;
        const btn = document.getElementById('btnSaveImportedCrews');
        btn.disabled = count === 0;
        document.getElementById('btnSaveText').textContent = count > 0 ? `Simpan ${count} Personil ke Master Data` : 'Simpan Personil ke Master Data';
    }

    function copySinglePhone(num) {
        navigator.clipboard.writeText(num).then(() => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                background: '#0f172a',
                color: '#fff'
            });
            Toast.fire({
                icon: 'success',
                title: `Nomor ${num} disalin!`
            });
        });
    }

    function copyAllPhoneNumbers() {
        if (!currentGroupMembers || currentGroupMembers.length === 0) return;
        const onlyUnregistered = document.getElementById('checkOnlyUnregistered').checked;
        
        const list = currentGroupMembers
            .filter(m => !onlyUnregistered || !m.isRegistered)
            .map(m => m.phoneFormatted);

        if (list.length === 0) {
            Swal.fire({ icon: 'info', title: 'Tidak Ada Nomor', text: 'Tidak ada nomor yang sesuai filter.', background: '#0f172a', color: '#fff' });
            return;
        }

        const text = list.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            const btnText = document.getElementById('copyBtnText');
            btnText.textContent = `${list.length} Nomor Tersalin!`;
            setTimeout(() => { btnText.textContent = 'Salin Semua Nomor'; }, 2000);

            Swal.fire({
                icon: 'success',
                title: `${list.length} Nomor Berhasil Disalin`,
                text: 'Daftar nomor telah dicopy ke clipboard. Siap ditempel di Excel / Notepad.',
                background: '#0f172a',
                color: '#fff'
            });
        });
    }

    async function saveImportedCrews() {
        const checkedBoxes = Array.from(document.querySelectorAll('.crew-import-checkbox:checked:not(:disabled)'));
        if (checkedBoxes.length === 0) return;

        const defaultRigId = document.getElementById('importTargetRigSelect').value;
        const payloadCrews = [];
        let hasEmptyName = false;

        checkedBoxes.forEach(cb => {
            const idx = cb.getAttribute('data-index');
            const member = currentGroupMembers[idx];
            const nameInput = document.getElementById(`crew_name_${idx}`);
            const nameVal = nameInput ? nameInput.value.trim() : '';

            if (!nameVal) {
                hasEmptyName = true;
                nameInput.classList.add('ring-2', 'ring-rose-500');
                nameInput.focus();
            }

            const posVal = document.getElementById(`crew_pos_${idx}`)?.value || 'Crew Lapangan';
            const nikVal = document.getElementById(`crew_nik_${idx}`)?.value.trim() || '';

            payloadCrews.push({
                nik: nikVal,
                name: nameVal,
                position: posVal,
                rig_id: defaultRigId,
                phone: member.number
            });
        });

        if (hasEmptyName) {
            Swal.fire({
                icon: 'warning',
                title: 'Nama Personil Wajib Diisi',
                text: 'Harap lengkapi nama personil yang dicentang sebelum menyimpan.',
                background: '#0f172a',
                color: '#fff'
            });
            return;
        }

        // Tampilkan konfirmasi simpan
        const confirm = await Swal.fire({
            title: `Simpan ${payloadCrews.length} Personil?`,
            text: `Data crew akan otomatis didaftarkan ke Master Data Unit Rig yang dipilih.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan Semua',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#059669',
            background: '#0f172a',
            color: '#fff'
        });

        if (!confirm.isConfirmed) return;

        Swal.fire({
            title: 'Menyimpan Personil...',
            text: 'Mohon tunggu sebentar...',
            allowOutsideClick: false,
            background: '#0f172a',
            color: '#fff',
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const res = await fetch('<?= base_url('crew/save_batch_imported') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ crews: payloadCrews })
            });

            const json = await res.json();
            if (json.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: json.message || `${payloadCrews.length} personil berhasil ditambahkan ke sistem.`,
                    background: '#0f172a',
                    color: '#fff'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: json.message || 'Terjadi kesalahan saat menyimpan data.',
                    background: '#0f172a',
                    color: '#fff'
                });
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: err.message,
                background: '#0f172a',
                color: '#fff'
            });
        }
    }

    function confirmDelete(id, name) {
        confirmAction('Hapus Data Crew?', `Apakah Anda yakin ingin menghapus data crew "${name}"? Tindakan ini tidak dapat dibatalkan.`, 'Ya, Hapus Data')
        .then(result => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('crew/delete/') ?>/${id}`;
            }
        });
    }
</script>
