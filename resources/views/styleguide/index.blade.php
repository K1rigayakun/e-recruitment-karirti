@extends('layouts.app', ['ambient' => true])

@section('title', 'Sistem desain')

@section('content')
    <div class="mx-auto max-w-6xl space-y-16 px-4 py-12">
        <header class="space-y-3">
            <h1 class="font-display text-4xl text-paper">Sistem desain</h1>
            <p class="measure text-mist">Token, komponen, perilaku UI native, dan Motion Lab (E02–E15, E17, E18).</p>
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

        {{-- ======================================================
             MOTION LAB (Fase 2)
             Setiap efek punya tombol "Putar ulang" dan switch reduced motion
             ====================================================== --}}
        <section class="space-y-4 rounded-sheet border border-signal/20 p-6" aria-labelledby="motion-lab">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="motion-lab" class="font-display text-2xl text-paper">Motion Lab</h2>
                    <p class="mt-1 text-sm text-mist">Uji efek E02–E15, E17, E18. Aktifkan switch untuk tes reduced-motion.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-mist">Reduced motion:</span>
                    <button type="button"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-chip border-2 border-ink-line bg-ink-raised transition-colors focus-visible:outline-2 focus-visible:outline-signal"
                        role="switch" aria-checked="false" data-motion-toggle aria-label="Toggle reduced motion"
                        id="motion-toggle-switch">
                        <span class="pointer-events-none inline-block h-5 w-5 translate-x-0 rounded-full bg-mist shadow-sm transition-transform duration-200"
                            id="motion-toggle-thumb"></span>
                    </button>
                    <span class="text-sm text-paper" data-motion-toggle-label>Kurangi animasi</span>
                </div>
            </div>

            {{-- E02 Spotlight --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E02</span>
                    <h3 class="text-sm font-semibold text-paper">Spotlight kursor</h3>
                </div>
                <div class="relative overflow-hidden rounded-card border border-ink-line p-8 text-center text-sm text-mist"
                    style="background-color:var(--color-ink)">
                    <div class="pointer-events-none absolute inset-0"
                        style="background: radial-gradient(200px circle at var(--mx,50%) var(--my,30%), rgb(79 140 255/.18), transparent 70%)"></div>
                    <p class="relative">Gerakkan kursor di area ini untuk melihat efek spotlight.</p>
                </div>
            </div>

            {{-- E03 Glow ambient --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E03</span>
                    <h3 class="text-sm font-semibold text-paper">Glow ambient</h3>
                    <span class="text-xs text-mist">(aktif jika halaman memakai <code class="text-signal">data-ambient</code>)</span>
                </div>
                <a href="{{ route('styleguide.layout.app') }}" class="link-draw text-sm text-signal">
                    Lihat di Layout publik →
                </a>
            </div>

            {{-- E05 Page enter --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E05</span>
                    <h3 class="text-sm font-semibold text-paper">Page enter</h3>
                </div>
                <div class="rounded-card border border-ink-line p-4">
                    <p class="text-sm text-mist">Muat ulang halaman ini. Konten masuk dengan fade+slide 360 ms.</p>
                </div>
            </div>

            {{-- E04 Scroll progress --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E04</span>
                    <h3 class="text-sm font-semibold text-paper">Scroll progress + navbar morph</h3>
                </div>
                <div class="rounded-card border border-ink-line p-4">
                    <p class="text-sm text-mist">Scroll ke bawah — garis tipis tumbuh di atas navbar, dan navbar menjadi "glass".</p>
                </div>
            </div>

            {{-- E06 Magnetic --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E06</span>
                    <h3 class="text-sm font-semibold text-paper">Tombol magnetic</h3>
                </div>
                <div class="rounded-card border border-ink-line p-6 text-center">
                    <x-ui.button variant="primary" data-magnetic>Hover perlahan di sini</x-ui.button>
                </div>
            </div>

            {{-- E07 Tilt kartu --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E07</span>
                    <h3 class="text-sm font-semibold text-paper">Tilt kartu</h3>
                </div>
                <div class="max-w-xs">
                    <x-job-card class="" />
                </div>
            </div>

            {{-- E09 Form shake + centang --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E09</span>
                    <h3 class="text-sm font-semibold text-paper">Form shake & centang</h3>
                </div>
                <form class="flex max-w-sm flex-col gap-3" data-validate-demo
                    onsubmit="event.preventDefault(); document.getElementById('e09-success').hidden=false; setTimeout(()=>document.getElementById('e09-success').hidden=true, 3000)">
                    <x-ui.input name="e09_email" type="email" label="Email" required placeholder="contoh@email.com" />
                    <div class="flex gap-2">
                        <x-ui.button variant="primary" type="submit">Kirim</x-ui.button>
                        <x-ui.button variant="ghost" type="button"
                            onclick="document.querySelector('[name=e09_email]').value=''; document.querySelector('[name=e09_email]').dispatchEvent(new Event('invalid'))">
                            Coba error
                        </x-ui.button>
                    </div>
                    <p id="e09-success" hidden class="text-sm text-mint">✓ Berhasil! Toast akan tampil di app sungguhan.</p>
                </form>
            </div>

            {{-- E10 Dropzone --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E10</span>
                    <h3 class="text-sm font-semibold text-paper">Dropzone file</h3>
                </div>
                <x-ui.file-drop name="demo_cv" accept=".pdf" label="Seret PDF ke sini atau" />
            </div>

            {{-- E11 Toast --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E11</span>
                    <h3 class="text-sm font-semibold text-paper">Toast</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-ui.button variant="ghost" data-toast-trigger="Lamaran terkirim." data-toast-tone="mint">Toast sukses</x-ui.button>
                    <x-ui.button variant="ghost" data-toast-trigger="Gagal menyimpan. Coba lagi." data-toast-tone="coral">Toast error</x-ui.button>
                    <x-ui.button variant="ghost" data-toast-trigger="Perubahan disimpan." data-toast-tone="signal">Toast info</x-ui.button>
                    <x-ui.button variant="ghost" data-toast-trigger="Ingat tenggat lamaranmu!" data-toast-tone="sun">Toast peringatan</x-ui.button>
                </div>
            </div>

            {{-- E12 Dialog --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E12</span>
                    <h3 class="text-sm font-semibold text-paper">Dialog (modal)</h3>
                </div>
                <x-ui.button variant="secondary" data-dialog-open="motion-lab-dialog">Buka dialog</x-ui.button>
                <x-ui.modal id="motion-lab-dialog" title="Dialog dengan animasi">
                    Muncul dengan scale + fade. Tutup lewat tombol, klik backdrop, atau Escape.
                    <x-slot:actions>
                        <x-ui.button variant="secondary" data-dialog-close>Tutup</x-ui.button>
                    </x-slot:actions>
                </x-ui.modal>
            </div>

            {{-- E13 Dropdown --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E13</span>
                    <h3 class="text-sm font-semibold text-paper">Dropdown</h3>
                </div>
                <x-ui.dropdown label="Pilih aksi" id="motion-lab-dropdown">
                    <button class="block w-full px-3 py-2 text-left text-sm text-paper hover:bg-ink" role="menuitem"
                        data-toast-trigger="Profil dilihat." data-toast-tone="signal">Lihat profil</button>
                    <button class="block w-full px-3 py-2 text-left text-sm text-paper hover:bg-ink" role="menuitem"
                        data-toast-trigger="Tautan disalin!" data-toast-tone="mint">Salin tautan</button>
                </x-ui.dropdown>
            </div>

            {{-- E14 Reveal scroll --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E14</span>
                    <h3 class="text-sm font-semibold text-paper">Reveal scroll — 4 pola</h3>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <p class="text-xs text-mist">fade-up (default)</p>
                        <div class="rounded-card border border-ink-line p-4" data-reveal="fade-up">
                            <p class="text-sm text-paper">Elemen ini muncul fade + geser ke atas.</p>
                        </div>
                        <x-ui.button variant="ghost" size="sm" data-replay="[data-reveal='fade-up']">Putar ulang</x-ui.button>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs text-mist">stagger (anak-anak)</p>
                        <div class="rounded-card border border-ink-line p-2" data-reveal="stagger">
                            <div class="p-2 text-sm text-paper">Item 1</div>
                            <div class="p-2 text-sm text-paper">Item 2</div>
                            <div class="p-2 text-sm text-paper">Item 3</div>
                        </div>
                        <x-ui.button variant="ghost" size="sm" data-replay="[data-reveal='stagger']">Putar ulang</x-ui.button>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs text-mist">split (per kata)</p>
                        <div class="text-xl font-display text-paper" data-reveal="split">
                            Teks muncul kata per kata
                        </div>
                        <x-ui.button variant="ghost" size="sm" data-replay="[data-reveal='split']">Putar ulang</x-ui.button>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs text-mist">scale-clip</p>
                        <div class="rounded-card border border-ink-line bg-ink-raised p-4" data-reveal="scale-clip">
                            <p class="text-sm text-paper">Muncul dengan clip + scale.</p>
                        </div>
                        <x-ui.button variant="ghost" size="sm" data-replay="[data-reveal='scale-clip']">Putar ulang</x-ui.button>
                    </div>
                </div>
            </div>

            {{-- E15 Count-up --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E15</span>
                    <h3 class="text-sm font-semibold text-paper">Count-up</h3>
                </div>
                <div class="flex flex-wrap gap-4">
                    <div class="rounded-card border border-ink-line p-4 text-center">
                        <p class="font-display text-3xl text-signal" data-count-to="128">128</p>
                        <p class="text-xs text-mist">Lowongan</p>
                    </div>
                    <div class="rounded-card border border-ink-line p-4 text-center">
                        <p class="font-display text-3xl text-sun" data-count-to="54">54</p>
                        <p class="text-xs text-mist">Perusahaan</p>
                    </div>
                    <div class="rounded-card border border-ink-line p-4 text-center">
                        <p class="font-display text-3xl text-mint" data-count-to="310">310</p>
                        <p class="text-xs text-mist">Pelamar</p>
                    </div>
                </div>
                <x-ui.button variant="ghost" size="sm" data-replay="[data-count-to='128']">Putar ulang</x-ui.button>
            </div>

            {{-- E17 Pipeline tracker --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E17</span>
                    <h3 class="text-sm font-semibold text-paper">Pipeline tracker</h3>
                </div>
                <div class="space-y-4" id="e17-demo">
                    <x-ui.pipeline status="interview" :dates="['applied' => '12 Okt', 'viewed' => '13 Okt', 'interview' => 'Kamis 16.00 WIB']" />
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach (['applied', 'viewed', 'interview', 'accepted', 'rejected'] as $s)
                        <x-ui.button variant="ghost" size="sm" data-replay="#e17-demo > div" onclick="document.querySelector('#e17-demo > div').dataset.status='{{ $s }}'; window.karirti?.replayPipeline?.(document.querySelector('#e17-demo > div'))">{{ ucfirst($s) }}</x-ui.button>
                    @endforeach
                </div>
            </div>

            {{-- E18 List enter (stagger) --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="rounded-chip bg-signal/20 px-2 py-0.5 font-mono text-xs text-signal">E18</span>
                    <h3 class="text-sm font-semibold text-paper">List enter (stagger kartu)</h3>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3" data-list-enter id="e18-demo">
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="rounded-card border border-ink-line bg-ink-raised p-3 text-sm text-paper">Item {{ $i }}</div>
                    @endfor
                </div>
                <x-ui.button variant="ghost" size="sm" data-replay="#e18-demo">Putar ulang</x-ui.button>
            </div>
        </section>
@endsection

