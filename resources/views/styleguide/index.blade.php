@extends('layouts.app')

@section('title', 'Sistem desain')

@section('content')
    <div class="mx-auto max-w-6xl space-y-16 px-4 py-12">
        <header class="space-y-3">
            <h1 class="font-display text-4xl text-paper">Sistem desain</h1>
            <p class="measure text-mist">Token, komponen, dan perilaku UI native. Animasi lanjutan belum aktif.</p>
            <p class="flex flex-wrap gap-3 text-sm">
                <a class="link-draw text-signal" href="{{ route('styleguide.layout.app') }}">Layout publik</a>
                <a class="link-draw text-signal" href="{{ route('styleguide.layout.guest') }}">Layout tamu</a>
                <a class="link-draw text-signal" href="{{ route('styleguide.layout.panel') }}">Layout panel</a>
            </p>
        </header>

        <section class="space-y-4" aria-labelledby="palet">
            <h2 id="palet" class="font-display text-2xl">Palet</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-ink"></div><p class="px-3 py-2 text-sm text-mist">Latar · ink</p></div>
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-ink-raised"></div><p class="px-3 py-2 text-sm text-mist">Kartu · ink-raised</p></div>
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-paper"></div><p class="px-3 py-2 text-sm text-mist">Teks · paper</p></div>
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-mist"></div><p class="px-3 py-2 text-sm text-mist">Sekunder · mist</p></div>
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-signal"></div><p class="px-3 py-2 text-sm text-mist">Aksen · signal</p></div>
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-sun"></div><p class="px-3 py-2 text-sm text-mist">CTA · sun</p></div>
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-mint"></div><p class="px-3 py-2 text-sm text-mist">Sukses · mint</p></div>
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-violet"></div><p class="px-3 py-2 text-sm text-mist">Interview · violet</p></div>
                <div class="overflow-hidden rounded-card border border-ink-line"><div class="h-16 bg-coral"></div><p class="px-3 py-2 text-sm text-mist">Bahaya · coral</p></div>
            </div>
        </section>

        <section class="space-y-4" aria-labelledby="tipografi">
            <h2 id="tipografi" class="font-display text-2xl">Tipografi</h2>
            <p class="font-display text-hero text-paper">Lulusan D3 TI, langsung ke lowongan yang cocok.</p>
            <p class="measure text-mist">Unggah CV, tautkan project GitHub, lalu pantau setiap tahap lamaranmu sampai keputusan akhir.</p>
            <p class="text-sm text-mist">Skala: 0.875 → 3.25 rem. Heading memakai Bricolage Grotesque.</p>
        </section>

        <section class="space-y-4" aria-labelledby="tombol">
            <h2 id="tombol" class="font-display text-2xl">Tombol</h2>
            <div class="flex flex-wrap gap-3">
                <x-ui.button variant="primary">Lamar sekarang</x-ui.button>
                <x-ui.button variant="secondary">Simpan perubahan</x-ui.button>
                <x-ui.button variant="ghost">Batal</x-ui.button>
                <x-ui.button variant="danger">Batalkan lamaran</x-ui.button>
                <x-ui.button variant="primary" disabled>Disabled</x-ui.button>
                <x-ui.button variant="primary" loading loading-text="Menyimpan...">Simpan</x-ui.button>
            </div>
        </section>

        <section class="space-y-4" aria-labelledby="form">
            <h2 id="form" class="font-display text-2xl">Form</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <x-ui.input name="nama" label="Nama lengkap" />
                <x-ui.input name="email" type="email" label="Email" hint="Pakai email aktif." />
                <x-ui.input name="email_error" type="email" label="Email (error)" error="Format email tidak valid." value="bukan-email" />
                <x-ui.input name="kota" label="Kota (disabled)" disabled value="Bandung" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="tentang" label="Tentang saya" />
                </div>
                <x-ui.select name="tipe" label="Jenis lowongan" :options="['internship' => 'Magang', 'full_time' => 'Penuh waktu']" />
                <x-ui.chip-select
                    label="Keahlian"
                    name="skills"
                    :options="['laravel' => 'Laravel', 'php' => 'PHP', 'mysql' => 'MySQL', 'js' => 'JavaScript']"
                    :selected="['php']"
                />
            </div>
        </section>

        <section class="space-y-4" aria-labelledby="badge">
            <h2 id="badge" class="font-display text-2xl">Badge</h2>
            <div class="flex flex-wrap gap-2">
                <x-ui.badge tone="signal">Dilamar</x-ui.badge>
                <x-ui.badge tone="sun">Dilihat</x-ui.badge>
                <x-ui.badge tone="violet">Interview</x-ui.badge>
                <x-ui.badge tone="mint">Diterima</x-ui.badge>
                <x-ui.badge tone="coral">Ditolak</x-ui.badge>
                <x-ui.badge tone="mist">Draft</x-ui.badge>
            </div>
        </section>

        <section class="space-y-4" aria-labelledby="kartu">
            <h2 id="kartu" class="font-display text-2xl">Kartu</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <x-job-card />
                <x-project-card />
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <x-ui.stat label="Lowongan" value="128" hint="Aktif minggu ini" />
                <x-ui.stat label="Perusahaan" value="54" />
                <x-ui.stat label="Pelamar" value="310" />
            </div>
            <x-ui.progress-ring :value="70" />
            <x-ui.pipeline status="interview" :dates="['applied' => '12 Okt', 'viewed' => '13 Okt', 'interview' => 'Kamis 16.00 WIB']" />
        </section>

        <section class="space-y-4" aria-labelledby="empty">
            <h2 id="empty" class="font-display text-2xl">Empty state & skeleton</h2>
            <x-ui.empty-state title="Belum ada lamaran." description="Jelajahi lowongan dan kirim lamaran pertamamu.">
                <x-slot:action>
                    <x-ui.button variant="primary" href="{{ url('/') }}">Cari lowongan</x-ui.button>
                </x-slot:action>
            </x-ui.empty-state>
            <x-ui.skeleton :lines="4" />
        </section>

        <section class="space-y-4" aria-labelledby="overlay">
            <h2 id="overlay" class="font-display text-2xl">Dialog, dropdown, toast, dropzone</h2>
            <div class="flex flex-wrap gap-3">
                <x-ui.button variant="secondary" data-dialog-open="contoh-dialog">Buka dialog</x-ui.button>
                <x-ui.dropdown label="Aksi" id="demo-dropdown">
                    <a href="{{ url('/') }}" class="block px-3 py-2 text-sm text-paper hover:bg-ink" role="menuitem">Lihat profil</a>
                    <a href="{{ url('/') }}" class="block px-3 py-2 text-sm text-paper hover:bg-ink" role="menuitem">Salin tautan</a>
                </x-ui.dropdown>
                <x-ui.button variant="primary" data-toast-trigger="Lamaran terkirim." data-toast-tone="mint">Tampilkan toast</x-ui.button>
            </div>
            <x-ui.modal id="contoh-dialog" title="Batalkan lamaran ini?">
                Kamu tidak bisa melamar lagi ke lowongan yang sama.
                <x-slot:actions>
                    <x-ui.button variant="danger" data-dialog-close>Batalkan lamaran</x-ui.button>
                </x-slot:actions>
            </x-ui.modal>
            <x-ui.file-drop />
        </section>

        <section class="space-y-4" aria-labelledby="markdown">
            <h2 id="markdown" class="font-display text-2xl">Markdown aman</h2>
            <x-safe-markdown :text="'**CV PDF** dan [GitHub](https://github.com). <script>alert(1)</script> tidak boleh jalan.'" />
        </section>
    </div>
@endsection
