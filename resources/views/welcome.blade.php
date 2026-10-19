<!DOCTYPE html>
<html lang="id" class="js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config("app.name") }}</title>
    @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body style="background-color: var(--color-ink); color: var(--color-paper); font-family: var(--font-sans); display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;">
    <div style="text-align: center; max-width: 540px; padding: 2rem;">
        <h1 style="font-family: var(--font-display); font-size: 3rem; font-weight: 800; letter-spacing: -0.02em; color: var(--color-signal); margin-bottom: 0.5rem;">
            {{ config("app.name") }}
        </h1>
        <p style="color: var(--color-mist); font-size: 1.125rem; margin-bottom: 2rem;">
            Portal Karir untuk Lulusan D3 Teknik Informatika
        </p>
        <div style="background: var(--color-ink-raised); border: 1px solid var(--color-ink-line); border-radius: var(--radius-card); padding: 1.5rem; text-align: left;">
            <p style="color: var(--color-mint); font-weight: 600; margin-bottom: 1rem;">✓ Fase 0 — Fondasi selesai</p>
            <ul style="list-style: none; padding: 0; margin: 0; color: var(--color-mist); font-size: 0.875rem; line-height: 2;">
                <li>✓ Laravel 13.37 · PHP 8.3.35</li>
                <li>✓ MySQL · Migrasi berhasil</li>
                <li>✓ Tailwind CSS v4.3.3</li>
                <li>✓ anime.js v4.5.0 (named import)</li>
                <li>✓ Font self-host: Bricolage + Plus Jakarta Sans</li>
                <li>✓ Token desain terpasang</li>
                <li>✓ Struktur folder sesuai plan.md</li>
            </ul>
        </div>
    </div>
</body>
</html>
