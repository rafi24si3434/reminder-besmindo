<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Buat Jadwal Meeting Rig Baru</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Atur jadwal meeting rutin mingguan atau koordinasi harian untuk crew rig</p>
        </div>
        <a href="<?= base_url('meeting') ?>"
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
        <form action="<?= base_url('meeting/save') ?>" method="POST" class="space-y-6">
            <div>
                <h3 class="text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Informasi &amp; Topik Meeting</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Nama / Judul Pre-Hitch Meeting <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="meetingTitle" required 
                            placeholder="Contoh: Pre-Hitch Safety & Operations Meeting Rig 01 - Grup A"
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                    </div>

                    <!-- Mode Meeting: Single Rig vs Joint Meeting -->
                    <div class="sm:col-span-2 p-3.5 rounded-xl border bg-zinc-50 dark:bg-zinc-950/60 border-zinc-200 dark:border-zinc-800">
                        <label class="block text-xs font-bold text-zinc-900 dark:text-zinc-100 mb-2">Tipe Pelaksanaan Rapat:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center space-x-3 p-2.5 rounded-lg border cursor-pointer transition bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
                                <input type="radio" name="is_joint" value="0" checked onchange="toggleJointMode(false)" class="w-4 h-4 text-sky-600 focus:ring-sky-500">
                                <div>
                                    <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">Single Rig Meeting</span>
                                    <span class="text-[11px] text-zinc-500">1 Unit Rig &amp; 1 Grup terpilih (~40 crew)</span>
                                </div>
                            </label>
                            <label class="flex items-center space-x-3 p-2.5 rounded-lg border cursor-pointer transition bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
                                <input type="radio" name="is_joint" value="1" onchange="toggleJointMode(true)" class="w-4 h-4 text-sky-600 focus:ring-sky-500">
                                <div>
                                    <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block flex items-center space-x-1.5">
                                        <span>Joint Meeting (Rapat Gabungan)</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] bg-sky-500 text-white font-bold">Multi-Rig</span>
                                    </span>
                                    <span class="text-[11px] text-zinc-500">Gabungan beberapa Rig/Grup dalam 1 ruangan Teams</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Single Rig Selector -->
                    <div id="singleRigBox">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Unit Rig <span class="text-rose-500">*</span></label>
                        <select name="rig_id" id="rigSelect" onchange="refreshCrewsList()"
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                            <option value="">-- Pilih Unit Rig --</option>
                            <?php foreach ($rigs as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Target Grup (A, B, C) -->
                    <div id="groupTargetBox">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Grup Crew Terundang <span class="text-rose-500">*</span></label>
                        <select name="group_target" id="groupTargetSelect" onchange="refreshCrewsList()"
                            class="w-full px-3 py-2 rounded-lg border text-sm font-semibold transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                            <option value="A" selected>Grup A (Shift A)</option>
                            <option value="B">Grup B (Shift B)</option>
                            <option value="C">Grup C (Shift C)</option>
                            <option value="ALL">Semua Grup (A, B, C)</option>
                        </select>
                    </div>

                    <!-- Joint Meeting Selector Matrix (Hidden by default) -->
                    <div id="jointTargetsBox" class="sm:col-span-2 hidden p-4 rounded-xl border bg-sky-500/5 border-sky-500/20">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-sky-700 dark:text-sky-300 flex items-center space-x-1.5">
                                <i class="fa-solid fa-layer-group"></i>
                                <span>Pilih Kombinasi Rig &amp; Grup yang Mengikuti Rapat Gabungan:</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-zinc-500 mb-3">Centang grup dari rig mana saja yang akan meeting bersama dalam 1 link Teams:</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            <?php foreach ($rigs as $rg): ?>
                                <?php foreach (array('A', 'B', 'C') as $g): ?>
                                    <label class="flex items-center space-x-2 p-2 rounded-lg border cursor-pointer bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 hover:border-sky-400">
                                        <input type="checkbox" name="joint_targets[]" value="<?= $rg['id'] ?>_<?= $g ?>" onchange="refreshCrewsList()" class="joint-target-cb w-4 h-4 text-sky-600 rounded">
                                        <div class="truncate text-xs font-medium text-zinc-800 dark:text-zinc-200">
                                            <span><?= htmlspecialchars($rg['name']) ?></span> - <span class="font-bold text-sky-600">Grup <?= $g ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Sesi Pre-Hitch (Siang / Malam) -->
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Sesi Waktu Pre-Hitch <span class="text-rose-500">*</span></label>
                        <select name="session_time" id="sessionTimeSelect"
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                            <option value="SIANG">Sesi Siang (Pre-Hitch Day Shift)</option>
                            <option value="MALAM">Sesi Malam (Pre-Hitch Night Shift)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Penanggung Jawab (PJ Meeting)</label>
                        <select name="pj_crew_id" id="pjSelect"
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                            <option value="">-- Pilih PJ Rig (Default) --</option>
                            <?php foreach ($crews as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['position']) ?> - <?= htmlspecialchars($c['rig_name'] ?: 'Rig') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Topik &amp; Agenda Pembahasan Pre-Hitch</label>
                        <textarea name="topic" rows="2" placeholder="Tuliskan agenda keselamatan, instruksi kerja mingguan, dan target operasional..."
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500"></textarea>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <i class="fa-regular fa-clock"></i>
                    <span>Waktu Pertemuan &amp; Media Microsoft Teams</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Tanggal Meeting <span class="text-rose-500">*</span></label>
                        <input type="date" name="meeting_date" value="<?= date('Y-m-d') ?>" required 
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" value="08:30" required 
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                        <input type="time" name="end_time" value="09:30" required 
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                    </div>

                    <div class="sm:col-span-3">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Link Microsoft Teams</label>
                            <button type="button" onclick="generateTeamsLink()" class="text-xs text-sky-600 dark:text-sky-400 hover:underline flex items-center space-x-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i>
                                <span>Generate Link Otomatis</span>
                            </button>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-500 text-xs">
                                <i class="fa-brands fa-microsoft"></i>
                            </div>
                            <input type="url" name="teams_link" id="teamsLinkInput" 
                                placeholder="https://teams.microsoft.com/l/meetup-join/..."
                                class="w-full pl-9 pr-3 py-2 rounded-lg border text-sm transition
                                    bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                    text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                    focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3.5 rounded-lg border flex items-center justify-between
                    bg-zinc-50 dark:bg-zinc-800/40 border-zinc-200 dark:border-zinc-800">
                    <div>
                        <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100 block">Jadikan Meeting Rutin Mingguan</span>
                        <span class="text-[11px] text-zinc-500 dark:text-zinc-400">Jadwal akan berulang setiap pekan di hari yang sama</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_recurring" value="1"
                            class="w-4 h-4 text-sky-600 rounded border-zinc-300 dark:border-zinc-700 focus:ring-sky-500">
                    </label>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-3">
                    <h3 class="text-xs font-medium text-sky-600 dark:text-sky-400 uppercase tracking-wider flex items-center space-x-2">
                        <i class="fa-solid fa-users"></i>
                        <span>Daftar Peserta Crew Rig Terundang</span>
                    </h3>
                    <div class="flex items-center space-x-2 text-xs">
                        <button type="button" onclick="toggleAllCrews(true)" class="text-sky-600 dark:text-sky-400 hover:underline">Pilih Semua</button>
                        <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                        <button type="button" onclick="toggleAllCrews(false)" class="text-zinc-500 dark:text-zinc-400 hover:underline">Batalkan</button>
                    </div>
                </div>

                <div id="crewCheckboxContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 max-h-60 overflow-y-auto p-1">
                    <div class="sm:col-span-3 text-center py-6 text-xs text-zinc-400">
                        Silakan pilih <strong>Unit Rig</strong> di atas terlebih dahulu.
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end space-x-2.5">
                <a href="<?= base_url('meeting') ?>"
                    class="px-4 py-2 rounded-lg border text-xs font-medium transition
                        bg-zinc-100 hover:bg-zinc-200 text-zinc-700 border-zinc-200
                        dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-700">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 rounded-lg text-xs font-medium text-white shadow-sm transition flex items-center space-x-2
                        bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
                    <i class="fa-solid fa-calendar-check text-xs"></i>
                    <span>Simpan &amp; Jadwalkan Meeting</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function generateTeamsLink() {
        const title = document.getElementById('meetingTitle').value || 'pre_hitch_meeting';
        const cleanTitle = encodeURIComponent(title.toLowerCase().replace(/[^a-z0-9]/g, '_'));
        const randomId = Math.random().toString(36).substring(2, 9);
        const link = `https://teams.microsoft.com/l/meetup-join/19%3ameeting_${cleanTitle}_${randomId}%40thread.v2/0?context=%7b%22Tid%22%3a%22besmindo-group%22%7d`;
        document.getElementById('teamsLinkInput').value = link;
    }

    function toggleJointMode(isJoint) {
        const singleRigBox = document.getElementById('singleRigBox');
        const groupTargetBox = document.getElementById('groupTargetBox');
        const jointBox = document.getElementById('jointTargetsBox');
        const rigSelect = document.getElementById('rigSelect');

        if (isJoint) {
            singleRigBox.classList.add('hidden');
            groupTargetBox.classList.add('hidden');
            jointBox.classList.remove('hidden');
            rigSelect.removeAttribute('required');
            // Auto pick first rig as primary host if empty
            if (!rigSelect.value && rigSelect.options.length > 1) {
                rigSelect.value = rigSelect.options[1].value;
            }
        } else {
            singleRigBox.classList.remove('hidden');
            groupTargetBox.classList.remove('hidden');
            jointBox.classList.add('hidden');
            rigSelect.setAttribute('required', 'required');
        }
        refreshCrewsList();
    }

    function refreshCrewsList() {
        const isJoint = document.querySelector('input[name="is_joint"]:checked').value === "1";
        const container = document.getElementById('crewCheckboxContainer');

        if (isJoint) {
            const selectedBoxes = Array.from(document.querySelectorAll('.joint-target-cb:checked')).map(cb => cb.value);
            if (selectedBoxes.length === 0) {
                container.innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-amber-500"><i class="fa-solid fa-arrow-up mr-1"></i> Silakan centang minimal 1 kombinasi Rig &amp; Grup pada kotak di atas.</div>';
                return;
            }

            container.innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-zinc-400"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Memuat personil gabungan...</div>';

            const formData = new FormData();
            selectedBoxes.forEach(val => formData.append('targets[]', val));

            fetch(`<?= base_url('meeting/get_joint_crews_json') ?>`, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(crews => renderCrewCheckboxes(crews));

        } else {
            const rigId = document.getElementById('rigSelect').value;
            const groupCode = document.getElementById('groupTargetSelect').value;

            if (!rigId) {
                container.innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-zinc-400">Silakan pilih Unit Rig di atas.</div>';
                return;
            }

            container.innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-zinc-400"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Memuat daftar crew...</div>';

            fetch(`<?= base_url('meeting/get_crews_by_rig_json') ?>/${rigId}?group=${encodeURIComponent(groupCode)}`)
                .then(r => r.json())
                .then(crews => renderCrewCheckboxes(crews));
        }
    }

    function renderCrewCheckboxes(crews) {
        const container = document.getElementById('crewCheckboxContainer');
        if (!crews || crews.length === 0) {
            container.innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-amber-500">Tidak ada personil aktif pada rig &amp; grup yang dipilih.</div>';
            return;
        }

        let html = '';
        crews.forEach(c => {
            const rigLabel = c.rig_name ? c.rig_name : '';
            const grpLabel = c.group_code ? `Grup ${c.group_code}` : 'Grup A';

            html += `
                <label class="flex items-center space-x-2.5 p-2.5 rounded-lg border cursor-pointer transition
                    bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    <input type="checkbox" name="crew_ids[]" value="${c.id}" checked class="crew-checkbox w-4 h-4 text-sky-600 rounded border-zinc-300 dark:border-zinc-700 focus:ring-sky-500">
                    <div class="truncate flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100 truncate">${c.name}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 ml-1.5">${grpLabel}</span>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                            <span class="truncate">${c.position}</span>
                            ${rigLabel ? `<span class="truncate text-[9px] opacity-75 font-mono">${rigLabel}</span>` : ''}
                        </div>
                    </div>
                </label>
            `;
        });
        container.innerHTML = html;
    }

    function toggleAllCrews(checked) {
        document.querySelectorAll('.crew-checkbox').forEach(cb => cb.checked = checked);
    }
</script>

