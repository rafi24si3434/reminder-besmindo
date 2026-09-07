<?php
$current_segment = $this->uri->segment(1) ?: 'dashboard';
$sub_segment = $this->uri->segment(2) ?: '';

// Helper for nav item classes
function nav_item($active) {
    if ($active) {
        return 'flex items-center space-x-3 px-3 py-2 rounded-lg font-medium text-sm bg-zinc-800 dark:bg-zinc-800 text-zinc-50 border border-zinc-700 shadow-sm';
    }
    return 'flex items-center space-x-3 px-3 py-2 rounded-lg font-medium text-sm text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-zinc-50';
}
?>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0
    bg-white dark:bg-zinc-900
    border-r border-zinc-200 dark:border-zinc-800">

    <!-- Sidebar Header / Logo -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-zinc-200 dark:border-zinc-800 flex-shrink-0">
        <a href="<?= base_url('dashboard') ?>" class="flex items-center space-x-2.5 min-w-0">
            <img src="<?= base_url('assets/images/logo_besmindo_light.png') ?>" alt="PT Besmindo Materi Sewatama"
                class="h-11 w-auto object-contain max-w-[180px] dark:hidden">
            <img src="<?= base_url('assets/images/logo_besmindo.png') ?>" alt="PT Besmindo Materi Sewatama"
                class="h-9 w-auto object-contain max-w-[180px] hidden dark:block">
        </a>
        <button type="button" class="lg:hidden p-1.5 rounded-md text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" onclick="toggleSidebar()">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5">

        <!-- Menu Utama -->
        <div class="space-y-1">
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-600">Menu Utama</p>
            <a href="<?= base_url('dashboard') ?>"
                class="<?= nav_item($current_segment === 'dashboard') ?>">
                <i class="fa-solid fa-gauge-high w-4 text-center text-sm <?= $current_segment === 'dashboard' ? 'text-sky-400' : 'text-zinc-400' ?>"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Fase 1: Jadwal & Crew -->
        <div class="space-y-1">
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-600 flex items-center justify-between">
                <span>Jadwal &amp; Crew</span>
                <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-500 border border-zinc-200 dark:border-zinc-700">F1</span>
            </p>
            <a href="<?= base_url('meeting') ?>"
                class="<?= nav_item($current_segment === 'meeting') ?>">
                <i class="fa-regular fa-calendar-check w-4 text-center text-sm <?= $current_segment === 'meeting' ? 'text-sky-400' : 'text-zinc-400' ?>"></i>
                <span>Jadwal Pre-Hitch Meeting</span>
            </a>
            <a href="<?= base_url('crew') ?>"
                class="<?= nav_item($current_segment === 'crew') ?>">
                <i class="fa-solid fa-users-gear w-4 text-center text-sm <?= $current_segment === 'crew' ? 'text-sky-400' : 'text-zinc-400' ?>"></i>
                <span>Manajemen Crew Rig</span>
            </a>
            <a href="<?= base_url('rig') ?>"
                class="<?= nav_item($current_segment === 'rig') ?>">
                <i class="fa-solid fa-tower-observation w-4 text-center text-sm <?= $current_segment === 'rig' ? 'text-sky-400' : 'text-zinc-400' ?>"></i>
                <span>Master Data Rig</span>
            </a>
        </div>

        <!-- Fase 2&3: WhatsApp -->
        <div class="space-y-1">
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-600 flex items-center justify-between">
                <span>WhatsApp</span>
                <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">F2-F3</span>
            </p>
            <a href="<?= base_url('reminder') ?>"
                class="<?= nav_item($current_segment === 'reminder' && $sub_segment === '') ?>">
                <i class="fa-brands fa-whatsapp w-4 text-center text-sm text-emerald-500"></i>
                <span>Undangan &amp; Broadcast</span>
            </a>
            <a href="<?= base_url('reminder/gateway') ?>"
                class="<?= nav_item($current_segment === 'reminder' && $sub_segment === 'gateway') ?>">
                <i class="fa-solid fa-tower-broadcast w-4 text-center text-sm text-sky-400"></i>
                <span>WhatsApp Gateway</span>
            </a>
            <a href="<?= base_url('reminder/log') ?>"
                class="<?= nav_item($current_segment === 'reminder' && $sub_segment === 'log') ?>">
                <i class="fa-solid fa-clock-rotate-left w-4 text-center text-sm text-zinc-400"></i>
                <span>Log Reminder Outbox</span>
            </a>
        </div>

        <!-- Fase 4: Monitoring Teams -->
        <div class="space-y-1">
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-600 flex items-center justify-between">
                <span>Monitoring Teams</span>
                <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900">F4</span>
            </p>
            <a href="<?= base_url('attendance/live') ?>"
                class="<?= nav_item($current_segment === 'attendance' && $sub_segment === 'live') ?>">
                <i class="fa-solid fa-video w-4 text-center text-sm text-indigo-400"></i>
                <div class="flex-1 flex items-center justify-between">
                    <span>Live Attendance</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
            </a>
            <a href="<?= base_url('attendance/rekap') ?>"
                class="<?= nav_item($current_segment === 'attendance' && $sub_segment === 'rekap') ?>">
                <i class="fa-solid fa-file-signature w-4 text-center text-sm text-sky-400"></i>
                <span>Rekap Absensi Sesi</span>
            </a>
            <a href="<?= base_url('attendance/simulator') ?>"
                class="<?= nav_item($current_segment === 'attendance' && $sub_segment === 'simulator') ?>">
                <i class="fa-solid fa-file-excel w-4 text-center text-sm text-emerald-400"></i>
                <span>Import Teams (.xlsx / .csv)</span>
            </a>
        </div>

        <!-- Fase 5: Laporan -->
        <div class="space-y-1">
            <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-600 flex items-center justify-between">
                <span>Laporan</span>
                <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900">F5</span>
            </p>
            <a href="<?= base_url('report') ?>"
                class="<?= nav_item($current_segment === 'report') ?>">
                <i class="fa-solid fa-chart-pie w-4 text-center text-sm text-amber-400"></i>
                <span>Rekap &amp; Analitik</span>
            </a>
        </div>

    </nav>

    <!-- Sidebar Footer: User Profile -->
    <div class="px-3 py-3 border-t border-zinc-200 dark:border-zinc-800 flex-shrink-0
        bg-zinc-50 dark:bg-zinc-950">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2.5 overflow-hidden min-w-0">
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 dark:bg-sky-500/20 border border-sky-500/20 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-user-shield text-xs text-sky-500 dark:text-sky-400"></i>
                </div>
                <div class="truncate">
                    <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-100 truncate"><?= htmlspecialchars($this->session->userdata('name') ?: 'Manager Rig') ?></p>
                    <p class="text-[10px] text-zinc-500 dark:text-zinc-500">Manager Operasional</p>
                </div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" title="Keluar"
                class="flex-shrink-0 p-1.5 rounded-md text-zinc-400 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition">
                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
            </a>
        </div>
    </div>
</aside>

<!-- Mobile overlay -->
<div id="sidebarBackdrop" onclick="toggleSidebar()"
    class="fixed inset-0 z-30 bg-zinc-950/60 backdrop-blur-sm hidden lg:hidden"></div>

<!-- Main content wrapper -->
<div class="lg:pl-64 flex flex-col flex-1 min-h-screen">

