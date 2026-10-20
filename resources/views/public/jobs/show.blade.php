@extends('layouts.app')

@section('title', $job->title.' — '.$job->companyProfile->company_name)
@section('meta-description', $job->summary ?? Str::limit(strip_tags($job->description), 155))

@section('head-extra')
{{-- Open Graph --}}
<meta property="og:title" content="{{ $job->title }} — {{ $job->companyProfile->company_name }}">
<meta property="og:description" content="{{ $job->summary ?? Str::limit(strip_tags($job->description), 155) }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('jobs.show', $job->slug) }}">
<link rel="canonical" href="{{ route('jobs.show', $job->slug) }}">
{{-- JSON-LD JobPosting --}}
<script type="application/ld+json" nonce="{{ Vite::cspNonce() }}">
@php
    $jsonLd = [
        "@context"         => "https://schema.org",
        "@type"            => "JobPosting",
        "title"            => $job->title,
        "description"      => strip_tags($job->description),
        "datePosted"       => $job->published_at?->toDateString(),
        "validThrough"     => $job->deadline_at?->toDateString(),
        "employmentType"   => $job->job_type->value,
        "hiringOrganization" => [
            "@type" => "Organization",
            "name"  => $job->companyProfile->company_name,
        ],
        "jobLocation" => [
            "@type"   => "Place",
            "address" => ["@type" => "PostalAddress", "addressLocality" => $job->location, "addressCountry" => "ID"],
        ],
    ];
