# Dokumentasi Motion — KarirTI

> Semua animasi memakai **anime.js v4** dengan named import. Tidak ada `anime()` versi v3.

## Prinsip

1. Satu momen signature (E01 hero pipeline, E17 tracker) — paling kaya.
2. Satu latar ambient tenang (E02 spotlight, E03 glow) — pelan dan redup.
3. Interaksi menjawab aksi pengguna (hover, press, fokus, dialog, toast) — cepat 120–400 ms.
4. Scroll storytelling bervariasi per section — tidak seragam.
5. Perubahan status selalu dianimasikan.

Animasikan hanya `transform` dan `opacity`. Jangan `box-shadow` atau `width` (kecuali `clipPath` di `scale-clip`).

## File Motion (`resources/js/motion/`)

| File | Fungsi | Efek |
|------|--------|------|
| `tokens.js` | Durasi, easing, stagger | — |
| `prefs.js` | Baca/tulis `motion-pref`, set `data-motion`, `isReduced()` | Toggle |
| `scope.js` | `createScope` global, `revert()` saat `pagehide` | — |
| `index.js` | `initMotion()` memanggil semua modul | — |
| `background.js` | Spotlight kursor + glow melayang | E02, E03 |
| `reveal.js` | Reveal scroll: `fade-up`, `stagger`, `split`, `scale-clip` | E14 |
| `buttons.js` | Magnetic button | E06 |
| `tilt.js` | Tilt kartu 3D | E07 |
| `counters.js` | Count-up angka | E15 |
| `page-enter.js` | Fade masuk halaman + scroll progress + navbar morph | E05, E04 |
| `pipeline.js` | Garis tergambar + node animasi + pulse aktif | E17 |

## Atribut `data-*` (hook Blade)

| Atribut | Efek |
|---------|------|
| `data-reveal="fade-up"` | Fade + geser ke atas saat masuk viewport |
| `data-reveal="stagger"` | Anak-anak muncul bergantian |
| `data-reveal="split"` | Heading muncul per kata (clip) |
| `data-reveal="scale-clip"` | Muncul dengan scale + clip path |
| `data-magnetic` | Tombol mengikuti kursor sedikit (hover) |
| `data-tilt` | Kartu tilt 3D saat hover |
| `data-count-to="N"` | Angka menghitung naik ke N |
| `data-page-enter` | Elemen masuk saat halaman dimuat |
| `data-scroll-progress` | Bar progress scroll (letakkan di nav) |
| `data-nav` | Navbar morfis saat scroll |
| `data-pipeline data-status="applied"` | Tracker pipeline animasi |
| `data-ambient` | Aktifkan glow melayang di halaman ini |
| `data-ambient-glows` | Container glow melayang |
| `data-glow` | Elemen glow individual |
| `data-motion-toggle` | Tombol toggle reduced motion |
| `data-motion-toggle-label` | Label teks tombol toggle |

## Cara Menambah Efek Baru

1. Buat file `resources/js/motion/namaefek.js` — ekspor `initNamaEfek()`.
2. Import dan panggil di `motion/index.js`.
3. Gunakan `isReduced()` dari `prefs.js` untuk lewati animasi jika reduced.
4. Definisikan `data-*` atribut dan dokumentasikan di sini.
5. Tambahkan demo ke `/styleguide` dengan tombol "Putar ulang".

## Aturan Performa

- Jangan animasikan properti layout (`width`, `height`, `margin`, `padding`).
- `box-shadow` dianimasikan lewat `opacity` pseudo-element.
- Stagger: maksimal 12 elemen pertama, total tidak lebih dari 900 ms.
- Loop ambient: 18–40 detik.
- Pause animasi saat `document.hidden` (tab tidak aktif).
- Efek tilt dan magnetic nonaktif di perangkat sentuh (`pointer: coarse`).

## Reduced Motion

Saat `data-motion="reduced"` aktif (atau sistem `prefers-reduced-motion`):
- Reveal langsung tampil (tanpa animasi).
- Count-up langsung tampil angka akhir.
- Pipeline menampilkan state akhir.
- Tilt, magnetic, glow, spotlight tidak aktif.
- Toast dan dialog tetap muncul tanpa animasi.

Toggle di footer dan menu pengguna: `data-motion-toggle` + `data-motion-toggle-label`.
