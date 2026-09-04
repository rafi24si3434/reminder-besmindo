<?php
/**
 * Modern & Professional Meeting Switcher Component
 * PT. Besmindo Materi Sewatama
 *
 * Parameters expected:
 * @var array  $meetings        List of all meetings (from get_meetings_detailed)
 * @var array  $current_meeting Currently active meeting record
 * @var string $target_route    Base route e.g. 'attendance/live/' or 'attendance/rekap/' or 'reminder?meeting_id='
 * @var string $mode            'url_segment' (e.g. attendance/live/1) or 'query_param' (e.g. reminder?meeting_id=1)
 */

$switcherId = 'sw_' . substr(md5(uniqid(rand(), true)), 0, 8);
$currentId  = isset($current_meeting['id']) ? $current_meeting['id'] : 0;
$currTitle  = isset($current_meeting['title']) ? $current_meeting['title'] : 'Pilih Meeting';
$currRig    = isset($current_meeting['rig_code']) ? $current_meeting['rig_code'] : 'RIG';
$currDate   = isset($current_meeting['meeting_date']) ? date('d M', strtotime($current_meeting['meeting_date'])) : '';
$currTime   = isset($current_meeting['start_time']) ? substr($current_meeting['start_time'], 0, 5) : '';
$currStatus = isset($current_meeting['status']) ? $current_meeting['status'] : 'scheduled';
$mode       = isset($mode) ? $mode : (strpos($target_route, '=') !== false ? 'query_param' : 'url_segment');
?>

