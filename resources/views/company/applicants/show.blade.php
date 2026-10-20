@extends('layouts.panel', ['role' => 'company'])

@section('title', $application->applicantProfile->user->name.' — Detail Pelamar')

@section('content')
<div class="mx-auto max-w-4xl space-y-8" data-page-enter>

    {{-- Breadcrumb --}}
    <p class="text-sm text-mist">
        <a href="{{ route('company.jobs.index') }}" class="link-draw text-signal">Lowongan</a>
        → <a href="{{ route('company.applicants.index', $application->jobPosting) }}" class="link-draw text-signal">{{ $application->jobPosting->title }}</a>
        → {{ $application->applicantProfile->user->name }}
    </p>

    <div class="grid gap-8 lg:grid-cols-[1fr_300px]">
        {{-- Kiri: info pelamar --}}
        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex items-start gap-4" data-reveal="fade-up">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-signal/20 font-display text-xl font-bold text-signal">
                    {{ Str::upper(Str::substr($application->applicantProfile->user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="font-display text-2xl text-paper">{{ $application->applicantProfile->user->name }}</h1>
                    @if ($application->applicantProfile->headline)
                        <p class="text-mist">{{ $application->applicantProfile->headline }}</p>
                    @endif
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if ($application->applicantProfile->alumni_verified_at)
                            <span class="rounded-chip bg-mint/20 px-2 py-0.5 text-xs text-mint">Alumni Terverifikasi ✓</span>
                        @endif
                        @php $tone = $application->status->color(); @endphp
                        <x-ui.badge :tone="$tone">{{ $application->status->label() }}</x-ui.badge>
                    </div>
                </div>
            </div>

            {{-- Info dasar (prinsip data seperlunya, plan.md 9.5) --}}
            <div class="grid gap-3 sm:grid-cols-2" data-reveal="stagger">
                @if ($application->applicantProfile->city)
                    <div class="rounded-card border border-ink-line bg-ink-raised p-3">
                        <p class="text-xs text-mist">Kota</p>
                        <p class="text-sm text-paper">{{ $application->applicantProfile->city }}</p>
                    </div>
                @endif
                @if ($application->applicantProfile->graduation_year)
                    <div class="rounded-card border border-ink-line bg-ink-raised p-3">
                        <p class="text-xs text-mist">Tahun lulus</p>
                        <p class="text-sm text-paper">{{ $application->applicantProfile->graduation_year }}</p>
                    </div>
                @endif
                @if ($application->applicantProfile->nim)
                    <div class="rounded-card border border-ink-line bg-ink-raised p-3">
                        <p class="text-xs text-mist">NIM</p>
                        <p class="text-sm text-paper">{{ $application->applicantProfile->nim }}</p>
                    </div>
                @endif
                @if ($application->applicantProfile->user->email)
                    <div class="rounded-card border border-ink-line bg-ink-raised p-3">
                        <p class="text-xs text-mist">Email</p>
                        <p class="text-sm text-paper">{{ $application->applicantProfile->user->email }}</p>
                    </div>
                @endif
            </div>

            {{-- Skill --}}
            @if ($application->applicantProfile->skills->isNotEmpty())
                <div data-reveal="fade-up">
                    <p class="mb-2 text-sm font-semibold text-mist">Keahlian</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($application->applicantProfile->skills as $skill)
                            <x-ui.badge tone="signal">{{ $skill->name }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- CV --}}
            <div data-reveal="fade-up">
                <p class="mb-2 text-sm font-semibold text-mist">CV yang dikirim</p>
                <div class="flex items-center gap-3 rounded-card border border-ink-line bg-ink-raised p-3">
                    <svg class="h-6 w-6 shrink-0 text-coral" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a2 2 0 012 2v1h3a1 1 0 011 1v14a1 1 0 01-1 1H7a1 1 0 01-1-1V6a1 1 0 011-1h3V4a2 2 0 012-2z"/>
                    </svg>
                    <p class="flex-1 text-sm text-paper">{{ $application->cvDocument->original_name }}</p>
                    <a href="{{ route('documents.show', $application->cvDocument) }}" target="_blank" rel="noopener noreferrer"
                       class="link-draw text-xs text-signal">Lihat →</a>
                </div>
            </div>

            {{-- Portofolio --}}
            @if ($application->applicantProfile->portfolioProjects->isNotEmpty())
                <div data-reveal="stagger">
                    <p class="mb-3 text-sm font-semibold text-mist">Project portofolio</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($application->applicantProfile->portfolioProjects as $project)
                            <x-project-card
                                :title="$project->title"
                                :description="$project->description"
                                :github-url="$project->github_url"
                                :demo-url="$project->demo_url"
                                :tech-stack="$project->tech_stack ?? []"
                            />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Surat pengantar --}}
            @if ($application->cover_letter)
                <div data-reveal="fade-up">
                    <p class="mb-2 text-sm font-semibold text-mist">Surat pengantar</p>
                    <div class="rounded-card border border-ink-line bg-ink-raised p-4 text-sm text-mist">
                        {{ $application->cover_letter }}
                    </div>
                </div>
            @endif

            {{-- Riwayat status --}}
            <div data-reveal="fade-up">
                <p class="mb-3 text-sm font-semibold text-mist">Riwayat</p>
                <div class="space-y-2">
                    @foreach ($application->statusHistories as $h)
                        <div class="flex items-start gap-3 text-sm">
                            <div class="mt-1.5 h-2 w-2 shrink-0 rounded-full" style="background:var(--color-{{ $h->to_status?->color() ?? 'mist' }})"></div>
                            <div>
                                <p class="text-paper">
                                    @if ($h->from_status) {{ $h->from_status->label() }} → @endif
                                    {{ $h->to_status?->label() }}
                                </p>
                                @if ($h->note)
                                    <p class="text-xs text-mist">{{ $h->note }}</p>
                                @endif
                                <p class="text-xs text-mist">{{ $h->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Kanan: aksi --}}
        <aside class="space-y-4">
            {{-- Keputusan --}}
            @if (! $application->status->isFinal())
                <div class="rounded-card border border-ink-line bg-ink-raised p-4 space-y-3" data-reveal="scale-clip">
                    <p class="text-sm font-semibold text-paper">Ambil keputusan</p>

                    @error('decide')
                        <p class="text-xs text-coral">{{ $message }}</p>
                    @enderror

                    <x-ui.button variant="primary" class="w-full" data-dialog-open="accept-dialog">
                        Terima pelamar
                    </x-ui.button>
                    <x-ui.button variant="danger" class="w-full" data-dialog-open="reject-dialog">
                        Tolak pelamar
                    </x-ui.button>
                @else
                    <div class="rounded-card border border-ink-line bg-ink-raised p-4" data-reveal="scale-clip">
                        <p class="text-sm text-mist">Keputusan sudah diambil: <strong class="text-paper">{{ $application->status->label() }}</strong></p>
                @endif
                </div>

            {{-- Catatan privat --}}
            <div class="rounded-card border border-ink-line bg-ink-raised p-4 space-y-3" data-reveal="scale-clip">
                <p class="text-sm font-semibold text-paper">Catatan privat</p>
                <p class="text-xs text-mist">Tidak terlihat oleh pelamar.</p>
                <form method="POST" action="{{ route('company.applicants.note', $application) }}" novalidate>
                    @csrf
                    <x-ui.textarea name="company_note" label="" rows="3"
                        :value="old('company_note', $application->company_note)"
                        placeholder="Tulis catatan internal..." />
                    <div class="mt-2">
                        <x-ui.button variant="secondary" type="submit" class="w-full" size="sm">Simpan catatan</x-ui.button>
                    </div>
                </form>
            </div>

            {{-- Jadwalkan interview --}}
            @if (!$application->status->isFinal())
                <div class="rounded-card border border-ink-line bg-ink-raised p-4 space-y-3" data-reveal="scale-clip">
                    <p class="text-sm font-semibold text-paper">Jadwalkan interview</p>
                    <form method="POST" action="{{ route('company.interviews.store', $application) }}" novalidate data-validate>
                        @csrf
                        @error('schedule') <p class="text-xs text-coral">{{ $message }}</p> @enderror
                        <div class="space-y-3">
                            <input type="datetime-local" name="scheduled_at"
                                min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                                class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none"
                                required>
                            <select name="duration_minutes" class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
                                @foreach ([15, 30, 45, 60, 90] as $d)
                                    <option value="{{ $d }}" @selected($d === 60)>{{ $d }} menit</option>
                                @endforeach
                            </select>
                            <select name="mode" id="interview-mode" onchange="toggleInterviewFields(this.value)"
                                class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
                                <option value="online">Online</option>
                                <option value="onsite">Tatap muka</option>
                            </select>
                            <input type="url" name="meeting_url" id="field-meeting-url" placeholder="https://meet.google.com/..."
                                class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
                            <input type="text" name="location" id="field-location" placeholder="Alamat kantor..." style="display:none"
                                class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
                            <textarea name="instructions" rows="2" placeholder="Instruksi untuk pelamar (opsional)"
                                class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none"></textarea>
                        </div>
                        <x-ui.button variant="secondary" type="submit" class="w-full" size="sm" loading loading-text="Menjadwalkan...">
                            Jadwalkan interview
                        </x-ui.button>
                    </form>
                </div>
            @endif
        </aside>
    </div>
</div>

{{-- Dialog konfirmasi terima --}}
<x-ui.modal id="accept-dialog" title="Terima pelamar ini?">
    <p class="text-sm text-mist">Pelamar akan mendapat notifikasi bahwa lamarannya diterima.</p>
    <form method="POST" action="{{ route('company.applicants.decide', $application) }}" id="accept-form">
        @csrf
        <input type="hidden" name="decision" value="accepted">
        <div class="mt-3">
            <x-ui.textarea name="note" label="Pesan (opsional)" rows="2" placeholder="Selamat! Kami senang menerima kamu..." />
        </div>
    </form>
    <x-slot:actions>
        <x-ui.button variant="primary" type="submit" form="accept-form">Ya, terima</x-ui.button>
        <x-ui.button variant="ghost" data-dialog-close>Batal</x-ui.button>
    </x-slot:actions>
</x-ui.modal>

{{-- Dialog konfirmasi tolak --}}
<x-ui.modal id="reject-dialog" title="Tolak pelamar ini?">
    <p class="text-sm text-mist">Pelamar akan mendapat notifikasi bahwa lamarannya tidak berlanjut.</p>
    <form method="POST" action="{{ route('company.applicants.decide', $application) }}" id="reject-form">
        @csrf
        <input type="hidden" name="decision" value="rejected">
        <div class="mt-3">
            <x-ui.select name="note" label="Alasan penolakan (opsional)"
                :options="[
                    '' => 'Pilih alasan...',
                    'Kualifikasi tidak sesuai' => 'Kualifikasi tidak sesuai',
                    'Posisi sudah diisi' => 'Posisi sudah diisi',
                    'Profil tidak lengkap' => 'Profil tidak lengkap',
                ]" />
        </div>
    </form>
    <x-slot:actions>
        <x-ui.button variant="danger" type="submit" form="reject-form">Ya, tolak</x-ui.button>
        <x-ui.button variant="ghost" data-dialog-close>Batal</x-ui.button>
    </x-slot:actions>
</x-ui.modal>

<script nonce="{{ Vite::cspNonce() }}">
function toggleInterviewFields(mode) {
    document.getElementById('field-meeting-url').style.display = mode === 'online' ? '' : 'none';
    document.getElementById('field-location').style.display    = mode === 'onsite' ? '' : 'none';
}
</script>
@endsection


