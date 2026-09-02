<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Manager - Besmindo Reminder</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        besmindo: {
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            800: '#0c4a6e',
                            900: '#082f49'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 bg-slate-950 text-slate-100">
    <div class="max-w-md w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500 to-besmindo-700 text-white shadow-xl shadow-sky-500/20 mb-4 border border-sky-400/30">
                <i class="fa-solid fa-oil-well text-3xl"></i>
            </div>
            <h1 class="text-2xl font-black tracking-wider text-white">BESMINDO RIG</h1>
            <p class="text-sky-400 text-xs font-semibold uppercase tracking-widest mt-0.5">Meeting Reminder & Attendance System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 sm:p-8 backdrop-blur">
            <h2 class="text-lg font-bold text-white mb-1">Masuk Portal Manager</h2>
            <p class="text-xs text-slate-400 mb-6">Silakan masukkan kredensial akun Manager Anda.</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start space-x-2">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-400 flex-shrink-0"></i>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-start space-x-2">
                    <i class="fa-solid fa-circle-check mt-0.5 text-emerald-400 flex-shrink-0"></i>
                    <span><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/attemptLogin') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input type="text" name="username" value="<?= old('username', 'admin') ?>" required autofocus 
                            placeholder="Contoh: admin"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" value="admin123" required 
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-sky-600 to-besmindo-700 hover:from-sky-500 hover:to-besmindo-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-sky-500/20 transition duration-150 flex items-center justify-center space-x-2">
                        <span>Masuk ke Dashboard</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>Demo Akun: <strong class="text-sky-400 font-mono">admin / admin123</strong></span>
                <a href="<?= base_url('install') ?>" class="text-slate-400 hover:text-white flex items-center space-x-1">
                    <i class="fa-solid fa-database text-[10px]"></i>
                    <span>Setup DB</span>
                </a>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-slate-500">
            &copy; <?= date('Y') ?> PT Besmindo Oilfield Operations. All rights reserved.
        </div>
    </div>
</body>
</html>
