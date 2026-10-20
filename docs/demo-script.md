# Demo Script — KarirTI (7 menit)

> Skrip untuk presentasi/gladi. Buka browser dalam mode fullscreen. Gunakan data demo lokal.
> Akun demo: password semua `Password123!`

---

## Persiapan (sebelum presentasi)

```bash
$env:PATH = "M:\Xampp;$env:PATH"
cd karirti
php artisan migrate:fresh --seed
php artisan serve
```

Buka `http://localhost:8000` di browser.

---

## Menit 0:00 — Pengantar (30 detik)

> "KarirTI adalah portal bursa kerja khusus lulusan D3 Teknik Informatika. Ada tiga role: Admin prodi, Perusahaan, dan Pelamar. Saya akan demo alur lengkap dari mendaftar sampai diterima kerja."

Tampilkan: **halaman landing** `/`

- Tunjukkan pipeline git graph bergerak di hero
- Angka statistik menghitung naik
- Marquee skill

---

## Menit 0:30 — Registrasi Pelamar (1 menit)

> "Pelamar daftar dengan email."

1. Klik **Daftar** → `/daftar/pelamar`
2. Isi: Nama `Budi Santoso`, email `budi@demo.test`, password `Password123!`, centang privasi
3. Submit → halaman verify-email
4. Cek log: `tail storage/logs/laravel.log` (atau tunjukkan URL verifikasi)
5. Buka URL verifikasi → dashboard pelamar

> "Setelah verifikasi, pelamar langsung masuk dashboard."

---

## Menit 1:30 — Lengkapi Profil & Upload CV (1 menit)

> "Progress ring menunjukkan kelengkapan profil."

1. Klik **Edit profil** → isi headline, kota, pilih 3 skill
2. Klik **CV** → seret file PDF demo → Upload berhasil
3. Klik **Portofolio** → tambah project GitHub

> "CV tersimpan aman di server, tidak bisa diakses langsung."

---

## Menit 2:30 — Cari & Lamar Lowongan (1 menit)

> "Sekarang kita cari lowongan."

1. Klik **Cari lowongan** → `/lowongan`
2. Filter: Jenis = Magang, tunjukkan filter sidebar
3. Klik salah satu kartu lowongan → halaman detail
4. Klik **Lamar sekarang** → dialog lamar muncul
5. Tulis surat pengantar singkat → Kirim lamaran

> "Lamaran terkirim. Status pipeline langsung tampil."

---

## Menit 3:30 — Sisi Perusahaan (1.5 menit)

> "Beralih ke akun perusahaan."

1. Logout → login sebagai perusahaan (gunakan akun dari seeder: `hrd@pt-solusi-digital-nusantara.test`)
2. `/perusahaan` — dashboard dengan statistik count-up
3. `/perusahaan/lowongan/{job}/pelamar` — buka daftar pelamar
4. Klik nama Budi → detail pelamar
   - Status otomatis berubah ke "Dilihat"
   - Lihat CV, portofolio GitHub
5. Klik **Jadwalkan interview** → isi tanggal besok, 60 menit, online, URL meet
6. Submit → kartu interview muncul

> "Pelamar otomatis mendapat notifikasi email."

---

## Menit 5:00 — Tracker & Konfirmasi Pelamar (45 detik)

> "Kembali ke akun pelamar."

1. Login kembali sebagai Budi
2. `/pelamar/lamaran` → klik lamaran
3. Tunjukkan **pipeline tracker** — status sudah Interview
4. Klik **Konfirmasi hadir** di kartu interview

> "Sekarang status interview: Dikonfirmasi."

---

## Menit 5:45 — Admin & Laporan (45 detik)

> "Terakhir, panel admin prodi."

1. Login sebagai `admin@karirti.test`
2. `/admin` — dashboard dengan grafik tren
3. `/admin/perusahaan` — tunjukkan antrian verifikasi
4. `/admin/laporan` → klik **Ekspor CSV**

> "Admin bisa mengunduh laporan seluruh aktivitas platform."

---

## Menit 6:30 — Penutup (30 detik)

> "KarirTI dibangun dengan Laravel 13, Tailwind CSS v4, anime.js v4, dan Chart.js. Semua data aman: CV tersimpan di disk privat, role tidak bisa diescalate, setiap aksi admin tercatat di log audit. Terima kasih."

Tampilkan: **halaman landing** dengan spotlight bergerak.

---

## Akun Demo Lokal

| Role | Email | Password |
|------|-------|---------|
| Admin | admin@karirti.test | Password123! |
| Perusahaan 1 | hrd@pt-solusi-digital-nusantara.test | Password123! |
| Pelamar | pelamar0@karirti.test | Password123! |