<div class="relative inline-block text-left select-none" id="<?= $switcherId ?>_wrapper">
    
    <!-- Trigger Button -->
    <button type="button" id="<?= $switcherId ?>_btn" 
        class="group inline-flex items-center space-x-2.5 bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 hover:from-slate-800 hover:to-slate-750 text-slate-200 border border-slate-700/80 hover:border-sky-500/60 rounded-xl px-3.5 py-2 text-xs font-semibold shadow-xl hover:shadow-sky-500/10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
        
        <!-- Status Indicator Dot -->
        <span class="flex h-2.5 w-2.5 relative items-center justify-center shrink-0">
            <?php if ($currStatus === 'completed'): ?>
                <span class="rounded-full h-2 w-2 bg-slate-500" title="Sesi Selesai / Ditutup"></span>
            <?php else: ?>
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500" title="Sesi Aktif"></span>
            <?php endif; ?>
        </span>

        <!-- Rig Badge Tag -->
        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/30 group-hover:bg-sky-500/30 transition shrink-0">
            <i class="fa-solid fa-oil-well mr-1 text-[9px]"></i><?= htmlspecialchars($currRig) ?>
        </span>

        <!-- Title & Schedule Preview -->
        <div class="text-left flex items-center space-x-2 max-w-[180px] sm:max-w-[260px] md:max-w-[320px] truncate">
            <span class="font-bold text-white truncate group-hover:text-sky-200 transition">
                <?= htmlspecialchars($currTitle) ?>
            </span>
            <?php if ($currDate): ?>
                <span class="text-[10px] text-slate-400 font-mono shrink-0 hidden sm:inline">
                    (<?= $currDate ?><?= $currTime ? ' ' . $currTime . ' WIB' : '' ?>)
                </span>
            <?php endif; ?>
        </div>

        <!-- Chevron Icon -->
        <span class="pl-1 text-slate-400 group-hover:text-sky-400 transition-colors shrink-0">
            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="<?= $switcherId ?>_chevron"></i>
        </span>
    </button>

    <!-- Floating Dropdown Menu Panel -->
    <div id="<?= $switcherId ?>_menu" 
        class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-slate-900/95 border border-slate-700/90 rounded-2xl shadow-2xl backdrop-blur-xl z-50 overflow-hidden ring-1 ring-white/10 divide-y divide-slate-800/80 transition-all">
        
        <!-- Header & Search Input -->
        <div class="p-3 bg-slate-950/70 border-b border-slate-800">
            <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                <span class="flex items-center space-x-1.5 text-sky-400">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Pilih Sesi Rapat Rig</span>
                </span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 font-mono border border-slate-700">
                    <?= count($meetings) ?> Jadwal
                </span>
            </div>

            <!-- Instant Search Box -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" id="<?= $switcherId ?>_search" placeholder="Cari nama rapat, kode rig, tanggal..." 
                    class="w-full pl-8 pr-7 py-1.5 bg-slate-800/90 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:outline-none transition">
                <button type="button" id="<?= $switcherId ?>_clear_search" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white text-xs">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Scrollable Meeting Roster -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-800/60 scrollbar-thin" id="<?= $switcherId ?>_list">
            <?php if (empty($meetings)): ?>
                <div class="p-6 text-center text-xs text-slate-500">
                    <i class="fa-regular fa-calendar-xmark text-2xl mb-2 block text-slate-600"></i>
                    Belum ada jadwal meeting yang tersimpan.
                </div>
            <?php else: ?>
                <?php foreach ($meetings as $m): 
                    $isSelected = ($currentId == $m['id']);
                    $isCompleted = ($m['status'] === 'completed');
                    $mDate = date('d M Y', strtotime($m['meeting_date']));
                    $mTime = substr($m['start_time'], 0, 5);
                    $mSearchText = strtolower($m['rig_code'] . ' ' . $m['rig_name'] . ' ' . $m['title'] . ' ' . $m['topic'] . ' ' . $mDate);
                    
                    // Build target URL
                    if ($mode === 'query_param') {
                        $itemUrl = base_url($target_route . $m['id']);
                    } else {
                        $itemUrl = base_url(rtrim($target_route, '/') . '/' . $m['id']);
                    }
                ?>
                    <a href="<?= $itemUrl ?>" 
                        class="<?= $switcherId ?>_item block p-3.5 hover:bg-slate-800/80 transition-all group <?= $isSelected ? 'bg-sky-500/10 border-l-4 border-l-sky-500' : 'border-l-4 border-l-transparent' ?>"
                        data-search="<?= htmlspecialchars($mSearchText) ?>">
                        
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <div class="flex items-center space-x-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase font-mono <?= $isSelected ? 'bg-sky-500 text-white shadow-sm' : 'bg-slate-800 text-sky-300 border border-slate-700/80' ?>">
                                    <?= htmlspecialchars($m['rig_code'] ?? 'RIG') ?>
                                </span>
                                
                                <?php if ($isCompleted): ?>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-800/90 text-slate-400 border border-slate-700 flex items-center space-x-1">
                                        <i class="fa-solid fa-lock text-[8px]"></i>
                                        <span>Ditutup</span>
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center space-x-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span>Aktif</span>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if ($isSelected): ?>
                                <span class="text-sky-400 font-bold text-xs flex items-center space-x-1 shrink-0">
                                    <i class="fa-solid fa-circle-check text-[11px]"></i>
                                    <span class="text-[10px] uppercase font-mono">Sedang Dibuka</span>
                                </span>
                            <?php else: ?>
                                <span class="text-slate-500 group-hover:text-sky-400 transition text-xs opacity-0 group-hover:opacity-100 shrink-0">
                                    Pilih &rarr;
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="font-bold text-xs text-white group-hover:text-sky-300 transition truncate">
                            <?= htmlspecialchars($m['title']) ?>
                        </div>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[10px] text-slate-400 mt-1 font-mono">
                            <span class="flex items-center space-x-1">
                                <i class="fa-regular fa-calendar text-slate-500"></i>
                                <span><?= $mDate ?></span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <i class="fa-regular fa-clock text-slate-500"></i>
                                <span><?= $mTime ?> WIB</span>
                            </span>
                            <?php if (!empty($m['rig_name'])): ?>
                                <span class="text-slate-500 truncate max-w-[120px]" title="<?= htmlspecialchars($m['rig_name']) ?>">
                                    &bull; <?= htmlspecialchars($m['rig_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div id="<?= $switcherId ?>_empty" class="hidden p-6 text-center text-xs text-slate-500">
                <i class="fa-solid fa-magnifying-glass text-xl mb-1 text-slate-600 block"></i>
                Tidak ada meeting yang cocok dengan pencarian Anda.
            </div>
        </div>

        <!-- Dropdown Footer -->
        <div class="p-2.5 bg-slate-950/90 border-t border-slate-800 flex items-center justify-between text-[11px]">
            <span class="text-slate-500 text-[10px]">Klik untuk beralih sesi</span>
            <a href="<?= base_url('meeting/create') ?>" class="text-sky-400 hover:text-sky-300 font-bold flex items-center space-x-1 text-xs">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>Jadwalkan Baru</span>
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    const btn     = document.getElementById('<?= $switcherId ?>_btn');
    const menu    = document.getElementById('<?= $switcherId ?>_menu');
    const chevron = document.getElementById('<?= $switcherId ?>_chevron');
    const search  = document.getElementById('<?= $switcherId ?>_search');
    const clear   = document.getElementById('<?= $switcherId ?>_clear_search');
    const wrapper = document.getElementById('<?= $switcherId ?>_wrapper');
    const items   = document.querySelectorAll('.<?= $switcherId ?>_item');
    const emptyMsg = document.getElementById('<?= $switcherId ?>_empty');

    if (!btn || !menu) return;

    function openMenu() {
        menu.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
        setTimeout(() => { if (search) search.focus(); }, 50);
    }

    function closeMenu() {
        menu.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }

    function toggleMenu() {
        if (menu.classList.contains('hidden')) {
            openMenu();
        } else {
            closeMenu();
        }
    }

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu();
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            closeMenu();
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMenu();
        }
    });

    // Search filter
    if (search) {
        search.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            if (clear) {
                if (query.length > 0) {
                    clear.classList.remove('hidden');
                } else {
                    clear.classList.add('hidden');
                }
            }

            items.forEach(item => {
                const searchData = item.getAttribute('data-search') || '';
                if (query === '' || searchData.includes(query)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (emptyMsg) {
                emptyMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        });

        if (clear) {
            clear.addEventListener('click', function() {
                search.value = '';
                search.dispatchEvent(new Event('input'));
                search.focus();
            });
        }
    }
})();
</script>
