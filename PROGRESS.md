# PROGRESS.md — KarirTI v1.0.0

> Semua fase selesai. Proyek siap demo dan deploy.

## Status Fase

| Fase | Nama | Status | Tanggal |
|------|------|--------|---------|
| 0 | Fondasi & Setup | ✅ SELESAI | 2026-10-19 |
| 1 | Sistem Desain | ✅ SELESAI | 2026-10-19 |
| 2 | Mesin Animasi & Background | ✅ SELESAI | 2026-10-19 |
| 3 | Database & Seeder | ✅ SELESAI | 2026-10-19 |
| 4 | Auth, Role, Keamanan Dasar | ✅ SELESAI | 2026-10-19 |
| 5 | Landing & Lowongan Publik | ✅ SELESAI | 2026-10-19 |
| 6 | Area Pelamar | ✅ SELESAI | 2026-10-19 |
| 7 | Area Perusahaan | ✅ SELESAI | 2026-10-19 |
| 8 | Melamar & Tracking Status | ✅ SELESAI | 2026-10-19 |
| 9 | Kelola Pelamar | ✅ SELESAI | 2026-10-19 |
| 10 | E-Interview Scheduling | ✅ SELESAI | 2026-10-20 |
| 11 | Admin & Laporan | ✅ SELESAI | 2026-10-20 |
| 12 | Hardening, Test & QA | ✅ SELESAI | 2026-10-20 |
| 13 | Polish, Deploy & Dokumentasi | ✅ SELESAI | 2026-10-20 |

## Cara Menjalankan Proyek

```powershell
$env:PATH = "M:\Xampp;$env:PATH"
cd karirti
php artisan migrate:fresh --seed
php artisan serve
# Buka http://localhost:8000
```

## Akun Demo Lokal

| Role | Email | Password |
|------|-------|---------|
| Admin | admin@karirti.test | Password123! |
| Perusahaan | hrd@pt-solusi-digital-nusantara.test | Password123! |
| Pelamar | pelamar0@karirti.test | Password123! |

## Versi Stack

| Stack | Versi |
|-------|-------|
| PHP | 8.3.35 (`M:\Xampp\php.exe`) |
| Laravel | 13.37.0 |
| Vite | 8.3.4 |
| Tailwind CSS | 4.3.3 |
| anime.js | 4.5.0 |
| Chart.js | 4.x |
| Node.js | 22.14.0 |
| MySQL/MariaDB | 10.4.32 |

## Keputusan Teknis Penting

| Fase | Keputusan |
|------|-----------|
| 0 | Tailwind v4 lewat `@tailwindcss/postcss` (bukan `@tailwindcss/vite`) — Vite 8 tidak kompatibel |
| 0 | PHP 8.3.35 di `M:\Xampp\php.exe` — set `$env:PATH` setiap sesi |
| 6 | CV di disk `local` (privat), nama UUID, magic bytes check |
| 8 | `ApplicationStatusService` — semua perubahan status wajib lewat service |
| 10 | `InterviewScheduler` — satu-satunya yang boleh set status Interview (BR7) |
| 10 | Cek bentrok interview di PHP (bukan SQL) agar kompatibel SQLite di test |
| 11 | Chart.js dimuat dinamis lewat `import()` hanya jika ada `[data-chart]` |

## Statistik Akhir

- **Tests**: 100 passed, 210 assertions
- **Files PHP**: 163 files (Pint bersih)
- **Routes**: ~70 routes terdaftar
- **npm audit**: 0 vulnerabilities
- **composer audit**: No advisories
