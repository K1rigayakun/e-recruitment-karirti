# PROGRESS.md — KarirTI

> File ini diperbarui di akhir setiap fase. AI membaca ini saat sesi dilanjutkan.

## Status Fase

| Fase | Nama | Status | Tanggal |
|------|------|--------|---------|
| 0 | Fondasi & Setup | ✅ SELESAI | 2026-10-19 |
| 1 | Sistem Desain | ⏳ Belum dimulai | - |
| 2 | Mesin Animasi & Background | ⏳ Belum dimulai | - |
| 3 | Database & Seeder | ⏳ Belum dimulai | - |
| 4 | Auth, Role, Keamanan Dasar | ⏳ Belum dimulai | - |
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

## Keputusan Penting

| Fase | Keputusan |
|------|-----------|
| 0 | Tailwind v4 lewat `@tailwindcss/postcss` (bukan `@tailwindcss/vite`) karena Vite 8 tidak kompatibel dengan plugin Vite Tailwind v4 |
| 0 | PHP 8.3.35 dari `M:\Xampp\php.exe` (bukan dari PATH default XAMPP) |
| 0 | Vite tetap v8 (dari skeleton Laravel 13.37) — tidak di-downgrade |
| 0 | Font self-host via `@fontsource-variable` |

## Utang Teknis

| Item | Keterangan |
|------|-----------|
| Warning font runtime | Font woff2 tidak resolved saat build tapi berjalan normal di runtime |
| PATH PHP | Harus set `$env:PATH = "M:\Xampp;$env:PATH"` setiap sesi baru |

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
