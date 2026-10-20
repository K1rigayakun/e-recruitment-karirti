# QA Script — KarirTI

> 30+ skenario pengujian lintas role. Skenario bertanda ✅ bisa diotomatisasi (sudah ada tes).
> Skenario bertanda 👁️ perlu dilakukan manual oleh pemilik.

---

## A. Registrasi & Auth

| # | Skenario | Tipe | Hasil yang diharapkan |
|---|----------|------|-----------------------|
| A1 | Daftar pelamar dengan data valid | ✅ Auto | Akun terbuat, profil kosong, redirect ke verify-email |
| A2 | Daftar perusahaan dengan data valid | ✅ Auto | Akun terbuat, status pending, redirect ke verify-email |
| A3 | Daftar dengan field `role=admin` di form | ✅ Auto | Role tetap applicant (tidak bisa eskalasi) |
| A4 | Login dengan password salah | ✅ Auto | Pesan error netral, akun tidak terkunci |
| A5 | Login 6 kali gagal berturut-turut | ✅ Auto | Percobaan ke-6 mendapat error throttle |
| A6 | Akun dinonaktifkan admin lalu coba akses panel | ✅ Auto | Diredirect ke login dengan pesan akun nonaktif |
| A7 | Reset password dengan email tidak terdaftar | ✅ Auto | Pesan sama dengan email terdaftar (netral) |
| A8 | Klik tautan verifikasi email | 👁️ Manual | Status terverifikasi, bisa akses dashboard |

---

## B. Profil Pelamar & CV

| # | Skenario | Tipe | Hasil yang diharapkan |
|---|----------|------|-----------------------|
| B1 | Upload CV valid (PDF, < 2MB) | ✅ Auto | CV tersimpan, muncul di daftar |
| B2 | Upload file .txt bernama .pdf | ✅ Auto | Ditolak: magic bytes tidak cocok |
| B3 | Upload file binary bukan PDF bernama .pdf | ✅ Auto | Ditolak |
| B4 | Upload file > 2MB | ✅ Auto | Ditolak dengan pesan ukuran |
| B5 | Akses CV milik pelamar lain | ✅ Auto | 403 Forbidden |
| B6 | Tambah project dengan URL GitHub valid | ✅ Auto | Project tersimpan |
| B7 | Tambah project ke-7 (melebihi batas) | ✅ Auto | Ditolak dengan pesan batas 6 project |
| B8 | URL GitHub dengan skema `javascript:` | ✅ Auto | Ditolak rule GithubUrl |
| B9 | Aktifkan portofolio publik, akses `/p/{slug}` | ✅ Auto | Halaman tampil tanpa email/NIM/telepon |
| B10 | Portofolio non-publik diakses orang lain | ✅ Auto | 404 |
| B11 | Progress ring profil akurat | 👁️ Manual | Ring terisi % sesuai item yang dilengkapi |

---

## C. Lowongan & Melamar

| # | Skenario | Tipe | Hasil yang diharapkan |
|---|----------|------|-----------------------|
| C1 | Filter lowongan berdasarkan tipe + mode + skill | ✅ Auto | Hanya lowongan open, perusahaan approved, sesuai filter |
| C2 | Cari lowongan dengan karakter `%` dan `_` | ✅ Auto | Tidak error, escapeLike bekerja |
| C3 | Akses detail lowongan draft sebagai tamu | ✅ Auto | 404 |
| C4 | Lamar lowongan dengan CV aktif | ✅ Auto | Lamaran terbuat, history tercatat, notifikasi terkirim |
| C5 | Lamar lowongan yang sama dua kali | ✅ Auto | Ditolak: sudah melamar |
| C6 | Lamar tanpa CV aktif | ✅ Auto | Ditolak dengan pesan upload CV |
| C7 | Lamar lowongan closed | ✅ Auto | Ditolak |
| C8 | Batalkan lamaran yang masih aktif | ✅ Auto | Status Withdrawn, konfirmasi dialog muncul |
| C9 | Batalkan lamaran yang sudah diterima | ✅ Auto | 403 Forbidden |
| C10 | Deskripsi lowongan berisi `<script>alert(1)</script>` | ✅ Auto | Script tidak dieksekusi di browser |

