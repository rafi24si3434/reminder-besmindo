<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Buat Jadwal Meeting Rig Baru</h1>
            <p class="text-xs text-slate-400 mt-1">Atur jadwal meeting rutin mingguan atau koordinasi harian untuk crew rig</p>
        </div>
        <a href="<?= base_url('meeting') ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <form action="<?= base_url('meeting/save') ?>" method="POST" class="space-y-6">
            <div>
                <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-slate-800 pb-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Informasi & Topik Meeting</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama / Judul Meeting <span class="text-rose-400">*</span></label>
                        <input type="text" name="title" id="meetingTitle" required 
                            placeholder="Contoh: Pre-Tour Safety & Operational Review Rig 01"
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Topik & Agenda Pembahasan</label>
                        <textarea name="topic" rows="2" placeholder="Tuliskan agenda rapat, target drilling, dan prosedur keselamatan..." class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none transition"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Unit Rig <span class="text-rose-400">*</span></label>
                        <select name="rig_id" id="rigSelect" required onchange="loadCrewsForRig(this.value)" class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                            <option value="">-- Pilih Unit Rig --</option>
                            <?php foreach ($rigs as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Penanggung Jawab (PJ Rig Meeting)</label>
                        <select name="pj_crew_id" id="pjSelect" class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                            <option value="">-- Pilih PJ Rig (Default) --</option>
                            <?php foreach ($crews as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['position']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-4 flex items-center space-x-2 border-b border-slate-800 pb-2">
                    <i class="fa-regular fa-clock"></i>
                    <span>Waktu Pertemuan & Media Microsoft Teams</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Tanggal Meeting <span class="text-rose-400">*</span></label>
                        <input type="date" name="meeting_date" value="<?= date('Y-m-d') ?>" required 
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Jam Mulai <span class="text-rose-400">*</span></label>
                        <input type="time" name="start_time" value="08:30" required 
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Jam Selesai <span class="text-rose-400">*</span></label>
                        <input type="time" name="end_time" value="09:30" required 
                            class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                    </div>

                    <div class="sm:col-span-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Link Microsoft Teams</label>
                            <button type="button" onclick="generateTeamsLink()" class="text-xs text-sky-400 hover:text-sky-300 flex items-center space-x-1">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                <span>Generate Link Otomatis</span>
                            </button>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400 text-sm">
                                <i class="fa-brands fa-microsoft"></i>
                            </div>
                            <input type="url" name="teams_link" id="teamsLinkInput" 
                                placeholder="https://teams.microsoft.com/l/meetup-join/..."
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3.5 bg-slate-800/40 border border-slate-700/60 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-white block">Jadikan Meeting Rutin Mingguan</span>
                        <span class="text-[11px] text-slate-400">Jadwal akan berulang setiap pekan di hari yang sama</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_recurring" value="1" class="w-4 h-4 text-sky-600 bg-slate-800 border-slate-700 rounded focus:ring-sky-500">
                    </label>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-2 mb-3">
                    <h3 class="text-xs font-bold text-sky-400 uppercase tracking-wider flex items-center space-x-2">
                        <i class="fa-solid fa-users"></i>
                        <span>Daftar Peserta Crew Rig Terundang</span>
                    </h3>
                    <div class="flex items-center space-x-2 text-xs">
                        <button type="button" onclick="toggleAllCrews(true)" class="text-sky-400 hover:underline">Pilih Semua</button>
                        <span class="text-slate-600">&bull;</span>
                        <button type="button" onclick="toggleAllCrews(false)" class="text-slate-400 hover:underline">Batalkan</button>
                    </div>
                </div>

                <div id="crewCheckboxContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 max-h-60 overflow-y-auto p-1 scrollbar-thin">
                    <div class="sm:col-span-3 text-center py-6 text-xs text-slate-500">
                        Silakan pilih <strong>Unit Rig</strong> di atas terlebih dahulu.
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="<?= base_url('meeting') ?>" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-lg shadow-sky-600/30 transition flex items-center space-x-2">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Simpan & Jadwalkan Meeting</span>
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
            document.getElementById('crewCheckboxContainer').innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-slate-500">Silakan pilih Unit Rig di atas.</div>';
            return;
        }

        fetch(`<?= base_url('meeting/get_crews_by_rig_json') ?>/${rigId}`)
            .then(r => r.json())
            .then(crews => {
                const container = document.getElementById('crewCheckboxContainer');
                if (crews.length === 0) {
                    container.innerHTML = '<div class="sm:col-span-3 text-center py-6 text-xs text-amber-400">Belum ada personil crew aktif di rig ini.</div>';
                    return;
                }

                let html = '';
                crews.forEach(c => {
                    html += `
                        <label class="flex items-center space-x-3 p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/60 hover:bg-slate-800 cursor-pointer transition">
                            <input type="checkbox" name="crew_ids[]" value="${c.id}" checked class="crew-checkbox w-4 h-4 text-sky-600 bg-slate-900 border-slate-700 rounded focus:ring-sky-500">
                            <div class="truncate">
                                <span class="text-xs font-bold text-white block truncate">${c.name}</span>
                                <span class="text-[10px] text-slate-400 truncate block">${c.position}</span>
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
