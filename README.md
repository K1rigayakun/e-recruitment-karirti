# KarirTI

Portal bursa kerja khusus lulusan D3 Teknik Informatika. Perusahaan memasang lowongan, pelamar melamar dengan CV PDF dan portofolio GitHub, admin prodi mengawasi dan membuat laporan.

## Fitur Utama

- **3 Role**: Admin, Perusahaan, Pelamar — masing-masing dengan hak akses terpisah
- **Upload CV PDF aman**: disk privat, magic bytes check, nama UUID
- **Portofolio GitHub**: link repo + demo, tampil sebagai "repo card"
- **Pipeline tracking**: status lamaran real-time (Dilamar → Dilihat → Interview → Diterima/Ditolak)
- **E-Interview Scheduling**: jadwal online/offline, konfirmasi, reschedule
- **Notifikasi email + in-app**: semua perubahan status dikirim otomatis
- **Laporan & ekspor CSV**: funnel status, tren per bulan, ekspor aman (anti CSV injection)
- **Animasi smooth**: anime.js v4, hero pipeline SVG, spotlight kursor
- **Keamanan**: CSRF, rate limit, CSP, audit log, BR13

## Stack

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| Backend | Laravel | 13.37.0 |
| Runtime | PHP | 8.3.35 |
| Database | MySQL/MariaDB | 10.4+ |
| Frontend CSS | Tailwind CSS | 4.3.3 |
| Animasi | anime.js | 4.5.0 |
| Chart | Chart.js | 4.x |
| Build | Vite | 8.x |
| Font | Bricolage Grotesque + Plus Jakarta Sans | self-host |

## Instalasi Lokal (Windows)

### Prasyarat

- PHP 8.3+ di `M:\Xampp\php.exe` (atau sesuai lokasi Anda)
- Composer 2
- Node.js 22 LTS
- MySQL/MariaDB

### Langkah

```powershell
# Set PATH PHP 8.3
$env:PATH = "M:\Xampp;$env:PATH"

# Clone/masuk ke folder
cd karirti

# Install dependensi
composer install
npm install

# Konfigurasi
cp .env.example .env
php artisan key:generate

# Edit .env: isi DB_CONNECTION, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Buat database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS karirti CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Migrasi + seed data demo
php artisan migrate --seed

# Build aset
npm run build

# Jalankan server
php artisan serve
```

Buka: `http://localhost:8000`

## Akun Demo Lokal

| Role | Email | Password |
|------|-------|---------|
| Admin | admin@karirti.test | Password123! |
| Perusahaan 1 | hrd@pt-solusi-digital-nusantara.test | Password123! |
| Pelamar | pelamar0@karirti.test | Password123! |

> **Jangan gunakan akun demo di production!**

## Test

```powershell
$env:PATH = "M:\Xampp;$env:PATH"
cd karirti
php artisan test
# Hasil: 100 tests, 210 assertions
```

## Struktur Utama

```
app/
  Enums/           # UserRole, ApplicationStatus, dll.
  Http/
    Controllers/   # Admin/, Applicant/, Company/, Auth/, Public/
    Middleware/    # EnsureRole, EnsureAccountActive, SecurityHeaders
    Requests/      # FormRequest per area
  Models/          # User, Application, JobPosting, dll.
  Notifications/   # Email + database notifications
  Policies/        # Authorization per model
  Rules/           # GithubUrl
  Services/        # ApplicationStatusService, InterviewScheduler, dll.
resources/
  css/app.css      # Tailwind v4 + token desain
  js/
    app.js         # Entry point
    motion/        # anime.js modules (E01-E18)
    ui/            # Dialog, dropdown, toast, dll.
    charts.js      # Chart.js (dimuat dinamis)
  views/           # Blade templates per role
docs/              # ERD, deploy, security, QA, user-guide, demo-script
```

## Dokumentasi

| Dokumen | Path |
|---------|------|
| ERD | `docs/erd.md` |
| Deploy | `docs/deploy.md` |
| Security Checklist | `docs/security-checklist.md` |
| QA Script | `docs/qa-script.md` |
| Motion Guide | `docs/motion.md` |
| User Guide | `docs/user-guide.md` |
| Demo Script | `docs/demo-script.md` |
| Progress | `PROGRESS.md` |
