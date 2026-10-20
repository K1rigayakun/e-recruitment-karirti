# Panduan Pengguna — KarirTI

Portal bursa kerja khusus lulusan D3 Teknik Informatika.

---

## Untuk Pelamar

### Daftar & Verifikasi
1. Buka `/daftar/pelamar`
2. Isi nama, email, password, dan setujui kebijakan privasi
3. Cek email — klik tautan verifikasi
4. Login ke dashboard pelamar

### Lengkapi Profil
- **Edit profil** (`/pelamar/profil`): headline, tentang saya, kota, GitHub, keahlian
- **Upload CV** (`/pelamar/cv`): seret PDF ke dropzone atau klik untuk pilih. Maks 2 MB.
- **Tambah project** (`/pelamar/proyek`): judul, deskripsi, URL GitHub (wajib), demo, tech stack

### Melamar
1. Cari lowongan di `/lowongan` (filter jenis, mode, skill)
2. Buka detail lowongan → klik "Lamar sekarang"
3. Pilih CV, tulis surat pengantar (opsional) → Kirim

### Pantau Lamaran
- Dashboard: ringkasan jumlah per status + 3 lamaran terbaru
- `/pelamar/lamaran`: daftar semua lamaran + filter status
- Detail lamaran: **pipeline tracker** menunjukkan posisi terkini

### Interview
- Jika perusahaan menjadwalkan interview, kartu interview muncul di detail lamaran
- Klik **"Konfirmasi hadir"** atau **"Ajukan jadwal ulang"** (tulis alasan)

---

## Untuk Perusahaan

### Daftar & Verifikasi Admin
1. Buka `/daftar/perusahaan` → isi data
2. Tunggu email verifikasi + persetujuan admin prodi
3. Setelah disetujui, banner pending hilang dan bisa publikasi lowongan

### Kelola Lowongan
- **Buat** (`/perusahaan/lowongan/buat`): isi judul, deskripsi (Markdown), persyaratan, lokasi, jenis, gaji
- **Simpan draft** → tinjau → **Publikasi** (hanya bisa setelah diverifikasi)
- **Tutup** lowongan yang sudah tidak aktif
- **Pratinjau**: klik "Pratinjau" dari daftar lowongan

### Tinjau Pelamar
- Dari daftar lowongan → klik "Pelamar"
- Klik nama pelamar → lihat profil, CV, portofolio
- Status otomatis berubah ke "Dilihat" saat pertama kali dibuka
- Tombol **Terima** / **Tolak** di sidebar kanan

### Jadwalkan Interview
- Di halaman detail pelamar → isi form jadwal (tanggal, durasi, mode, URL meeting)
- Pelamar otomatis mendapat notifikasi email

### Agenda Interview
- `/perusahaan/interview`: daftar + kalender bulan

### Laporan
- `/perusahaan/laporan`: funnel status + top lowongan
- Tombol "Ekspor CSV" untuk mengunduh data pelamar

---

## Untuk Admin (Prodi)

### Verifikasi Perusahaan
1. `/admin/perusahaan`: daftar perusahaan pending
2. Klik "Setujui" atau "Tolak" (isi alasan)
3. Perusahaan mendapat notifikasi email

### Kelola Pengguna
- `/admin/pengguna`: cari, filter role/status, aktifkan/nonaktifkan
- Tidak bisa menonaktifkan diri sendiri atau admin terakhir

### Moderasi Lowongan
- `/admin/lowongan`: semua lowongan dari semua perusahaan
- Klik "Turunkan" untuk menutup lowongan yang melanggar aturan

### Laporan & Ekspor
- `/admin/laporan`: funnel status, tren per bulan, diterima per tahun lulus, top lowongan
- Ekspor CSV: data semua pelamar

### Log Audit
- `/admin/log`: semua tindakan admin (approve, reject, nonaktifkan, turunkan)

---

## Tips Umum

- **Email tidak masuk?** Cek folder spam. Di lokal, cek `storage/logs/laravel.log`
- **CV tidak terupload?** Pastikan format PDF dan ukuran ≤ 2 MB
- **Tidak bisa publikasi lowongan?** Periksa status verifikasi perusahaan
- **Toggle "Kurangi animasi"** ada di footer dan menu navigasi panel
