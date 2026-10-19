@extends('layouts.app', ['ambient' => true])

@section('title', config('app.name').' — Lulusan D3 TI, langsung ke lowongan yang cocok.')
@section('meta-description', 'Portal karir khusus lulusan D3 Teknik Informatika. Unggah CV, tautkan project GitHub, lalu pantau setiap tahap lamaranmu.')

@section('content')
{{-- ============================================================
     HERO (E01, E14 split, E06 magnetic, E02 ambient)
     ============================================================ --}}
<section class="relative overflow-hidden px-4 pb-16 pt-20 sm:pt-28" data-reveal="fade-up">
    <div class="mx-auto grid max-w-6xl items-center gap-12 lg:grid-cols-2">
        <div class="space-y-8">
            {{-- Headline split kata (E14) --}}
            <h1 class="font-display text-hero leading-none tracking-tight text-paper" data-reveal="split">
                Lulusan D3 TI, langsung ke lowongan yang cocok.
            </h1>
            <p class="measure text-lg text-mist">
                Unggah CV, tautkan project GitHub, lalu pantau setiap tahap lamaranmu sampai keputusan akhir.
            </p>
            <div class="flex flex-wrap gap-3">
                <x-ui.button variant="primary" href="{{ route('jobs.index') }}" data-magnetic>
                    Cari lowongan
                </x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('register.company') }}">
                    Pasang lowongan
                </x-ui.button>
            </div>
        </div>

        {{-- SVG Pipeline E01 --}}
        <div class="flex justify-center lg:justify-end">
            <x-hero-pipeline />
        </div>
    </div>
</section>

{{-- ============================================================
     STATISTIK (E15 count-up, reveal stagger)
     ============================================================ --}}
<section class="px-4 py-16" aria-label="Statistik platform">
    <div class="mx-auto max-w-6xl" data-reveal="stagger">
        <div class="grid grid-cols-3 divide-x divide-ink-line rounded-card border border-ink-line bg-ink-raised">
            <div class="p-6 text-center sm:p-10">
                <p class="font-display text-4xl font-bold text-signal sm:text-5xl"
                   data-count-to="{{ $stats['jobs'] }}">{{ number_format($stats['jobs']) }}</p>
                <p class="mt-2 text-sm text-mist">Lowongan aktif</p>
            </div>
            <div class="p-6 text-center sm:p-10">
                <p class="font-display text-4xl font-bold text-sun sm:text-5xl"
                   data-count-to="{{ $stats['companies'] }}">{{ number_format($stats['companies']) }}</p>
                <p class="mt-2 text-sm text-mist">Perusahaan</p>
            </div>
            <div class="p-6 text-center sm:p-10">
                <p class="font-display text-4xl font-bold text-mint sm:text-5xl"
                   data-count-to="{{ $stats['applicants'] }}">{{ number_format($stats['applicants']) }}</p>
                <p class="mt-2 text-sm text-mist">Pelamar terdaftar</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     CARA KERJA (E17 statis, reveal scale-clip)
     ============================================================ --}}
<section class="px-4 py-16" aria-labelledby="cara-kerja">
    <div class="mx-auto max-w-6xl">
        <h2 id="cara-kerja" class="mb-10 font-display text-3xl text-paper" data-reveal="split">
            Tiga langkah menuju pekerjaan impian.
        </h2>
        <div class="grid gap-6 sm:grid-cols-3">
            <div class="rounded-card border border-ink-line bg-ink-raised p-6" data-reveal="scale-clip">
                <span class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-signal/20 font-display text-lg font-bold text-signal">1</span>
                <h3 class="font-display text-lg text-paper">Lengkapi profil dan CV</h3>
                <p class="mt-2 text-sm text-mist">Upload CV PDF, tambahkan link GitHub, dan pilih keahlianmu. Profilmu jadi portofolio digital.</p>
            </div>
            <div class="rounded-card border border-ink-line bg-ink-raised p-6" data-reveal="scale-clip">
                <span class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-sun/20 font-display text-lg font-bold text-sun">2</span>
                <h3 class="font-display text-lg text-paper">Lamar dan pantau</h3>
                <p class="mt-2 text-sm text-mist">Kirim lamaran dengan sekali klik. Pantau status dari "Dilamar" sampai "Dilihat" lewat tracker realtime.</p>
            </div>
            <div class="rounded-card border border-ink-line bg-ink-raised p-6" data-reveal="scale-clip">
                <span class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-mint/20 font-display text-lg font-bold text-mint">3</span>
                <h3 class="font-display text-lg text-paper">Interview dan diterima</h3>
                <p class="mt-2 text-sm text-mist">Terima undangan interview langsung di platform. Konfirmasi jadwal atau ajukan perubahan dengan mudah.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     LOWONGAN TERBARU (E07 tilt, reveal stagger)
     ============================================================ --}}
<section class="px-4 py-16" aria-labelledby="lowongan-terbaru">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8 flex items-end justify-between">
            <h2 id="lowongan-terbaru" class="font-display text-3xl text-paper" data-reveal="split">
                Lowongan terbaru.
            </h2>
            <a href="{{ route('jobs.index') }}" class="link-draw text-sm text-signal">Lihat semua →</a>
        </div>

        @if ($latestJobs->isEmpty())
            <x-ui.empty-state title="Belum ada lowongan." description="Coba lagi nanti." />
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-reveal="stagger">
                @foreach ($latestJobs as $job)
                    <x-job-card
                        :title="$job->title"
                        :company="$job->companyProfile->company_name"
                        :location="$job->location"
                        :jobType="$job->job_type->label()"
                        :workMode="$job->work_mode->label()"
                        :href="route('jobs.show', $job->slug)"
                        :tags="$job->skills->take(3)->pluck('name')->toArray()"
                    />
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============================================================
     MARQUEE SKILL (E16)
     ============================================================ --}}
@if ($skills->isNotEmpty())
<section class="overflow-hidden py-10" aria-label="Keahlian yang dicari" aria-hidden="true">
    <p class="mb-4 text-center text-xs uppercase tracking-widest text-mist">Keahlian yang dicari perusahaan</p>
    <div class="marquee-track">
        <div class="marquee-inner">
            @foreach ($skills->merge($skills) as $skill)
                <span class="mx-2 inline-block rounded-chip border border-ink-line bg-ink-raised px-4 py-1.5 text-sm text-mist">{{ $skill->name }}</span>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================================================
     CTA PERUSAHAAN (reveal fade-up)
     ============================================================ --}}
<section class="px-4 py-20" aria-labelledby="cta-perusahaan" data-reveal="fade-up">
    <div class="mx-auto max-w-2xl rounded-sheet border border-signal/30 bg-ink-raised p-10 text-center">
        <h2 id="cta-perusahaan" class="font-display text-3xl text-paper">Pasang lowongan, temukan talenta D3 TI.</h2>
        <p class="measure mx-auto mt-4 text-mist">Jangkau ratusan lulusan siap kerja. Gratis, tanpa batas lowongan.</p>
        <div class="mt-8">
            <x-ui.button variant="primary" href="{{ route('register.company') }}" data-magnetic>
                Daftar sebagai perusahaan
            </x-ui.button>
        </div>
    </div>
</section>
@endsection
