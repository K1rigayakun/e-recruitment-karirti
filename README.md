# KarirTI

Portal bursa kerja khusus lulusan D3 Teknik Informatika. Perusahaan memasang lowongan, pelamar melamar dengan CV PDF dan portofolio GitHub, admin prodi mengawasi dan membuat laporan.

## Fitur Utama

- **3 Role**: Admin, Perusahaan, Pelamar — masing-masing dengan hak akses terpisah
- **Upload CV PDF aman**: disk privat, magic bytes check, nama UUID
- **Portofolio GitHub**: link repo + demo, tampil sebagai "repo card"
- **Pipeline tracking**: pelacakan status lamaran terstruktur (Dilamar → Dilihat → Interview → Diterima/Ditolak)
- **E-Interview Scheduling**: jadwal online/offline, konfirmasi, reschedule
- **Notifikasi email + in-app**: notifikasi terkirim via antrean database (queue worker)
- **Laporan & ekspor CSV**: funnel status, tren per bulan, ekspor aman (anti CSV injection)
- **Animasi smooth**: anime.js v4, hero pipeline SVG, spotlight kursor
- **Keamanan**: CSRF, rate limit, CSP Nonce, audit log, BR13

## Stack

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| Backend | Laravel | 13.37.0 |
| Runtime | PHP | 8.3+ |
| Database | MySQL / MariaDB / SQLite | 10.4+ |
| Frontend CSS | Tailwind CSS | 4.3.3 |
| Animasi | anime.js | 4.5.0 |
| Chart | Chart.js | 4.x |
| Build | Vite | 8.x |
| Font | Bricolage Grotesque + Plus Jakarta Sans | Self-hosted WOFF2 |

## Instalasi & Menjalankan Proyek

### Prasyarat

- PHP >= 8.3 dengan ekstensi `pdo`, `mbstring`, `curl`, `openssl`, `fileinfo`
- Composer 2
- Node.js 22 LTS & npm
- Basis Data: MariaDB / MySQL / SQLite

### Langkah Instalasi

```bash
# 1. Clone & masuk ke folder kerja
cd karirti

# 2. Install dependensi
composer install
npm install

# 3. Konfigurasi Environment
cp .env.example .env
php artisan key:generate

# 4. Migrasi & Seed Data Demo
php artisan migrate:fresh --seed

# 5. Build Aset Frontend
npm run build
```

### Menjalankan Server & Queue Worker

Untuk memastikan notifikasi email dan in-app bekerja dengan baik, jalankan aplikasi menggunakan script simultan:

```bash
composer run dev
```

Script di atas akan menjalankan `php artisan serve`, `php artisan queue:listen`, dan `npm run dev` secara paralel.
Atau secara manual di dua terminal terpisah:
```bash
# Terminal 1: Web Server
php artisan serve

# Terminal 2: Queue Worker
php artisan queue:work
```

Buka peramban di: `http://localhost:8000`

## Akun Demo Lokal

| Role | Email | Password |
|------|-------|---------|
| Admin | admin@karirti.test | Password123! |
| Perusahaan | hrd@pt-solusi-digital-nusantara.test | Password123! |
| Pelamar | pelamar0@karirti.test | Password123! |

## Pengujian Otomatis (Automated Tests)

```bash
php artisan test
# Hasil: 113 tests passed, 252 assertions (SQLite & MySQL)
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
