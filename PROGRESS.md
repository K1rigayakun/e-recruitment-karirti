# PROGRESS.md — KarirTI

> File ini diperbarui di akhir setiap fase. AI membaca ini saat sesi dilanjutkan.

## Status Fase

| Fase | Nama | Status | Tanggal |
|------|------|--------|---------|
| 0 | Fondasi & Setup | ✅ SELESAI | 2026-10-19 |
| 1 | Sistem Desain | ✅ SELESAI | 2026-10-19 |
| 2 | Mesin Animasi & Background | ✅ SELESAI | 2026-10-19 |
| 3 | Database & Seeder | ✅ SELESAI | 2026-10-19 |
| 4 | Auth, Role, Keamanan Dasar | ✅ SELESAI | 2026-10-19 |
| 5–13 | ... | ⏳ Belum dimulai | - |

## Cara Menjalankan Proyek

```bash
# Masuk ke folder
cd karirti

# Jalankan dengan PHP 8.3
$env:PATH = "M:\Xampp;$env:PATH"

# Dev server (jalankan manual di terminal):
php artisan serve

# Build assets:
npm run build

# Jalankan test:
php artisan test
```

Styleguide (hanya `APP_ENV=local`): http://127.0.0.1:8000/styleguide

## Keputusan Penting

| Fase | Keputusan |
|------|-----------|
| 0 | Tailwind v4 lewat `@tailwindcss/postcss` (bukan `@tailwindcss/vite`) karena Vite 8 tidak kompatibel dengan plugin Vite Tailwind v4 |
| 0 | PHP 8.3.35 dari `M:\Xampp\php.exe` (bukan dari PATH default XAMPP) |
| 0 | Vite tetap v8 (dari skeleton Laravel 13.37) — tidak di-downgrade |
| 0 | Font self-host via `@fontsource-variable` |
| 1 | `/styleguide` selalu terdaftar, 404 kecuali `app()->environment('local')` agar tes production mudah |
| 1 | UI overlay (dialog/dropdown/nav/toast) memakai JS vanilla, tanpa Alpine |
| 1 | Toast diposisikan kanan-bawah supaya tidak menutup hamburger di mobile |
| 1 | Nonce CSP Vite diaktifkan di `AppServiceProvider` untuk skrip `no-js` → `js` |

## Utang Teknis

| Item | Keterangan |
|------|-----------|
| Warning font runtime | Font woff2 tidak resolved saat build tapi berjalan normal di runtime |
| PATH PHP | Harus set `$env:PATH = "M:\Xampp;$env:PATH"` setiap sesi baru |
| HEAD detached | Repo masih detached dari tag skeleton Laravel; commit fase 1 dibuat di branch `main` lokal |
| Esc di otomasi browser | Dialog native menutup lewat tombol Tutup; Escape browser-tool tidak selalu sampai ke `<dialog>` |

## Versi Stack

| Stack | Versi |
|-------|-------|
| PHP | 8.3.35 (`M:\Xampp\php.exe`) |
| Laravel | 13.37.0 |
| Vite | 8.3.4 |
| Tailwind CSS | 4.3.3 |
| anime.js | 4.5.0 |
| Node.js | 22.14.0 |
| MySQL/MariaDB | 10.4.32 |







