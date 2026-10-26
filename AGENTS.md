# Pedoman Agen AI - KarirTI (E-Recruitment & E-Portofolio D3 TI)

Selamat datang di proyek **KarirTI**. Dokumen ini adalah panduan kerja standar untuk semua agen AI pelaksana.

## 1. Sumber Kebenaran
- **Spesifikasi Utama**: `plan.md` di root workspace `M:\E-Recruitment`.
- **Status Progres**: `PROGRESS.md` di root proyek `karirti`.
- **Konfigurasi Khusus AI**: Folder `.agents/` di `M:\E-Recruitment\.agents/` (`agent.md`, `context.md`, `skill.md`).

## 2. Aturan Penting
1. **Bahasa**: Selalu gunakan Bahasa Indonesia dalam setiap penjelasan.
2. **Icon vs Emoji**: Jangan pernah menggunakan emoji di UI web; gunakan icon semantik / SVG.
3. **Kebijakan Folder**: Jangan pernah menghapus folder apapun.
4. **Environment PHP**: Jalankan PHP >= 8.3 via `$env:PATH = "M:\Xampp;$env:PATH"`.
5. **Testing**:
   - Seluruh tes backend wajib memanggil `$this->withoutVite()`.
   - File testing disimpan di folder khusus testing.
6. **Keamanan & Kerapian**:
   - Jaga kepatuhan CSP (hindari inline event handler `on*=` pada Blade).
   - Seluruh perubahan status lamaran wajib melewati `ApplicationStatusService`.
   - Jadwal interview wajib melalui `InterviewScheduler`.
