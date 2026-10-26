<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 — Sedang Dalam Pemeliharaan — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script nonce="{{ Vite::cspNonce() }}">
        document.documentElement.classList.replace('no-js', 'js');
    </script>
</head>
<body class="min-h-dvh flex items-center justify-center px-4 py-20">
    <div class="text-center space-y-6 max-w-md" data-page-enter>
        <p class="text-6xl">🔄</p>
        <p class="font-display text-8xl font-bold text-signal opacity-20">503</p>
        <h1 class="font-display text-3xl text-paper -mt-8">Sedang dalam pemeliharaan.</h1>
        <p class="text-mist">Kami sedang melakukan pembaruan. Kembali dalam beberapa menit.</p>
        <div class="flex justify-center gap-3">
            <button type="button" data-reload class="rounded-control border border-signal/40 bg-signal/10 px-5 py-2.5 text-sm font-medium text-signal hover:bg-signal/20">Muat ulang halaman</button>
            <a href="/" class="rounded-control border border-ink-line px-5 py-2.5 text-sm text-mist hover:text-paper">Beranda</a>
        </div>
    </div>
</body>
</html>
