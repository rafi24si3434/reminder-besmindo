<header class="h-16 bg-slate-900/80 backdrop-blur border-b border-slate-800 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8">
    <div class="flex items-center space-x-3">
        <button type="button" class="lg:hidden text-slate-300 hover:text-white p-2 rounded-lg hover:bg-slate-800" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                <i class="fa-solid fa-signal mr-1.5 text-[10px]"></i> Operational System
            </span>
            <span class="hidden md:inline-block text-xs text-slate-400">
                <i class="fa-regular fa-clock mr-1"></i> <span id="liveClock"><?= date('H:i:s') ?></span> WIB
            </span>
        </div>
    </div>

    <!-- Quick Actions Top Bar -->
    <div class="flex items-center space-x-3">
        <!-- Background Reminder Trigger Button -->
        <button type="button" onclick="triggerBackgroundReminders()" title="Periksa Jadwal & Trigger Reminder Otomatis Sekarang" 
            class="hidden sm:inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
            <i class="fa-solid fa-paper-plane text-emerald-400"></i>
            <span>Scan Reminders</span>
        </button>

        <!-- New Meeting Button -->
        <a href="<?= base_url('meeting/create') ?>" 
            class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-sky-600 hover:bg-sky-500 text-white shadow-md shadow-sky-600/30 transition">
            <i class="fa-solid fa-plus"></i>
            <span class="hidden sm:inline">Buat Jadwal Meeting</span>
            <span class="sm:hidden">Meeting</span>
        </a>

        <!-- Live Link to Install / DB Check -->
        <a href="<?= base_url('install') ?>" title="Pengaturan Database & Dummy Data" 
            class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition text-sm">
            <i class="fa-solid fa-database"></i>
        </a>
    </div>
</header>
