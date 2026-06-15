<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Masuk' }} — Keuangan</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
    <div class="flex min-h-full items-center justify-center bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 px-4 py-12">
        <!-- Decorative background elements -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -left-1/4 -top-1/4 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute -bottom-1/4 -right-1/4 h-96 w-96 rounded-full bg-purple-500/20 blur-3xl"></div>
            <div class="absolute left-1/2 top-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-cyan-500/10 blur-3xl"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-2xl shadow-indigo-500/30">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Keuangan</h1>
                <p class="mt-1 text-sm text-slate-400">Pengelolaan Keuangan Pribadi & UMKM</p>
            </div>

            <!-- Card -->
            <div class="rounded-2xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-xl">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="mt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Keuangan. Kelola keuanganmu dengan bijak.
            </p>
        </div>
    </div>
</body>
</html>
