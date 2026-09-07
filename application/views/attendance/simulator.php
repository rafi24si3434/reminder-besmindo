<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-file-excel text-2xl text-emerald-500"></i>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Import &amp; Analisis Kehadiran Microsoft Teams</h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Unggah file laporan Teams (.xlsx atau .csv) untuk analisis otomatis kehadiran crew Pre-Hitch Meeting</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= base_url('attendance/live' . ($meeting ? '/' . $meeting['id'] : '')) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition">
                <i class="fa-solid fa-desktop text-sky-500"></i>
                <span>Buka Live Attendance</span>
            </a>
            <a href="<?= base_url('attendance/rekap' . ($meeting ? '/' . $meeting['id'] : '')) ?>" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-lg bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold shadow-xs transition">
                <i class="fa-solid fa-clipboard-check text-emerald-500"></i>
                <span>Rekap Absensi</span>
            </a>
        </div>
    </div>

    <!-- Meeting Switcher Bar -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs">
        <form method="GET" action="<?= base_url('attendance/simulator') ?>" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Pilih Jadwal Meeting:</label>
                <?php $this->load->view('components/meeting_switcher', array(
                    'meetings'        => $meetings,
                    'current_meeting' => $meeting,
                    'target_route'    => 'attendance/simulator?meeting_id=',
                    'mode'            => 'query_param'
                )); ?>
            </div>
            <?php if ($meeting): ?>
                <div class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                        <?= htmlspecialchars($meeting['rig_name']) ?>
                    </span>
                    <span>&bull;</span>
                    <span><?= date('d M Y', strtotime($meeting['meeting_date'])) ?> <?= substr($meeting['start_time'], 0, 5) ?> WIB</span>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Top Grid: Upload & Analysis Zone (Left) + Quick Simulator (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Upload & Analysis Form (Col 8) -->
        <div class="lg:col-span-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4">
            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                        <i class="fa-solid fa-cloud-arrow-up text-emerald-500"></i>
                        <span>Upload File Laporan Microsoft Teams (.xlsx / .csv)</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Sistem akan membaca nama-nama peserta dari file dan menganalisis kecocokannya dengan daftar crew meeting</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    Smart AI Match
                </span>
            </div>

            <form id="formAnalyzeTeams" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="meeting_id" id="targetMeetingId" value="<?= $meeting['id'] ?? '' ?>">

                <div id="dropZone" class="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-xl p-8 text-center cursor-pointer transition bg-zinc-50/50 dark:bg-zinc-950/40">
                    <div class="space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="fa-solid fa-file-excel text-2xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100" id="fileLabel">Pilih atau Drag &amp; Drop File Teams (.xlsx / .csv)</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">File attendance report resmi hasil download dari Microsoft Teams meeting</p>
                        <input type="file" name="teams_file" id="teamsFileInput" accept=".xlsx, .xls, .csv" required class="hidden">
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400 flex items-center space-x-2">
                        <i class="fa-solid fa-circle-info text-sky-500"></i>
                        <span>Kolom yang dideteksi otomatis: Nama / Peserta, Join Time, &amp; Durasi.</span>
                    </div>

                    <button type="button" id="btnAnalyze" onclick="executeAnalysis()" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition flex items-center space-x-2">
                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                        <span id="btnAnalyzeText">Mulai Analisis Kehadiran</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Manual / Simulator (Col 4) -->
        <div class="lg:col-span-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center space-x-2">
                        <i class="fa-solid fa-user-check text-sky-500"></i>
                        <span>Simulasi Cepat Masuk Hadir</span>
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Uji coba presensi perorangan secara instan</p>
                </div>

                <?php if ($meeting && !empty($attendances)): ?>
                    <div class="space-y-2 mt-3 max-h-56 overflow-y-auto pr-1 scrollbar-thin">
                        <?php foreach (array_slice($attendances, 0, 6) as $att): ?>
                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 rounded-lg flex items-center justify-between gap-2 text-xs">
                                <div class="truncate">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100 truncate"><?= htmlspecialchars($att['crew_name']) ?></div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate"><?= htmlspecialchars($att['position']) ?></div>
                                </div>
                                <button type="button" onclick="runSim(<?= $att['id'] ?>)" class="px-2.5 py-1 rounded bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-500/20 flex-shrink-0 transition">
                                    <i class="fa-solid fa-check mr-1"></i> Hadir
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-xs text-zinc-500 dark:text-zinc-400">
                        Tidak ada personil crew.
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 text-[11px] text-zinc-500 dark:text-zinc-400">
                <span>Status kehadiran yang berlaku: <strong>HADIR, IZIN, TIDAK HADIR</strong></span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- INTERACTIVE ANALYSIS RESULTS CONTAINER (Shown after file analyzed)        -->
    <!-- ========================================================================= -->
    <div id="analysisResultSection" class="hidden space-y-5">

        <!-- Header Hasil Analisis -->
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300 flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                    <span>Hasil Analisis File: <span id="resFileName" class="font-mono underline"></span></span>
                </h3>
                <p class="text-xs text-emerald-700/80 dark:text-emerald-400/80 mt-0.5">
                    Target Meeting: <strong id="resMeetingTitle"></strong> (<span id="resMeetingRig"></span>)
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-white dark:bg-zinc-900 px-3 py-1.5 rounded-lg border border-emerald-500/30">
                    Tingkat Kehadiran: <span id="resPercentage" class="text-emerald-600 dark:text-emerald-400">0%</span>
                </span>
            </div>
        </div>

        <!-- 4 Kartu Metrik Analisis -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs text-center">
                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Crew Diundang</span>
                <span class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 block mt-1" id="statExpected">0</span>
                <span class="text-[10px] text-zinc-400">Peserta Roster</span>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-emerald-500/30 rounded-xl p-4 shadow-xs text-center">
                <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Cocok / Hadir</span>
                <span class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 block mt-1" id="statMatched">0</span>
                <span class="text-[10px] text-emerald-600/70">Teridentifikasi di File</span>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-rose-500/30 rounded-xl p-4 shadow-xs text-center">
                <span class="text-[11px] font-medium text-rose-600 dark:text-rose-400 uppercase tracking-wider block">Belum Hadir / Alpha</span>
                <span class="text-2xl font-bold tracking-tight text-rose-600 dark:text-rose-400 block mt-1" id="statUnmatched">0</span>
                <span class="text-[10px] text-rose-600/70">Tidak Ada di File</span>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-xs text-center">
                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Peserta Eksternal</span>
                <span class="text-2xl font-bold tracking-tight text-zinc-700 dark:text-zinc-300 block mt-1" id="statExternal">0</span>
                <span class="text-[10px] text-zinc-400">Tamu / Tak Dikenal</span>
            </div>
        </div>

        <!-- Tabbed Details -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xs overflow-hidden">
            <!-- Tabs Nav -->
            <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 border-b border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="switchAnalysisTab('matched')" id="tabBtnMatched" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Crew Terdeteksi Hadir (<span id="tabCountMatched">0</span>)</span>
                    </button>
                    <button type="button" onclick="switchAnalysisTab('unmatched')" id="tabBtnUnmatched" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-circle-xmark text-rose-500"></i>
                        <span>Belum Hadir (<span id="tabCountUnmatched">0</span>)</span>
                    </button>
                    <button type="button" onclick="switchAnalysisTab('unrecognized')" id="tabBtnUnrecognized" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-circle-question text-zinc-400"></i>
                        <span>Peserta Eksternal (<span id="tabCountUnrecognized">0</span>)</span>
                    </button>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Aksi Personil Belum Hadir:</span>
                    <select id="unmatchedActionSelect" class="px-2.5 py-1 text-xs bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg text-zinc-900 dark:text-zinc-100">
                        <option value="leave_as_is">Tetap status BELUM_HADIR</option>
                        <option value="mark_alpha">Tandai TIDAK_HADIR (Alpha)</option>
                    </select>
                    <button type="button" id="btnApplyAttendance" onclick="applyAttendanceToDB()" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan ke Database</span>
                    </button>
                </div>
            </div>

            <!-- Tab Content 1: Matched Attendees -->
            <div id="tabContentMatched" class="overflow-x-auto max-h-96 scrollbar-thin">
                <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400 uppercase font-semibold text-[10px] tracking-wider border-b border-zinc-200 dark:border-zinc-800 sticky top-0">
                        <tr>
                            <th class="px-4 py-2.5">No</th>
                            <th class="px-4 py-2.5">Nama Crew (Database)</th>
                            <th class="px-4 py-2.5">Jabatan &amp; Grup</th>
                            <th class="px-4 py-2.5">Nama Terdeteksi di File</th>
                            <th class="px-4 py-2.5 text-center">Metode Cocok</th>
                            <th class="px-4 py-2.5 text-center">Waktu Join</th>
                            <th class="px-4 py-2.5 text-center">Durasi</th>
                            <th class="px-4 py-2.5 text-center">Status Baru</th>
                        </tr>
                    </thead>
                    <tbody id="matchedTableBody" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <!-- Filled dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Tab Content 2: Unmatched Crew (Belum Hadir) -->
            <div id="tabContentUnmatched" class="hidden overflow-x-auto max-h-96 scrollbar-thin">
                <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400 uppercase font-semibold text-[10px] tracking-wider border-b border-zinc-200 dark:border-zinc-800 sticky top-0">
                        <tr>
                            <th class="px-4 py-2.5">No</th>
                            <th class="px-4 py-2.5">Nama Personil</th>
                            <th class="px-4 py-2.5">NIK</th>
                            <th class="px-4 py-2.5">Jabatan</th>
                            <th class="px-4 py-2.5">Grup</th>
                            <th class="px-4 py-2.5">No. WhatsApp</th>
                            <th class="px-4 py-2.5 text-center">Status Sekarang</th>
                            <th class="px-4 py-2.5 text-right">Aksi Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody id="unmatchedTableBody" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <!-- Filled dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Tab Content 3: Unrecognized Rows (Eksternal / Tamu) -->
            <div id="tabContentUnrecognized" class="hidden overflow-x-auto max-h-96 scrollbar-thin">
                <table class="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400 uppercase font-semibold text-[10px] tracking-wider border-b border-zinc-200 dark:border-zinc-800 sticky top-0">
                        <tr>
                            <th class="px-4 py-2.5">No</th>
                            <th class="px-4 py-2.5">Nama Peserta di File</th>
                            <th class="px-4 py-2.5 text-center">Waktu Join</th>
                            <th class="px-4 py-2.5 text-center">Durasi</th>
                            <th class="px-4 py-2.5">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="unrecognizedTableBody" class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        <!-- Filled dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<script>
    let currentAnalysisData = null;

    // File input trigger
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('teamsFileInput');
    const fileLabel = document.getElementById('fileLabel');

    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileLabel.innerHTML = `<i class="fa-solid fa-file-circle-check text-emerald-500 mr-2"></i> ${fileInput.files[0].name}`;
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-emerald-500', 'bg-emerald-500/5');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-emerald-500', 'bg-emerald-500/5');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-emerald-500', 'bg-emerald-500/5');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileLabel.innerHTML = `<i class="fa-solid fa-file-circle-check text-emerald-500 mr-2"></i> ${e.dataTransfer.files[0].name}`;
            }
        });
    }

    // Execute Analysis via AJAX
    function executeAnalysis() {
        const meetingId = document.getElementById('targetMeetingId').value;
        if (!meetingId) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan pilih jadwal meeting terlebih dahulu.' });
            return;
        }

        if (!fileInput.files || fileInput.files.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Pilih File', text: 'Silakan pilih file (.xlsx atau .csv) terlebih dahulu.' });
            return;
        }

        const formData = new FormData();
        formData.append('meeting_id', meetingId);
        formData.append('teams_file', fileInput.files[0]);

        const btn = document.getElementById('btnAnalyze');
        const btnText = document.getElementById('btnAnalyzeText');
        const originalText = btnText.innerHTML;

        btn.disabled = true;
        btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menganalisis...';

        fetch('<?= base_url('attendance/analyze_teams_file') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btnText.innerHTML = originalText;

            if (!res.success) {
                Swal.fire({ icon: 'error', title: 'Gagal Analisis', text: res.message });
                return;
            }

            currentAnalysisData = res;
            renderAnalysisResults(res);
        })
        .catch(err => {
            btn.disabled = false;
            btnText.innerHTML = originalText;
            Swal.fire({ icon: 'error', title: 'Error', text: err.toString() });
        });
    }

    // Render results into UI
    function renderAnalysisResults(data) {
        document.getElementById('analysisResultSection').classList.remove('hidden');

        document.getElementById('resFileName').innerText = data.fileName;
        document.getElementById('resMeetingTitle').innerText = data.meeting.title;
        document.getElementById('resMeetingRig').innerText = data.meeting.rig_name;
        document.getElementById('resPercentage').innerText = data.summary.percentage + '%';

        document.getElementById('statExpected').innerText = data.summary.total_expected;
        document.getElementById('statMatched').innerText = data.summary.total_found;
        document.getElementById('statUnmatched').innerText = data.summary.total_unmatched;
        document.getElementById('statExternal').innerText = data.summary.total_external;

        document.getElementById('tabCountMatched').innerText = data.matched.length;
        document.getElementById('tabCountUnmatched').innerText = data.unmatched.length;
        document.getElementById('tabCountUnrecognized').innerText = data.unrecognized.length;

        // Render Matched Table
        const mBody = document.getElementById('matchedTableBody');
        mBody.innerHTML = '';
        if (data.matched.length === 0) {
            mBody.innerHTML = '<tr><td colspan="8" class="p-4 text-center text-zinc-500">Tidak ada crew yang cocok di file.</td></tr>';
        } else {
            data.matched.forEach((m, idx) => {
                mBody.innerHTML += `
                    <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-2.5 font-mono text-zinc-400">${idx + 1}</td>
                        <td class="px-4 py-2.5">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">${escapeHtml(m.crew_name)}</div>
                            <div class="text-[10px] text-zinc-400 font-mono">${escapeHtml(m.nik || '-')}</div>
                        </td>
                        <td class="px-4 py-2.5">
                            <span>${escapeHtml(m.position)}</span>
                            <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">Grup ${m.group_code}</span>
                        </td>
                        <td class="px-4 py-2.5 font-mono text-zinc-800 dark:text-zinc-200">
                            ${escapeHtml(m.file_name)}
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                ${escapeHtml(m.method)}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center font-mono text-zinc-600 dark:text-zinc-300">
                            ${escapeHtml(m.join_time)}
                        </td>
                        <td class="px-4 py-2.5 text-center text-zinc-600 dark:text-zinc-300">
                            ${m.duration_minutes} Menit
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                <i class="fa-solid fa-check mr-1"></i> HADIR
                            </span>
                        </td>
                    </tr>
                `;
            });
        }

        // Render Unmatched Table
        const uBody = document.getElementById('unmatchedTableBody');
        uBody.innerHTML = '';
        if (data.unmatched.length === 0) {
            uBody.innerHTML = '<tr><td colspan="8" class="p-4 text-center text-emerald-600 font-medium">Luar biasa! Seluruh peserta yang diundang hadir dalam rapat ini.</td></tr>';
        } else {
            data.unmatched.forEach((u, idx) => {
                const urgentMsg = `Halo ${u.crew_name}, Meeting ${data.meeting.title} telah berlangsung. Mohon segera bergabung di Microsoft Teams.`;
                uBody.innerHTML += `
                    <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-2.5 font-mono text-zinc-400">${idx + 1}</td>
                        <td class="px-4 py-2.5 font-semibold text-zinc-900 dark:text-zinc-100">${escapeHtml(u.crew_name)}</td>
                        <td class="px-4 py-2.5 font-mono text-zinc-400 text-[10px]">${escapeHtml(u.nik || '-')}</td>
                        <td class="px-4 py-2.5 text-zinc-600 dark:text-zinc-300">${escapeHtml(u.position)}</td>
                        <td class="px-4 py-2.5">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">Grup ${u.group_code}</span>
                        </td>
                        <td class="px-4 py-2.5 font-mono text-zinc-500">${escapeHtml(u.phone || '-')}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                ${escapeHtml(u.current_status)}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <button type="button" onclick="openWhatsApp('${u.phone}', '${escapeJs(urgentMsg)}')" class="px-2.5 py-1 rounded bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 text-[10px] font-semibold border border-emerald-500/20 transition">
                                <i class="fa-brands fa-whatsapp mr-1"></i> WA Darurat
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        // Render Unrecognized Table
        const exBody = document.getElementById('unrecognizedTableBody');
        exBody.innerHTML = '';
        if (data.unrecognized.length === 0) {
            exBody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-zinc-400">Tidak ada peserta eksternal tak dikenal.</td></tr>';
        } else {
            data.unrecognized.forEach((ex, idx) => {
                exBody.innerHTML += `
                    <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-2.5 font-mono text-zinc-400">${idx + 1}</td>
                        <td class="px-4 py-2.5 font-semibold text-zinc-900 dark:text-zinc-100">${escapeHtml(ex.raw_name)}</td>
                        <td class="px-4 py-2.5 text-center font-mono text-zinc-500">${escapeHtml(ex.join_time)}</td>
                        <td class="px-4 py-2.5 text-center text-zinc-500">${ex.duration_minutes} Menit</td>
                        <td class="px-4 py-2.5 text-zinc-400 italic text-[11px]">Tidak cocok dengan personil kru rig yang dijadwalkan (Tamu/Vendor luar)</td>
                    </tr>
                `;
            });
        }

        // Scroll smoothly to results
        document.getElementById('analysisResultSection').scrollIntoView({ behavior: 'smooth' });
    }

    // Tab switcher
    function switchAnalysisTab(tab) {
        document.getElementById('tabContentMatched').classList.add('hidden');
        document.getElementById('tabContentUnmatched').classList.add('hidden');
        document.getElementById('tabContentUnrecognized').classList.add('hidden');

        document.getElementById('tabBtnMatched').className = 'tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition flex items-center space-x-1.5';
        document.getElementById('tabBtnUnmatched').className = 'tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition flex items-center space-x-1.5';
        document.getElementById('tabBtnUnrecognized').className = 'tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition flex items-center space-x-1.5';

        if (tab === 'matched') {
            document.getElementById('tabContentMatched').classList.remove('hidden');
            document.getElementById('tabBtnMatched').className = 'tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white transition flex items-center space-x-1.5';
        } else if (tab === 'unmatched') {
            document.getElementById('tabContentUnmatched').classList.remove('hidden');
            document.getElementById('tabBtnUnmatched').className = 'tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-600 text-white transition flex items-center space-x-1.5';
        } else {
            document.getElementById('tabContentUnrecognized').classList.remove('hidden');
            document.getElementById('tabBtnUnrecognized').className = 'tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-700 text-white transition flex items-center space-x-1.5';
        }
    }

    // Apply presence to database
    function applyAttendanceToDB() {
        if (!currentAnalysisData || !currentAnalysisData.matched) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tidak ada data presensi untuk disimpan.' });
            return;
        }

        const unmatchedAction = document.getElementById('unmatchedActionSelect').value;

        Swal.fire({
            title: 'Terapkan Presensi Kehadiran?',
            text: `Akan menyimpan status HADIR untuk ${currentAnalysisData.matched.length} personil ke database. Tindakan unpresent: ${unmatchedAction === 'mark_alpha' ? 'Tandai Alpha' : 'Biarkan pending'}.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Terapkan Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#059669'
        }).then(result => {
            if (result.isConfirmed) {
                const btn = document.getElementById('btnApplyAttendance');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...';

                const payload = new URLSearchParams();
                payload.append('meeting_id', currentAnalysisData.meeting.id);
                payload.append('matched_data', JSON.stringify(currentAnalysisData.matched));
                payload.append('unmatched_action', unmatchedAction);

                fetch('<?= base_url('attendance/apply_teams_attendance') ?>', {
                    method: 'POST',
                    body: payload,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(res => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i><span>Simpan ke Database</span>';

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Disimpan!',
                            text: res.message,
                            confirmButtonColor: '#059669'
                        }).then(() => {
                            window.location.href = '<?= base_url('attendance/live/') ?>' + currentAnalysisData.meeting.id;
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i><span>Simpan ke Database</span>';
                    Swal.fire({ icon: 'error', title: 'Error', text: err.toString() });
                });
            }
        });
    }

    // Quick Simulation function
    function runSim(attId) {
        fetch('<?= base_url('attendance/simulate_join') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `attendance_id=${attId}&join_type=on_time`
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Presensi Tercatat',
                    text: res.message,
                    timer: 1000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function escapeJs(text) {
        if (!text) return '';
        return String(text).replace(/'/g, "\\'").replace(/"/g, '\\"');
    }
</script>
