# KarirTI

Portal bursa kerja untuk lulusan D3 Teknik Informatika.

## Prasyarat

- PHP 8.3+ (`M:\Xampp\php.exe`)
- Composer 2
- Node.js 22 LTS
- MySQL/MariaDB

## Instalasi

```bash
cd karirti
$env:PATH = "M:\Xampp;$env:PATH"
composer install
php artisan key:generate
# Edit .env: isi DB_PASSWORD jika diperlukan
php artisan migrate
npm install
npm run build
```

## Menjalankan Lokal

```bash
$env:PATH = "M:\Xampp;$env:PATH"
php artisan serve
# Buka http://localhost:8000
```

## Test

```bash
$env:PATH = "M:\Xampp;$env:PATH"
php artisan test
```

## Dokumentasi

Lihat `plan.md` untuk arsitektur lengkap, `PROGRESS.md` untuk status pengerjaan, dan `docs/` untuk ERD dan panduan lain.
