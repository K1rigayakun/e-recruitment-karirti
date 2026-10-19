@extends('layouts.app')

@section('title', 'Kebijakan Privasi — '.config('app.name'))

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12" data-reveal="fade-up">
    <h1 class="font-display text-4xl text-paper">Kebijakan Privasi</h1>
    <p class="mt-2 text-sm text-mist">Terakhir diperbarui: Oktober 2026</p>

    <div class="prose-karirti mt-8 space-y-6 text-mist">
        <p>{{ config('app.name') }} berkomitmen melindungi privasi pengguna sesuai prinsip Undang-Undang Perlindungan Data Pribadi (UU PDP).</p>

        <h2 class="font-display text-xl text-paper">Data yang kami kumpulkan</h2>
        <p>Kami hanya mengumpulkan data yang diperlukan: nama, email, CV, dan informasi profil yang kamu isi sendiri. Kami tidak menjual data kepada pihak ketiga.</p>

        <h2 class="font-display text-xl text-paper">Bagaimana data digunakan</h2>
        <p>Data pelamar hanya terlihat oleh perusahaan yang kamu lamar dan admin platform. Data digunakan untuk proses rekrutmen dan peningkatan layanan.</p>

        <h2 class="font-display text-xl text-paper">Hak pengguna</h2>
        <p>Kamu berhak mengakses, memperbarui, atau menghapus data pribadimu. Hubungi kami melalui email yang terdaftar untuk mengajukan permintaan.</p>

        <h2 class="font-display text-xl text-paper">Keamanan</h2>
        <p>Kami menggunakan enkripsi HTTPS, penyimpanan privat untuk CV, dan kontrol akses berbasis role untuk melindungi datamu.</p>

        <h2 class="font-display text-xl text-paper">Kontak</h2>
        <p>Pertanyaan? Hubungi: <a href="mailto:{{ config('karirti.admin_email') }}" class="link-draw text-signal">{{ config('karirti.admin_email') }}</a></p>
    </div>
</div>
@endsection
