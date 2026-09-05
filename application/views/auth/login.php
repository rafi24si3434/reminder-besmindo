<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal Manager - PT Besmindo Materi Sewatama</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        zinc: {
                            50:  '#fafafa', 100: '#f4f4f5', 200: '#e4e4e7',
                            300: '#d4d4d8', 400: '#a1a1aa', 500: '#71717a',
                            600: '#52525b', 700: '#3f3f46', 800: '#27272a',
                            850: '#1f1f23', 900: '#18181b', 950: '#09090b'
                        }
                    }
                }
            }
        }
    </script>
    <script>
        (function() {
            var s = localStorage.getItem('besmindo_theme');
            if (s === 'light') { document.documentElement.classList.remove('dark'); }
            else { document.documentElement.classList.add('dark'); }
        })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_besmindo.png') ?>">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 transition-colors
    bg-zinc-50 text-zinc-900
    dark:bg-zinc-950 dark:text-zinc-100">

    <!-- Top Theme Switcher -->
    <div class="fixed top-4 right-4 z-20">
        <button type="button" onclick="toggleTheme()" title="Ganti Tema"
            class="p-2 rounded-lg border text-xs font-medium transition
                bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800
                text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 shadow-sm">
            <i id="themeIconSun" class="fa-solid fa-sun text-amber-500 hidden"></i>
            <i id="themeIconMoon" class="fa-solid fa-moon text-sky-400 hidden"></i>
        </button>
    </div>

    <div class="max-w-md w-full my-8">
        
        <!-- Header / Logo -->
        <div class="text-center mb-6">
            <div class="inline-flex p-3 rounded-2xl mb-3 border shadow-sm
                bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
                <img src="<?= base_url('assets/images/logo_besmindo_light.png') ?>" alt="PT Besmindo Materi Sewatama" class="h-16 w-auto mx-auto object-contain dark:hidden">
                <img src="<?= base_url('assets/images/logo_besmindo.png') ?>" alt="PT Besmindo Materi Sewatama" class="h-12 w-auto mx-auto object-contain hidden dark:block">
            </div>
            <h1 class="text-base font-bold tracking-tight text-zinc-900 dark:text-zinc-100">SISTEM REMINDER &amp; MONITORING RIG</h1>
            <p class="text-xs font-medium text-sky-600 dark:text-sky-400 mt-0.5 uppercase tracking-wider">PT. Besmindo Materi Sewatama</p>
        </div>

        <!-- Card Shadcn UI -->
        <div class="rounded-xl border shadow-sm p-6 sm:p-8
            bg-white dark:bg-zinc-900
            border-zinc-200 dark:border-zinc-800">
            <div class="space-y-1 mb-6">
                <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Masuk Portal</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Masukkan username dan password untuk melanjutkan.</p>
            </div>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-5 p-3 rounded-lg border text-xs flex items-start space-x-2.5
                    bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500 flex-shrink-0"></i>
                    <span><?= $this->session->flashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="mb-5 p-3 rounded-lg border text-xs flex items-start space-x-2.5
                    bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300">
                    <i class="fa-solid fa-circle-check mt-0.5 text-emerald-500 flex-shrink-0"></i>
                    <span><?= $this->session->flashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/attempt_login') ?>" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input type="text" name="username" value="admin" required autofocus 
                            placeholder="Contoh: admin"
                            class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border transition
                                bg-zinc-50 dark:bg-zinc-950
                                border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 text-xs">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" value="admin123" required 
                            placeholder="••••••••"
                            class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border transition
                                bg-zinc-50 dark:bg-zinc-950
                                border-zinc-200 dark:border-zinc-800
                                text-zinc-900 dark:text-zinc-100 placeholder-zinc-400
                                focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-lg font-medium text-sm transition shadow-sm flex items-center justify-center space-x-2
                            bg-zinc-900 hover:bg-zinc-800 text-white
                            dark:bg-zinc-50 dark:hover:bg-zinc-200 dark:text-zinc-900">
                        <span>Masuk ke Dashboard</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                <span>Akun demo: <strong class="text-zinc-700 dark:text-zinc-300 font-mono">admin / admin123</strong></span>
                <a href="<?= base_url('install') ?>" class="hover:text-zinc-900 dark:hover:text-zinc-100 flex items-center space-x-1">
                    <i class="fa-solid fa-database text-[10px]"></i>
                    <span>Setup DB</span>
                </a>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-zinc-400 dark:text-zinc-600 space-y-1">
            <p>&copy; <?= date('Y') ?> PT. Besmindo Materi Sewatama</p>
            <p class="text-[11px]">Oilfield Equipment Sales &amp; Rental, Drilling &amp; Work Over Rig Services</p>
        </div>
    </div>

    <script>
        function isDark() { return document.documentElement.classList.contains('dark'); }
        function syncThemeIcons() {
            var sun = document.getElementById('themeIconSun');
            var moon = document.getElementById('themeIconMoon');
            if (!sun || !moon) return;
            if (isDark()) { sun.classList.remove('hidden'); moon.classList.add('hidden'); }
            else { sun.classList.add('hidden'); moon.classList.remove('hidden'); }
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
        syncThemeIcons();
    </script>
</body>
</html>

