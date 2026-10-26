# Security Checklist — KarirTI

> Berdasarkan tabel 9.1 plan.md. Diperbarui: Fase 12.

## Status: SEMUA OK ✅

| Ancaman | Kontrol | Status | Bukti |
|---------|---------|--------|-------|
| Akses tanpa login | middleware `auth`, `verified`, `active` | ✅ OK | `AuthenticationTest::test_guest_redirected_to_login` |
| Eskalasi role | Role dari controller, bukan input; `EnsureRole`; admin dari seeder | ✅ OK | `RegistrationTest::test_role_admin_from_input_is_ignored` |
| IDOR | Route model binding + Policy + scope query pemilik | ✅ OK | `CvUploadTest::test_document_idor`, `ApplicationTest::test_idor_application` |
| Brute force login | Throttle 5x per email+IP, `session()->regenerate()` | ✅ OK | `AuthenticationTest::test_rate_limit_after_five_failed_attempts` |
| Mass assignment | `$fillable` eksplisit; hanya `validated()` | ✅ OK | `DatabaseTest::test_role_not_in_user_fillable`, `test_status_not_in_application_fillable` |
| SQL injection | Eloquent/Query Builder + binding; `escapeLike()` | ✅ OK | `EscapeLikeTest` (4 kasus); `selectRaw` hanya literal — tidak ada user input |
| XSS & CSP Nonce | `{{ }}` auto-escape; `<x-safe-markdown>` strip HTML & image tracker; CSP nonce di production; 0 inline event handlers | ✅ OK | `CriticalBugsRegressionTest::test_a2_production_csp_has_valid_nonce`, `HomeTest::test_job_description_xss_is_sanitized` |
| CSRF | `@csrf` di semua form; tidak ada GET yang mengubah data | ✅ OK | Semua form view pakai `@csrf`; auto-viewed adalah satu-satunya exception GET (terdokumentasi) |
| Upload berbahaya | `mimes` + `mimetypes` + magic bytes + max size + disk private + nama UUID | ✅ OK | `CvUploadTest` (4 kasus); audit `grep disk('public')` hanya logo |
| Tautan berbahaya | `GithubUrl` rule (`\z/D`); domain LinkedIn host check; `url:https`; `rel="noopener noreferrer"` | ✅ OK | `GithubUrlTest` (8 kasus) |
| Kebocoran data pribadi | CV hanya lewat DocumentPolicy; company_note tidak ke pelamar; portofolio opt-in | ✅ OK | `CvUploadTest::test_document_idor`, `ApplicantManagementTest::test_company_note_not_visible` |
| Clickjacking & MIME sniffing | `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, CSP tanpa `unsafe-inline` | ✅ OK | `SecurityHeadersTest::test_security_headers_present`, `CriticalBugsRegressionTest::test_a2_production_csp_has_valid_nonce` |
| Spam email & Brute Force | `throttle:6,1` di pendaftaran & reset password; throttle IP 20x & email+IP 5x di login | ✅ OK | `AuthenticationTest::test_rate_limit_after_five_failed_attempts`, routes throttle |
| Penyamaran Identitas Perusahaan | Ganti `company_name` atau `website` otomatis mereset status ke `pending` | ✅ OK | `CriticalBugsRegressionTest::test_b8_company_identity_change_resets_verification` |
| Rahasia bocor | `.env` di `.gitignore`; APP_DEBUG=true hanya dev | ✅ OK | Audit `git ls-files .env` = kosong |
| CSV injection | `CsvExporter::safe()` prefix `=`, `+`, `-`, `@` dengan `'` | ✅ OK | `AdminPanelTest::test_csv_exporter_safe_escapes_formulas` |
| Enumerasi akun | Pesan reset password netral | ✅ OK | `SecurityHeadersTest::test_password_reset_message_is_neutral` |
| BR13 Admin terakhir | Cek `activeAdmins <= 1` sebelum deactivate | ✅ OK | `AdminPanelTest::test_cannot_deactivate_last_admin` |

## Audit Grep (Fase 12)

| Pemeriksaan | Hasil | Keterangan |
|-------------|-------|-----------|
| `$request->all()` | ✅ 0 temuan | Tidak ada penggunaan |
| `{!!` tidak aman | ✅ 1 temuan (safe) | Hanya di `safe-markdown.blade.php` (terdokumentasi) |
| `selectRaw/whereRaw` | ✅ 8 temuan (safe) | Semua literal SQL, tidak ada user input |
| `anime(` / `easing:` v3 | ✅ 1 temuan (safe) | Hanya di `charts.js` — itu Chart.js bukan anime.js |
| `disk('public')` | ✅ 1 temuan (safe) | Hanya untuk logo perusahaan (diizinkan) |
| `dd()/dump()/console.log` | ✅ 0 temuan aktif | False positive: bukan debug artifacts |
| `npm audit` | ✅ 0 vulnerabilities | — |
| `composer audit` | ✅ No advisories | — |

## Semua Route Panel Terlindungi

Semua route `/pelamar/*`, `/perusahaan/*`, `/admin/*` menggunakan:
- `auth` — wajib login
- `verified` — email terverifikasi
- `active` — akun aktif
- `role:applicant|company|admin` — role sesuai

Tidak ada route panel tanpa middleware lengkap.
