<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Besmindo Rig Meeting Reminder' ?></title>

    <!-- Google Font: Inter (Shadcn UI Standard) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                        },
                        besmindo: {
                            500: '#0284c7', 600: '#0369a1', 700: '#075985',
                            800: '#0c4a6e', 900: '#082f49'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Theme Init: Runs before body renders to prevent FOUC -->
    <script>
        (function() {
            var s = localStorage.getItem('besmindo_theme');
            if (s === 'light') { document.documentElement.classList.remove('dark'); }
            else { document.documentElement.classList.add('dark'); }
        })();
    </script>

    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo_besmindo.png') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        .dark ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; }
        :not(.dark) ::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 4px; }
        html { transition: background-color 0.2s ease, color 0.2s ease; }
        * { transition: background-color 0.15s ease, border-color 0.15s ease; }
    </style>
</head>
<body class="h-full antialiased selection:bg-sky-500/20
    bg-zinc-50 text-zinc-900
    dark:bg-zinc-950 dark:text-zinc-100
    flex flex-col">

