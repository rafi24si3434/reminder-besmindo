/**
 * Teams Auto-Sync Robot for PT. Besmindo Materi Sewatama
 * Script ini berjalan di tab browser Microsoft Teams (teams.microsoft.com)
 * Membaca roster peserta rapat dan mensinkronisasikan kehadiran ke server Besmindo secara live.
 */
(function() {
    // Hindari multiple instance
    if (window.__besmindoTeamsRobotRunning) {
        alert("🤖 Besmindo Teams Sync Robot sudah berjalan di tab ini!");
        return;
    }

    // Ambil konfigurasi dari query string atau global config
    const config = window.__besmindoConfig || {
        meetingId: 1,
        meetingTitle: "Meeting Rig Besmindo",
        apiUrl: "http://localhost/Project%20Besmindo%20Reminder/index.php/api/sync_teams_live",
        intervalSeconds: 10
    };

    window.__besmindoTeamsRobotRunning = true;
    let timerId = null;
    let isPaused = false;
    let syncCount = 0;

    // 1. Buat Floating HUD Widget
    const hud = document.createElement('div');
    hud.id = 'besmindo-teams-robot-hud';
    hud.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        width: 320px;
        background: rgba(15, 23, 42, 0.96);
        border: 1px solid rgba(56, 189, 248, 0.5);
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
        z-index: 9999999;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #f8fafc;
        font-size: 12px;
        backdrop-filter: blur(12px);
        overflow: hidden;
        user-select: none;
        transition: box-shadow 0.2s;
    `;

    hud.innerHTML = `
        <div id="besmindo-hud-header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 10px 14px; cursor: move; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 16px;">🤖</span>
                <div>
                    <strong style="font-size: 12px; font-weight: 800; letter-spacing: 0.5px; color: #fff; display: block;">BESMINDO SYNC ROBOT</strong>
                    <span style="font-size: 9px; color: #bae6fd; font-weight: 600;">Teams Live Attendance</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span id="besmindo-status-dot" style="width: 8px; height: 8px; border-radius: 999px; background: #4ade80; display: inline-block; box-shadow: 0 0 8px #4ade80;"></span>
                <button id="besmindo-close-btn" style="background: rgba(0,0,0,0.2); border: none; color: #fff; border-radius: 6px; width: 22px; height: 22px; cursor: pointer; font-size: 11px; display: flex; align-items: center; justify-content: center;">✕</button>
            </div>
        </div>

        <div style="padding: 12px 14px; display: flex; flex-direction: column; gap: 10px;">
            <div style="background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(51, 65, 85, 0.8); border-radius: 10px; padding: 8px 10px;">
                <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 2px;">Target Meeting:</div>
                <div style="font-weight: 700; color: #38bdf8; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="besmindo-meeting-title">${config.meetingTitle}</div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <div style="background: rgba(30, 41, 59, 0.6); border: 1px solid rgba(51, 65, 85, 0.6); border-radius: 8px; padding: 6px 8px; text-align: center;">
                    <div style="font-size: 9px; color: #94a3b8; font-weight: 600;">TERDETEKSI TEAMS</div>
                    <div id="besmindo-stat-detected" style="font-size: 16px; font-weight: 800; color: #fff; margin-top: 2px;">0</div>
                </div>
                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 6px 8px; text-align: center;">
                    <div style="font-size: 9px; color: #6ee7b7; font-weight: 600;">COCOK & HADIR</div>
                    <div id="besmindo-stat-matched" style="font-size: 16px; font-weight: 800; color: #34d399; margin-top: 2px;">0</div>
                </div>
            </div>

            <!-- Mini Log Console -->
            <div style="background: #020617; border: 1px solid rgba(30, 41, 59, 0.8); border-radius: 8px; padding: 6px 8px; height: 75px; overflow-y: auto; font-family: monospace; font-size: 10px; color: #cbd5e1; display: flex; flex-direction: column-reverse; gap: 3px;" id="besmindo-log-box">
                <div style="color: #38bdf8;">[READY] Robot aktif & mensinkronkan tiap ${config.intervalSeconds}s...</div>
            </div>

            <!-- Controls -->
            <div style="display: flex; gap: 6px;">
                <button id="besmindo-sync-now-btn" style="flex: 1; background: #0284c7; hover:background: #0369a1; border: none; color: #fff; padding: 7px 10px; border-radius: 8px; font-weight: 700; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px;">
                    <span>🔄</span> <span>Sync Sekarang</span>
                </button>
                <button id="besmindo-pause-btn" style="background: #334155; border: none; color: #cbd5e1; padding: 7px 10px; border-radius: 8px; font-weight: 600; font-size: 11px; cursor: pointer;">
                    ⏸️ Jeda
                </button>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 9px; color: #64748b; padding-top: 2px;">
                <span id="besmindo-last-sync">Belum pernah sync</span>
                <span>v2.0 &bull; PT Besmindo</span>
            </div>
        </div>
    `;

    document.body.appendChild(hud);

    // Draggable Functionality
    let isDragging = false;
    let dragStartX, dragStartY, initialLeft, initialTop;
    const header = document.getElementById('besmindo-hud-header');

    header.addEventListener('mousedown', function(e) {
        if (e.target.id === 'besmindo-close-btn') return;
        isDragging = true;
        dragStartX = e.clientX;
        dragStartY = e.clientY;
        const rect = hud.getBoundingClientRect();
        initialLeft = rect.left;
        initialTop = rect.top;
        e.preventDefault();
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        const deltaX = e.clientX - dragStartX;
        const deltaY = e.clientY - dragStartY;
        hud.style.left = (initialLeft + deltaX) + 'px';
        hud.style.top = (initialTop + deltaY) + 'px';
        hud.style.right = 'auto';
    });

    document.addEventListener('mouseup', function() {
        isDragging = false;
    });

    // Helper Log
    function addLog(msg, color = '#cbd5e1') {
        const box = document.getElementById('besmindo-log-box');
        if (!box) return;
        const d = new Date();
        const timeStr = d.toTimeString().split(' ')[0];
        const line = document.createElement('div');
        line.style.color = color;
        line.textContent = `[${timeStr}] ${msg}`;
        box.appendChild(line);
        box.scrollTop = box.scrollHeight;
    }

    // 2. Fungsi Pembaca Nama Peserta dari DOM Microsoft Teams
    function extractTeamsParticipants() {
        const names = new Set();

        // 1. Selector komprehensif untuk Microsoft Teams Web baru dan lama
        const selectors = [
            '[data-tid="roster-participant"]',
            '[data-tid="participant-name"]',
            '[data-tid="roster-item"]',
            '[data-tid="video-tile-name"]',
            'span[data-tid="user-name"]',
            '[data-tid="thread-user-profile-name"]',
            '.fui-PersonaName',
            'span[role="heading"]'
        ];

        selectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(el => {
                const txt = (el.innerText || el.textContent || '').trim().split('\n')[0].trim();
                if (txt && txt.length >= 2 && txt.length <= 50) {
                    names.add(txt);
                }
            });
        });

        // 2. Baca elemen list item dan aria-label
        document.querySelectorAll('li[role="listitem"], div[role="listitem"], [data-tid*="roster"]').forEach(li => {
            const aria = li.querySelector('[aria-label]') || li;
            const label = aria.getAttribute('aria-label') || '';
            if (label) {
                const clean = label.split(',')[0].trim().replace(/\s*\([^)]*\)/g, '').trim();
                if (clean && clean.length >= 2 && clean.length <= 50) {
                    names.add(clean);
                }
            }
        });

        // 3. Filter kata-kata sistem yang bukan nama orang
        const blacklisted = [
            'mute', 'bisuk', 'share', 'invite', 'peserta', 'attendee',
            'lobby', 'lobi', 'dalam rapat', 'in this meeting', 'waiting',
            'menunggu', 'video', 'audio', 'camera', 'kamera', 'raise', 'hand',
            'recording', 'rekam', 'transcription', 'transkrip'
        ];

        const cleanList = [];
        names.forEach(name => {
            const lower = name.toLowerCase();
            const isBlack = blacklisted.some(b => lower.includes(b));
            if (!isBlack && name.length >= 2 && name.length <= 50) {
                cleanList.push(name);
            }
        });

        return cleanList;
    }

    // 3. Fungsi Sinkronisasi ke Server Besmindo
    function executeSync() {
        if (isPaused) return;

        const participants = extractTeamsParticipants();
        document.getElementById('besmindo-stat-detected').textContent = participants.length;

        if (participants.length === 0) {
            addLog("Mencari peserta... Buka panel 'People' di Teams!", "#94a3b8");
            return;
        }

        fetch(config.apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                meeting_id: config.meetingId,
                names: participants
            })
        })
        .then(res => res.json())
        .then(data => {
            syncCount++;
            const d = new Date();
            document.getElementById('besmindo-last-sync').textContent = 'Sync: ' + d.toTimeString().split(' ')[0];

            if (data.success) {
                document.getElementById('besmindo-stat-matched').textContent = data.matched_count;

                if (data.newly_joined && data.newly_joined.length > 0) {
                    data.newly_joined.forEach(nj => {
                        addLog(`🎉 HADIR: ${nj.crew_name} (${nj.status})`, '#4ade80');
                    });
                } else if (data.matched_count > 0) {
                    addLog(`Sync #${syncCount}: ${data.matched_count} crew aktif cocok`, '#38bdf8');
                } else {
                    addLog(`Terdeteksi ${participants.length} nama, belum ada yang cocok`, '#fbbf24');
                }
            } else {
                addLog(`Gagal: ${data.message || 'Error'}`, '#f87171');
            }
        })
        .catch(err => {
            addLog(`Error koneksi ke API Besmindo. Pastikan XAMPP aktif!`, '#f87171');
        });
    }

    // Event Listener Buttons
    document.getElementById('besmindo-sync-now-btn').addEventListener('click', function() {
        addLog("Menjalankan sinkronisasi manual...", "#38bdf8");
        executeSync();
    });

    const pauseBtn = document.getElementById('besmindo-pause-btn');
    pauseBtn.addEventListener('click', function() {
        isPaused = !isPaused;
        const dot = document.getElementById('besmindo-status-dot');
        if (isPaused) {
            pauseBtn.textContent = '▶️ Lanjut';
            pauseBtn.style.background = '#eab308';
            pauseBtn.style.color = '#000';
            dot.style.background = '#eab308';
            dot.style.boxShadow = '0 0 8px #eab308';
            addLog("Robot dijeda sementara.", "#eab308");
        } else {
            pauseBtn.textContent = '⏸️ Jeda';
            pauseBtn.style.background = '#334155';
            pauseBtn.style.color = '#cbd5e1';
            dot.style.background = '#4ade80';
            dot.style.boxShadow = '0 0 8px #4ade80';
            addLog("Robot kembali aktif.", "#4ade80");
            executeSync();
        }
    });

    document.getElementById('besmindo-close-btn').addEventListener('click', function() {
        if (confirm("Hentikan Teams Auto-Sync Robot?")) {
            clearInterval(timerId);
            hud.remove();
            window.__besmindoTeamsRobotRunning = false;
        }
    });

    // Jalankan sync pertama kali dan aktifkan loop
    executeSync();
    timerId = setInterval(executeSync, config.intervalSeconds * 1000);
})();