@endphp{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10">

    {{-- Banner pratinjau --}}
    @if ($isPreviewing)
        <div class="mb-6 rounded-card border border-sun/40 bg-sun/10 px-4 py-3 text-sm text-sun">
            Pratinjau — lowongan ini belum dipublikasikan dan tidak terlihat oleh pelamar.
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-8 space-y-4" data-reveal="fade-up">
        <div class="flex items-start gap-4">
            @if ($job->companyProfile->logo_path)
                <img src="{{ Storage::url($job->companyProfile->logo_path) }}"
                    alt="Logo {{ $job->companyProfile->company_name }}"
                    class="h-14 w-14 rounded-control object-contain bg-ink-raised border border-ink-line p-1">
            @else
                <div class="flex h-14 w-14 items-center justify-center rounded-control bg-ink-raised border border-ink-line font-display text-xl font-bold text-signal">
                    {{ Str::upper(Str::substr($job->companyProfile->company_name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="font-display text-3xl text-paper">{{ $job->title }}</h1>
                <p class="mt-1 text-mist">
                    <a href="#" class="hover:text-signal">{{ $job->companyProfile->company_name }}</a>
                    · {{ $job->location }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <x-ui.badge tone="signal">{{ $job->job_type->label() }}</x-ui.badge>
            <x-ui.badge tone="mist">{{ $job->work_mode->label() }}</x-ui.badge>
            @foreach ($job->skills as $skill)
                <x-ui.badge tone="violet">{{ $skill->name }}</x-ui.badge>
            @endforeach
        </div>

        @if ($job->show_salary && $job->salary_min)
            <p class="text-sm text-mist">
                Gaji: <span class="text-paper">Rp {{ number_format($job->salary_min, 0, ',', '.') }}
                @if ($job->salary_max) – Rp {{ number_format($job->salary_max, 0, ',', '.') }} @endif</span>
            </p>
        @endif
        @if ($job->deadline_at)
            <p class="text-sm text-mist">
                Tenggat: <span class="text-paper">{{ $job->deadline_at->translatedFormat('d M Y') }}</span>
                @if ($job->deadline_at->isPast()) <x-ui.badge tone="coral">Berakhir</x-ui.badge> @endif
            </p>
        @endif
    </div>

    <div class="grid gap-8 lg:grid-cols-[1fr_280px]">
        {{-- Konten utama --}}
        <div class="space-y-8">
            <div data-reveal="fade-up">
                <h2 class="mb-3 font-display text-xl text-paper">Deskripsi</h2>
                <x-safe-markdown :text="$job->description" />
            </div>
            <div data-reveal="fade-up">
                <h2 class="mb-3 font-display text-xl text-paper">Persyaratan</h2>
                <x-safe-markdown :text="$job->requirements" />
            </div>
        </div>

        {{-- Sidebar aksi --}}
        <aside class="space-y-4">
            <div class="rounded-card border border-ink-line bg-ink-raised p-5 space-y-3" data-reveal="scale-clip">
                @auth
                    @php
                        $role       = auth()->user()->role?->value;
                        $profile    = auth()->user()->applicantProfile;
                        $alreadyApplied = $profile ? \App\Models\Application::where('job_posting_id', $job->id)
                            ->where('applicant_profile_id', $profile->id)->exists() : false;
                    @endphp
                    @if ($role === 'applicant')
                        @if ($alreadyApplied)
                            <p class="text-sm text-mint text-center">✓ Kamu sudah melamar lowongan ini.</p>
                            <x-ui.button variant="secondary" class="w-full" href="{{ route('applicant.applications.index') }}">Lihat lamaran</x-ui.button>
                        @elseif ($job->status->value !== 'open')
                            <x-ui.button variant="primary" class="w-full" disabled>Lowongan ditutup</x-ui.button>
                        @elseif (!$profile?->current_cv_document_id)
                            <x-ui.button variant="secondary" class="w-full" href="{{ route('applicant.cv.index') }}">Upload CV dulu</x-ui.button>
                            <p class="text-xs text-mist text-center">Kamu butuh CV aktif untuk melamar.</p>
                        @else
                            <x-ui.button variant="primary" class="w-full" data-magnetic data-dialog-open="apply-dialog">
                                Lamar sekarang
                            </x-ui.button>
                        @endif
                    @else
                        <p class="text-sm text-mist">Login sebagai {{ auth()->user()->role?->label() }}.</p>
                    @endif
                @else
                    <x-ui.button variant="primary" class="w-full" href="{{ route('login') }}">
                        Masuk untuk melamar
                    </x-ui.button>
                    <p class="text-xs text-mist text-center">Belum punya akun? <a href="{{ route('register.applicant') }}" class="link-draw text-signal">Daftar</a></p>
                @endauth

                <button type="button" class="w-full text-sm text-mist hover:text-paper flex items-center justify-center gap-2"
                    onclick="navigator.clipboard.writeText('{{ route('jobs.show', $job->slug) }}').then(()=>window.karirti?.showToast('Tautan disalin!','mint'))">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M7 3.5A1.5 1.5 0 018.5 2h3.879a1.5 1.5 0 011.06.44l3.122 3.12A1.5 1.5 0 0117 6.622V12.5a1.5 1.5 0 01-1.5 1.5h-1v-3.379a3 3 0 00-.879-2.121L10.5 5.379A3 3 0 008.379 4.5H7v-1zm-2 0v1H4.5A1.5 1.5 0 003 6v10a1.5 1.5 0 001.5 1.5h7A1.5 1.5 0 0013 16v-.5h1.5A1.5 1.5 0 0016 14V6.622a1.5 1.5 0 00-.44-1.06L12.44 2.44A1.5 1.5 0 0011.378 2H8.5A1.5 1.5 0 007 3.5z"/>
                    </svg>
                    Salin tautan
                </button>
            </div>

            {{-- Info perusahaan --}}
            <div class="rounded-card border border-ink-line bg-ink-raised p-5 space-y-2" data-reveal="scale-clip">
                <p class="font-semibold text-paper">{{ $job->companyProfile->company_name }}</p>
                @if ($job->companyProfile->industry)
                    <p class="text-xs text-mist">{{ $job->companyProfile->industry }}</p>
                @endif
                @if ($job->companyProfile->city)
                    <p class="text-xs text-mist">{{ $job->companyProfile->city }}</p>
                @endif
                @if ($job->companyProfile->website)
                    <a href="{{ $job->companyProfile->website }}" target="_blank" rel="noopener noreferrer"
                       class="link-draw text-xs text-signal">Website →</a>
                @endif
            </div>
        </aside>
    </div>

    {{-- Lowongan serupa (reveal stagger) --}}
    @if ($similar->isNotEmpty())
        <div class="mt-12">
            <h2 class="mb-4 font-display text-2xl text-paper">Lowongan serupa</h2>
            <div class="grid gap-4 sm:grid-cols-3" data-reveal="stagger">
                @foreach ($similar as $s)
                    <x-job-card
                        :title="$s->title"
                        :company="$s->companyProfile->company_name"
                        :location="$s->location"
                        :jobType="$s->job_type->label()"
                        :workMode="$s->work_mode->label()"
                        :href="route('jobs.show', $s->slug)"
                        :tags="$s->skills->take(3)->pluck('name')->toArray()"
                    />
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Dialog Lamar --}}
@auth
    @if (auth()->user()->role?->value === 'applicant')
        @php
            $profile  = auth()->user()->applicantProfile;
            $cvDocs   = auth()->user()->documents()->where('type', 'cv')->latest()->get();
            $activeCv = $profile?->current_cv_document_id;
        @endphp
        <x-ui.modal id="apply-dialog" title="Lamar {{ $job->title }}">
            <form method="POST" action="{{ route('applicant.applications.store', $job->slug) }}"
                  id="apply-form" novalidate data-validate>
                @csrf

                {{-- Pilih CV --}}
                @if ($cvDocs->isNotEmpty())
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-medium text-mist">CV yang digunakan</label>
                        <select name="cv_document_id"
                            class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
                            @foreach ($cvDocs as $doc)
                                <option value="{{ $doc->id }}" @selected($doc->id === $activeCv)>
                                    {{ $doc->original_name }}
                                    @if ($doc->id === $activeCv) (Aktif) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Surat pengantar --}}
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-mist" for="cover_letter">
                        Surat pengantar <span class="text-xs">(opsional, maks 1500 karakter)</span>
                    </label>
                    <textarea id="cover_letter" name="cover_letter" rows="4" maxlength="1500"
                        class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper placeholder-mist focus:border-signal focus:outline-none"
                        placeholder="Ceritakan singkat mengapa kamu cocok untuk posisi ini..."></textarea>
                    <p class="mt-1 text-right text-xs text-mist">
                        <span id="cover-count">0</span>/1500
                    </p>
                </div>

                {{-- Ringkasan profil --}}
                <div class="rounded-control border border-ink-line bg-ink p-3 text-xs text-mist">
                    <p><strong class="text-paper">{{ auth()->user()->name }}</strong></p>
                    @if ($profile?->headline) <p>{{ $profile->headline }}</p> @endif
                    @if ($profile?->graduation_year) <p>Lulus {{ $profile->graduation_year }}</p> @endif
                </div>

                @error('apply')
                    <p class="mt-2 text-xs text-coral">{{ $message }}</p>
                @enderror
            </form>

            <x-slot:actions>
                <x-ui.button variant="primary" type="submit" form="apply-form" loading loading-text="Mengirim...">
                    Kirim lamaran
                </x-ui.button>
                <x-ui.button variant="ghost" data-dialog-close>Batal</x-ui.button>
            </x-slot:actions>
        </x-ui.modal>

        <script nonce="{{ Vite::cspNonce() }}">
            document.getElementById('cover_letter')?.addEventListener('input', function() {
                document.getElementById('cover-count').textContent = this.value.length;
            });
        </script>
    @endif
@endauth
@endsection



