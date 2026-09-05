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
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Nama / Judul Meeting <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="meetingTitle" required 
                            placeholder="Contoh: Pre-Tour Safety & Operational Review Rig 01"
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Topik &amp; Agenda Pembahasan</label>
                        <textarea name="topic" rows="2" placeholder="Tuliskan agenda rapat, target drilling, dan prosedur keselamatan..."
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Unit Rig <span class="text-rose-500">*</span></label>
                        <select name="rig_id" id="rigSelect" required onchange="loadCrewsForRig(this.value)"
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                            <option value="">-- Pilih Unit Rig --</option>
                            <?php foreach ($rigs as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Penanggung Jawab (PJ Rig Meeting)</label>
                        <select name="pj_crew_id" id="pjSelect"
                            class="w-full px-3 py-2 rounded-lg border text-sm transition
                                bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                            <option value="">-- Pilih PJ Rig (Default) --</option>
                            <?php foreach ($crews as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['position']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
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
        const title = document.getElementById('meetingTitle').value || 'rig_meeting';
        const cleanTitle = encodeURIComponent(title.toLowerCase().replace(/[^a-z0-9]/g, '_'));
        const randomId = Math.random().toString(36).substring(2, 9);
        const link = `https://teams.microsoft.com/l/meetup-join/19%3ameeting_${cleanTitle}_${randomId}%40thread.v2/0?context=%7b%22Tid%22%3a%22besmindo-group%22%7d`;
        document.getElementById('teamsLinkInput').value = link;
    }

    function loadCrewsForRig(rigId) {
        if (!rigId) {
            document.getElementById('crewCheckboxContainer').innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-zinc-400">Silakan pilih Unit Rig di atas.</div>';
            return;
        }

        fetch(`<?= base_url('meeting/get_crews_by_rig_json') ?>/${rigId}`)
            .then(r => r.json())
            .then(crews => {
                const container = document.getElementById('crewCheckboxContainer');
                if (crews.length === 0) {
                    container.innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-amber-500">Belum ada personil crew aktif di rig ini.</div>';
                    return;
                }

                let html = '';
                crews.forEach(c => {
                    html += `
                        <label class="flex items-center space-x-2.5 p-2.5 rounded-lg border cursor-pointer transition
                            bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <input type="checkbox" name="crew_ids[]" value="${c.id}" checked class="crew-checkbox w-4 h-4 text-sky-600 rounded border-zinc-300 dark:border-zinc-700 focus:ring-sky-500">
                            <div class="truncate">
                                <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100 block truncate">${c.name}</span>
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate block">${c.position}</span>
                            </div>
                        </label>
                    `;
                });
                container.innerHTML = html;
            });
    }

    function toggleAllCrews(checked) {
        document.querySelectorAll('.crew-checkbox').forEach(cb => cb.checked = checked);
    }
</script>

