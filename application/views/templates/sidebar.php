<?php
$current_segment = $this->uri->segment(1) ?: 'dashboard';
$sub_segment = $this->uri->segment(2) ?: '';
?>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 border-r border-slate-800 transition-transform duration-300 -translate-x-full lg:translate-x-0 flex flex-col">
    <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 bg-slate-950/60">
        <a href="<?= base_url('dashboard') ?>" class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-sky-700 flex items-center justify-center text-white shadow-lg shadow-sky-500/20">
                <i class="fa-solid fa-oil-well text-xl"></i>
            </div>
            <div>
                <span class="font-bold text-base text-white tracking-wide block leading-tight">BESMINDO</span>
                <span class="text-[10px] text-sky-400 font-semibold tracking-wider uppercase">Rig Meeting Reminder</span>
            </div>
        </a>
        <button type="button" class="lg:hidden text-slate-400 hover:text-white" onclick="toggleSidebar()">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-6 scrollbar-thin scrollbar-thumb-slate-700">
        <div>
            <span class="px-3 text-[11px] font-bold text-slate-500 tracking-wider uppercase">Menu Utama</span>
            <div class="mt-2 space-y-1">
                <a href="<?= base_url('dashboard') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= $current_segment === 'dashboard' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-gauge-high w-5 text-center text-base"></i>
                    <span>Dashboard Manager</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[11px] font-bold text-slate-500 tracking-wider uppercase flex items-center justify-between">
                <span>Fase 1: Jadwal & Crew</span>
                <span class="text-[9px] bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded font-mono">F1</span>
            </span>
            <div class="mt-2 space-y-1">
                <a href="<?= base_url('meeting') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= $current_segment === 'meeting' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-regular fa-calendar-check w-5 text-center text-base"></i>
                    <span>Jadwal Meeting Rig</span>
                </a>
                <a href="<?= base_url('crew') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= $current_segment === 'crew' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-users-gear w-5 text-center text-base"></i>
                    <span>Manajemen Crew Rig</span>
                </a>
                <a href="<?= base_url('rig') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= $current_segment === 'rig' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-tower-observation w-5 text-center text-base"></i>
                    <span>Master Data Rig</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[11px] font-bold text-slate-500 tracking-wider uppercase flex items-center justify-between">
                <span>Fase 2 & 3: WhatsApp</span>
                <span class="text-[9px] bg-slate-800 text-emerald-400 px-1.5 py-0.5 rounded font-mono">F2-F3</span>
            </span>
            <div class="mt-2 space-y-1">
                <a href="<?= base_url('reminder') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= ($current_segment === 'reminder' && $sub_segment === '') ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-brands fa-whatsapp w-5 text-center text-base text-emerald-400"></i>
                    <span>Undangan & Broadcast</span>
                </a>
                <a href="<?= base_url('reminder/gateway') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= ($current_segment === 'reminder' && $sub_segment === 'gateway') ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-tower-broadcast w-5 text-center text-base text-sky-400"></i>
                    <span>Layanan WhatsApp Gateway</span>
                </a>
                <a href="<?= base_url('reminder/log') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= ($current_segment === 'reminder' && $sub_segment === 'log') ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-clock-rotate-left w-5 text-center text-base"></i>
                    <span>Log Reminder Outbox</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[11px] font-bold text-slate-500 tracking-wider uppercase flex items-center justify-between">
                <span>Fase 4: Monitoring Teams</span>
                <span class="text-[9px] bg-slate-800 text-indigo-400 px-1.5 py-0.5 rounded font-mono">F4</span>
            </span>
            <div class="mt-2 space-y-1">
                <a href="<?= base_url('attendance/live') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= ($current_segment === 'attendance' && $sub_segment === 'live') ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-video w-5 text-center text-base text-indigo-400"></i>
                    <div class="flex-1 flex items-center justify-between">
                        <span>Live Attendance</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                </a>
                <a href="<?= base_url('attendance/simulator') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= ($current_segment === 'attendance' && $sub_segment === 'simulator') ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-gamepad w-5 text-center text-base text-amber-400"></i>
                    <span>Teams Simulator & CSV</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[11px] font-bold text-slate-500 tracking-wider uppercase flex items-center justify-between">
                <span>Fase 5: Rekap & Laporan</span>
                <span class="text-[9px] bg-slate-800 text-amber-400 px-1.5 py-0.5 rounded font-mono">F5</span>
            </span>
            <div class="mt-2 space-y-1">
                <a href="<?= base_url('report') ?>" 
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-colors <?= $current_segment === 'report' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-base text-amber-400"></i>
                    <span>Rekap & Analitik</span>
                </a>
            </div>
        </div>
    </div>

    <div class="p-4 border-t border-slate-800 bg-slate-950/40">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-slate-700 flex items-center justify-center text-sky-400 font-bold border border-slate-600 flex-shrink-0">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div class="truncate">
                    <p class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($this->session->userdata('name') ?: 'Manager Rig') ?></p>
                    <p class="text-[10px] text-slate-400">Manager Operasional</p>
                </div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" title="Keluar" 
                class="text-slate-400 hover:text-rose-400 p-2 rounded-lg hover:bg-slate-800 transition">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </div>
    </div>
</aside>

<div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 z-30 bg-slate-950/70 backdrop-blur-sm hidden lg:hidden"></div>

<div class="lg:pl-64 flex flex-col flex-1 min-h-screen">
