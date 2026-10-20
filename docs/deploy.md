# Panduan Deploy — KarirTI

## Prasyarat Server

- PHP 8.3+ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `ctype`, `tokenizer`, `xml`, `curl`, `zip`
- MySQL 8.0+ atau MariaDB 10.6+
- Node.js 22 LTS (untuk build aset)
- Composer 2
- Web server: Nginx atau Apache
- Supervisor (untuk queue worker)

## Langkah Deploy

### 1. Upload kode

```bash
git clone <repo-url> /var/www/karirti
cd /var/www/karirti
```

### 2. Install dependensi PHP (tanpa dev)

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Build aset frontend

```bash
npm ci && npm run build
```

### 4. Konfigurasi .env production

```bash
cp .env.example .env
```

Edit `.env` dengan nilai production:

```dotenv
APP_NAME="KarirTI"
APP_ENV=production
APP_DEBUG=false
APP_KEY=           # akan di-generate di langkah 5
APP_URL=https://karirti.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=karirti
DB_USERNAME=karirti_user
DB_PASSWORD=<strong_password>

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
MAIL_MAILER=smtp
MAIL_HOST=<smtp_host>
MAIL_PORT=465
MAIL_USERNAME=<smtp_user>
MAIL_PASSWORD=<smtp_pass>
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"

CV_MAX_KB=2048
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD=<strong_admin_pass>
```

### 5. Generate app key (SEKALI saja)

```bash
php artisan key:generate
```

### 6. Jalankan migrasi

```bash
php artisan migrate --force
```

### 7. Seed admin (isi ADMIN_EMAIL + ADMIN_PASSWORD di .env dulu)

```bash
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=SkillSeeder
```

Setelah seeder selesai, **hapus ADMIN_PASSWORD dari .env** (ganti dengan kosong).

### 8. Buat symlink storage

```bash
php artisan storage:link
```

### 9. Set izin folder

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 10. Optimize

```bash
php artisan optimize
php artisan view:cache
```

## Konfigurasi Web Server

### Nginx

```nginx
server {
    listen 443 ssl http2;
    server_name karirti.yourdomain.com;
    root /var/www/karirti/public;
    index index.php;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Blokir akses ke file sensitif
    location ~ /\. { deny all; }
    location ~ /(storage/app|bootstrap/cache) { deny all; }
}
```

### .htaccess (Apache)

Laravel sudah menyertakan `public/.htaccess` yang benar.

## Queue Worker (Supervisor)

Simpan ke `/etc/supervisor/conf.d/karirti-worker.conf`:

```ini
[program:karirti-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/karirti/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/karirti-worker.log
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start karirti-worker:*
```

## Scheduler (Cron)

```cron
* * * * * www-data php /var/www/karirti/artisan schedule:run >> /dev/null 2>&1
```

## Backup

```bash
# Database
mysqldump -u karirti_user -p karirti > backup/db_$(date +%Y%m%d).sql

# File private (CV)
tar -czf backup/storage_$(date +%Y%m%d).tar.gz storage/app/private/
```

## Checklist Pasca-Deploy

- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_ENV=production` di `.env`
- [ ] `SESSION_SECURE_COOKIE=true` di `.env`
- [ ] HTTPS aktif dengan sertifikat valid
- [ ] `php artisan optimize` sudah dijalankan
- [ ] `php artisan storage:link` sudah dijalankan
- [ ] Queue worker berjalan (`supervisorctl status karirti-worker:*`)
- [ ] Email test terkirim (kirim dari panel admin)
- [ ] Login admin berfungsi
- [ ] Upload CV berfungsi (coba upload dari akun pelamar)
- [ ] **Hapus ADMIN_PASSWORD dari `.env`** setelah seeder
