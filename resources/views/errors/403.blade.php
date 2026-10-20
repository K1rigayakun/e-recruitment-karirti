<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script nonce="{{ Vite::cspNonce() }}">
        document.documentElement.classList.replace('no-js', 'js');
    </script>
</head>
<body class="min-h-dvh flex items-center justify-center px-4 py-20">
    <div class="text-center space-y-6 max-w-md" data-page-enter>
        <p class="text-6xl">🚫</p>
        <p class="font-display text-8xl font-bold text-signal opacity-20">403</p>
        <h1 class="font-display text-3xl text-paper -mt-8">Akses ditolak.</h1>
        <p class="text-mist">Kamu tidak memiliki izin untuk melihat halaman ini.</p>
        <div class="flex justify-center gap-3">
            <x-ui.button variant="primary" href="{{ route('home') }}">Ke beranda</x-ui.button>
            <a href="javascript:history.back()" class="rounded-control border border-ink-line px-5 py-2.5 text-sm text-mist hover:text-paper">Kembali</a>
        </div>
    </div>
</body>
</html>
