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

## Milestone v1.1.0 — Stabilisasi & Hardening Production (25 Okt 2026)

- **A1**: Portabilitas query bulanan `ReportBuilder` (MySQL `DATE_FORMAT` & SQLite `strftime`) — error 500 admin teratasi.
- **A2**: CSP Nonce diaktifkan via `Vite::useCspNonce()`, 18 inline event handlers dihapus dan digantikan delegasi `data-*` di `delegations.js`.
- **A3**: Sinkronisasi timezone `Asia/Jakarta` di `config/app.php` dan pembersihan double timezone parsing.
- **A4**: Pemisahan copy teks dan route tujuan notifikasi email `ApplicationReceived` untuk perusahaan.
- **A5**: Rendering flash message session `'toast'` pada `toast-region.blade.php`.
- **B1**: Penerapan throttling `throttle:6,1` pada pendaftaran/reset password dan limiter IP pada login.
- **B2**: Perbaikan grouped WHERE pada pencarian pengguna di `Admin/UserController`.
- **B3**: Pencegahan penimpaan ID lamaran saat join profil pelamar (`select('applications.*')`).
- **B4**: Penanganan uncheck `show_salary` dan sinkronisasi keahlian kosong di lowongan.
- **B5**: Bundling otomatis berkas font WOFF2 ke `public/build/assets/`.
- **B6**: Komponen paginasi bertema gelap KarirTI dan lokalisasi `lang/id/pagination.php`.
- **B8**: Otomasi status re-verifikasi `pending` saat perusahaan mengubah nama/website.
- **C1 - D8**: Concurrency lock pada status lamaran, guard status interview, validasi ekspor CSV, perbaikan URL GitHub/LinkedIn, dan pembersihan skeleton.

## Statistik Terkini (v1.1.0)

- **Tests**: 113 passed, 252 assertions (100% green di SQLite & MySQL)
- **Inline Event Handlers**: 0 temuan (100% CSP compliant)
- **Routes**: 83 routes terdaftar
- **npm build**: Bersih (tanpa warning font / aset)
- **npm audit**: 0 vulnerabilities
- **composer audit**: No advisories
