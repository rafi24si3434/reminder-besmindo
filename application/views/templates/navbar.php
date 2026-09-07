<header class="h-16 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8 border-b transition-colors
    bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md
    border-zinc-200 dark:border-zinc-800">

    <div class="flex items-center space-x-3">
        <button type="button" class="lg:hidden p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars text-base"></i>
        </button>
        <div class="flex items-center space-x-2.5">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 mr-2 animate-pulse"></span>
                Operational System
            </span>
            <span class="hidden md:inline-flex items-center text-xs text-zinc-500 dark:text-zinc-400">
                <i class="fa-regular fa-clock mr-1.5 text-zinc-400"></i>
                <span id="liveClock" class="font-mono"><?= date('H:i:s') ?></span>&nbsp;WIB
            </span>
        </div>
    </div>

    <div class="flex items-center space-x-2 sm:space-x-3">
        <!-- Theme Toggle Button (Light / Dark Mode) -->
        <button type="button" id="themeToggleBtn" onclick="toggleTheme()" title="Ganti Mode Terang/Gelap"
            class="p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition">
            <i id="themeIconSun" class="fa-solid fa-sun text-sm text-amber-500 hidden"></i>
            <i id="themeIconMoon" class="fa-solid fa-moon text-sm text-sky-400 hidden"></i>
        </button>

        <!-- Notification Bell -->
        <div class="relative" id="notifWrap">
            <button type="button" id="notifBell" onclick="toggleNotifications()" title="Notifikasi"
                class="relative p-2 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 transition"
                aria-haspopup="true" aria-expanded="false" aria-label="Notifikasi">
                <i class="fa-regular fa-bell text-sm"></i>
                <span id="notifBadge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-semibold leading-[18px] text-center text-white bg-sky-500 shadow-sm"></span>
            </button>

            <!-- Dropdown Panel -->
            <div id="notifPanel" class="hidden absolute right-0 mt-2 w-[min(92vw,380px)] origin-top-right rounded-xl border shadow-2xl ring-1 ring-black/5
                bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 z-50
                opacity-0 translate-y-1 pointer-events-none transition-all duration-200 ease-out
                dark:shadow-black/40">
                <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-bell text-xs text-zinc-400"></i>
                        <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Notifikasi</span>
                        <span id="notifLiveDot" class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" title="Real-time"></span>
                    </div>
                    <button type="button" onclick="markAllRead()" id="notifMarkAll"
                        class="text-xs font-medium text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 hover:underline disabled:opacity-40 disabled:pointer-events-none">Tandai dibaca</button>
                </div>
                <div id="notifList" class="max-h-[360px] overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800"></div>
                <div id="notifFooter" class="px-4 py-2.5 border-t border-zinc-200 dark:border-zinc-800 text-center text-[11px] text-zinc-400 dark:text-zinc-500">
                    <span id="notifUpdated"></span>
                </div>
            </div>
        </div>

        <button type="button" onclick="triggerBackgroundReminders()" title="Periksa Jadwal &amp; Trigger Reminder Otomatis Sekarang" 
            class="hidden sm:inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg text-xs font-medium transition
                bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700
                text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
            <i class="fa-solid fa-paper-plane text-emerald-500"></i>
            <span>Scan Reminders</span>
        </button>

        <a href="<?= base_url('meeting/create') ?>" 
            class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg text-xs font-medium text-white transition shadow-sm
                bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-50 dark:text-zinc-900 dark:hover:bg-zinc-200">
            <i class="fa-solid fa-plus text-xs"></i>
            <span class="hidden sm:inline">Buat Jadwal Meeting</span>
            <span class="sm:hidden">Meeting</span>
        </a>

        <a href="<?= base_url('install') ?>" title="Pengaturan Database &amp; Dummy Data" 
            class="p-2 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-sm">
            <i class="fa-solid fa-database"></i>
        </a>
    </div>
</header>

<main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="mb-6 p-4 rounded-xl flex items-center justify-between border shadow-sm
            bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                <span class="text-sm font-medium"><?= $this->session->flashdata('success') ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600/60 dark:text-emerald-400/60 hover:text-emerald-700 dark:hover:text-emerald-300">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="mb-6 p-4 rounded-xl flex items-center justify-between border shadow-sm
            bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-base"></i>
                <span class="text-sm font-medium"><?= $this->session->flashdata('error') ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600/60 dark:text-rose-400/60 hover:text-rose-700 dark:hover:text-rose-300">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

