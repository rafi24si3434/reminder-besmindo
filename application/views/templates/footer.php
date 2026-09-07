</main>

<footer class="mt-auto py-4 px-6 border-t text-center text-xs transition-colors
    border-zinc-200 dark:border-zinc-800
    bg-white/60 dark:bg-zinc-900/60
    text-zinc-500 dark:text-zinc-400">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 max-w-7xl mx-auto">
        <div>
            &copy; <?= date('Y') ?> <strong class="font-medium text-zinc-700 dark:text-zinc-200">PT. Besmindo Materi Sewatama</strong> &bull; Rig Crew Meeting Reminder &amp; Attendance System
        </div>
        <div class="flex items-center space-x-3 text-[11px]">
            <a href="https://besmindoms.com/id/" target="_blank" class="text-sky-600 dark:text-sky-400 hover:underline inline-flex items-center space-x-1">
                <i class="fa-solid fa-globe"></i>
                <span>besmindoms.com</span>
            </a>
            <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
            <span class="text-zinc-400 dark:text-zinc-500">CI 3.1 &bull; PHP <?= phpversion() ?></span>
        </div>
    </div>
</footer>

</div>

<script>
    const BASE_URL = '<?= base_url() ?>';

    // Theme Switcher Logic
    function isDark() {
        return document.documentElement.classList.contains('dark');
    }

    function syncThemeIcons() {
        const sun = document.getElementById('themeIconSun');
        const moon = document.getElementById('themeIconMoon');
        if (!sun || !moon) return;
        if (isDark()) {
            sun.classList.remove('hidden');
            moon.classList.add('hidden');
        } else {
            sun.classList.add('hidden');
            moon.classList.remove('hidden');
        }
    }

    function toggleTheme() {
        if (isDark()) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('besmindo_theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('besmindo_theme', 'dark');
        }
        syncThemeIcons();
    }

    // Run icon sync immediately
    syncThemeIcons();
    document.addEventListener('DOMContentLoaded', syncThemeIcons);

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        sidebar.classList.toggle('-translate-x-full');
        backdrop.classList.toggle('hidden');
    }

    setInterval(() => {
        const el = document.getElementById('liveClock');
        if (el) {
            const now = new Date();
            el.innerText = now.toLocaleTimeString('id-ID', { hour12: false });
        }
    }, 1000);

    // ─── Notification Center (real-time) ────────────────────────────────
    let notifOpen = false;
    let notifUnread = 0;
    let notifPolling = false;

    function notifTypeMeta(type) {
        switch (type) {
            case 'meeting_soon':  return { label: 'Jadwal', badge: 'text-amber-500 bg-amber-500/10' };
            case 'meeting_now':   return { label: 'Berlangsung', badge: 'text-emerald-500 bg-emerald-500/10' };
            case 'not_present':   return { label: 'Belum hadir', badge: 'text-rose-500 bg-rose-500/10' };
            case 'attendance':    return { label: 'Kehadiran', badge: 'text-sky-500 bg-sky-500/10' };
            case 'reminder':      return { label: 'WhatsApp', badge: 'text-emerald-500 bg-emerald-500/10' };
            default:              return { label: 'Info', badge: 'text-zinc-500 bg-zinc-500/10' };
        }
    }

    function toggleNotifications() {
        const panel = document.getElementById('notifPanel');
        const btn = document.getElementById('notifBell');
        notifOpen = !notifOpen;
        if (notifOpen) { renderNotifications(); }
        panel.classList.toggle('hidden', !notifOpen);
        panel.classList.toggle('opacity-0', !notifOpen);
        panel.classList.toggle('translate-y-1', !notifOpen);
        panel.classList.toggle('pointer-events-none', !notifOpen);
        btn.setAttribute('aria-expanded', notifOpen ? 'true' : 'false');
    }

    // Empty-state and per-type row renderer; raw html injected from our own API only.
    function notifRow(it, isUnread) {
        const meta = notifTypeMeta(it.type);
        return `<a href="${it.link}" data-key="${it.key}"
            class="flex items-start space-x-3 px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/70 transition group">
            <span class="flex-shrink-0 w-9 h-9 rounded-lg ${it.color} flex items-center justify-center">
                <i class="${it.icon} text-sm"></i>
            </span>
            <span class="flex-1 min-w-0">
                <span class="block text-[13px] leading-snug text-zinc-700 dark:text-zinc-200">${it.text}</span>
                <span class="flex items-center space-x-1.5 mt-1.5">
                    <span class="text-[10px] px-1.5 py-0.5 rounded ${meta.badge} font-medium">${meta.label}</span>
                    <span class="text-[11px] text-zinc-400 dark:text-zinc-500">${it.time_text}</span>
                </span>
            </span>
            ${isUnread ? '<span class="flex-shrink-0 w-2 h-2 mt-1.5 rounded-full bg-sky-500" aria-hidden="true"></span>'
                       : `<i class="flex-shrink-0 mt-1.5 fa-solid fa-chevron-right text-[10px] text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-400 dark:group-hover:text-zinc-500"></i>`}
        </a>`;
    }

    function renderNotifications() {
        const list = document.getElementById('notifList');
        // Server re-renders nothing client-side; we keep last fetched items here.
        if (typeof window.__notifItems === 'undefined') {
            list.innerHTML = '<div class="px-4 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500"><i class="fa-solid fa-spinner fa-spin mb-2 block text-lg"></i>Memuat notifikasi...</div>';
            return;
        }
        const items = window.__notifItems;
        const seen = window.__notifSeen || {};
        if (!items.length) {
            list.innerHTML = `<div class="px-4 py-12 text-center">
                <div class="mx-auto mb-3 w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 flex items-center justify-center">
                    <i class="fa-solid fa-bell-slash text-emerald-500 dark:text-emerald-400"></i>
                </div>
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Tidak ada notifikasi baru</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Semua aman. Akan muncul saat ada jadwal, kehadiran, atau pengiriman reminder.</p>
            </div>`;
            document.getElementById('notifMarkAll').disabled = true;
            return;
        }
        document.getElementById('notifMarkAll').disabled = false;
        list.innerHTML = items.map(it => notifRow(it, !seen[it.key])).join('');
    }

    async function pollNotifications() {
        if (notifPolling) return;
        notifPolling = true;
        try {
            const res = await fetch('<?= base_url('api/notifications') ?>');
            const data = await res.json();
            if (data.error === 'login_required') return;
            window.__notifItems = data.items || [];
            window.__notifUnread = data.unread || 0;
            window.__notifSeen = {};
            (data.items || []).forEach(it => {
                if (window.__notifReadKeys && window.__notifReadKeys[it.key]) window.__notifSeen[it.key] = true;
            });
            updateBadge();
            if (notifOpen) renderNotifications();
            document.getElementById('notifUpdated').innerText = 'Terakhir diperbarui ' + data.now;
        } catch (e) { /* offline / gateway down: keep silent */ }
        finally { notifPolling = false; }
    }

    function updateBadge() {
        const badge = document.getElementById('notifBadge');
        const markAll = document.getElementById('notifMarkAll');
        notifUnread = window.__notifUnread || 0;
        if (notifUnread > 0) {
            badge.classList.remove('hidden');
            badge.innerText = notifUnread > 99 ? '99+' : notifUnread;
            badge.classList.add('animate-pulse');
            setTimeout(() => badge.classList.remove('animate-pulse'), 2000);
        } else {
            badge.classList.add('hidden');
        }
        if (markAll) markAll.disabled = notifUnread === 0;
    }

    async function markRead(keys) {
        try {
            await fetch('<?= base_url('api/notifications_read') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ keys: keys })
            });
        } catch (e) {}
    }

    async function markAllRead() {
        await markRead(null);
        window.__notifUnread = 0;
        window.__notifReadKeys = window.__notifReadKeys || {};
        (window.__notifItems || []).forEach(it => window.__notifReadKeys[it.key] = true);
        window.__notifSeen = {};
        updateBadge();
        if (notifOpen) renderNotifications();
    }

    // Item click: mark that item read, then follow link.
    document.addEventListener('click', (e) => {
        const row = e.target.closest('#notifList a[data-key]');
        if (row) {
            e.preventDefault();
            const key = row.getAttribute('data-key');
            markRead([key]);
            window.location.href = row.getAttribute('href');
        }
        // Close when clicking outside the bell wrap
        const wrap = document.getElementById('notifWrap');
        if (notifOpen && wrap && !wrap.contains(e.target)) {
            toggleNotifications();
        }
    });

    // Poll schedule: on load and every 20s.
    pollNotifications();
    setInterval(pollNotifications, 20000);

    function triggerBackgroundReminders() {
        const dark = isDark();
        Swal.fire({
            title: 'Memeriksa Jadwal Meeting...',
            text: 'Sistem sedang mengevaluasi reminder H-1, H-1 Jam, dan H-15 Menit untuk semua crew...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            background: dark ? '#18181b' : '#ffffff',
            color: dark ? '#fafafa' : '#09090b',
            didOpen: () => {
                Swal.showLoading();
                fetch('<?= base_url('api/check_reminders') ?>')
                    .then(r => r.json())
                    .then(res => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reminder Engine Selesai',
                            html: `<div class="text-left text-sm space-y-1">
                                <p><strong>Status:</strong> ${res.message}</p>
                                <p><strong>Pesan Diproses:</strong> ${res.processed || 0}</p>
                                <p class="text-xs text-zinc-400 mt-2">${res.timestamp || ''}</p>
                            </div>`,
                            confirmButtonColor: '#0284c7',
                            background: isDark() ? '#18181b' : '#ffffff',
                            color: isDark() ? '#fafafa' : '#09090b'
                        });
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Trigger Reminder',
                            text: err.message,
                            confirmButtonColor: '#ef4444',
                            background: isDark() ? '#18181b' : '#ffffff',
                            color: isDark() ? '#fafafa' : '#09090b'
                        });
                    });
            }
        });
    }
</script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>