---

## D. Sisi Perusahaan

| # | Skenario | Tipe | Hasil yang diharapkan |
|---|----------|------|-----------------------|
| D1 | Buat lowongan, simpan draft, publikasi | 👁️ Manual | Status berubah Open, muncul di halaman publik |
| D2 | Perusahaan pending coba publikasi lowongan | ✅ Auto | 403 Forbidden |
| D3 | Perusahaan lain coba edit lowongan | ✅ Auto | 403 Forbidden |
| D4 | Hapus lowongan open | ✅ Auto | 403 Forbidden |
| D5 | Upload logo SVG | ✅ Auto | Ditolak |
| D6 | Buka detail pelamar pertama kali | ✅ Auto | Status otomatis berubah ke Viewed (idempoten) |
| D7 | Terima pelamar — pelamar mendapat notifikasi | ✅ Auto | Status Accepted, notif terkirim |
| D8 | Catatan privat tidak terlihat pelamar | ✅ Auto | HTML pelamar tidak mengandung catatan |
| D9 | Jadwalkan interview dengan waktu < 1 jam | ✅ Auto | Ditolak validasi |
| D10 | Jadwalkan interview online tanpa URL | ✅ Auto | Ditolak validasi |
| D11 | Jadwalkan interview dengan URL `http://` | ✅ Auto | Ditolak (hanya https) |
| D12 | Ekspor CSV laporan perusahaan | ✅ Auto | File terbuka di Excel, BOM UTF-8, tanpa formula injection |

---

## E. Admin

| # | Skenario | Tipe | Hasil yang diharapkan |
|---|----------|------|-----------------------|
| E1 | Non-admin akses `/admin` | ✅ Auto | 403 Forbidden |
| E2 | Verifikasi perusahaan pending | ✅ Auto | Status Approved, notifikasi ke perusahaan |
| E3 | Nonaktifkan diri sendiri sebagai admin | ✅ Auto | Ditolak (BR13) |
| E4 | Nonaktifkan admin terakhir | ✅ Auto | Ditolak (BR13) |
| E5 | Turunkan lowongan open dengan alasan | 👁️ Manual | Status Closed, admin_note tersimpan |
| E6 | Ekspor CSV laporan admin | ✅ Auto | File valid, sel `=1+1` menjadi `'=1+1` |
| E7 | Periksa log audit setelah approve perusahaan | 👁️ Manual | Log tercatat di `/admin/log` |

---

## F. UI/UX & Aksesibilitas

| # | Skenario | Tipe | Hasil yang diharapkan |
|---|----------|------|-----------------------|
| F1 | Navigasi keyboard-only (Tab, Enter, Esc) | 👁️ Manual | Semua aksi bisa dilakukan tanpa mouse |
| F2 | Aktifkan "Kurangi animasi" di footer | 👁️ Manual | Semua animasi berhenti, pipeline tidak bergerak |
| F3 | Buka di HP 375px | 👁️ Manual | Tidak ada scroll horizontal, dialog mobile berfungsi |
| F4 | Tooltip/label accessible di form | 👁️ Manual | Screen reader bisa membaca label dengan benar |
| F5 | Gerakkan kursor di landing hero | 👁️ Manual | Spotlight mengikuti halus |
| F6 | Pipeline tracker di detail lamaran | 👁️ Manual | Garis tergambar sampai status aktif, node berdenyut |
| F7 | Dialog lamar di HP | 👁️ Manual | Penghitung karakter surat pengantar berfungsi |

---

## Ringkasan Otomatisasi

- Total skenario: 37
- Terotomatisasi (test ada): 27 (73%)
- Manual (perlu pemilik): 10 (27%)

Untuk menjalankan semua tes otomatis:
```bash
$env:PATH = "M:\Xampp;$env:PATH"
cd karirti
php artisan test
```
