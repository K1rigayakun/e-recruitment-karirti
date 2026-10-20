@extends('layouts.panel', ['role' => 'applicant'])

@section('title', $application->jobPosting->title)

@section('content')
<div class="mx-auto max-w-3xl space-y-8" data-page-enter>
    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl text-paper">{{ $application->jobPosting->title }}</h1>
            <p class="mt-1 text-mist">{{ $application->jobPosting->companyProfile->company_name }}</p>
        </div>
        @php $tone = $application->status->color(); @endphp
        <x-ui.badge :tone="$tone" class="shrink-0 text-sm">{{ $application->status->label() }}</x-ui.badge>
    </div>

    {{-- Pipeline tracker E17 --}}
    <div class="rounded-card border border-ink-line bg-ink-raised p-5" data-reveal="fade-up">
        @php
            $dates = [];
            foreach ($application->statusHistories as $h) {
                if ($h->to_status) {
                    $dates[$h->to_status->value] = $h->created_at->translatedFormat('d M Y');
                }
            }
        @endphp
        <x-ui.pipeline :status="$application->status->value" :dates="$dates" />
    </div>

    {{-- Riwayat status --}}
    <div data-reveal="fade-up">
        <h2 class="mb-3 font-display text-lg text-paper">Riwayat</h2>
        <div class="space-y-3">
            @foreach ($application->statusHistories as $h)
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-2 w-2 rounded-full shrink-0" style="background:var(--color-{{ $h->to_status?->color() ?? 'mist' }})"></div>
                    <div>
                        <p class="text-sm text-paper">
                            @if ($h->from_status)
                                {{ $h->from_status->label() }} → {{ $h->to_status?->label() }}
                            @else
                                Lamaran dikirim
                            @endif
                        </p>
                        @if ($h->note)
                            <p class="text-xs text-mist mt-0.5">{{ $h->note }}</p>
                        @endif
                        <p class="text-xs text-mist">{{ $h->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- CV & Surat pengantar --}}
    <div class="grid gap-4 sm:grid-cols-2" data-reveal="stagger">
        <div class="rounded-card border border-ink-line bg-ink-raised p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-mist">CV yang dikirim</p>
            <p class="mt-2 text-sm text-paper">{{ $application->cvDocument->original_name }}</p>
            <a href="{{ route('documents.show', $application->cvDocument) }}" target="_blank" rel="noopener noreferrer"
               class="mt-2 inline-block link-draw text-xs text-signal">Lihat CV →</a>
        </div>
        @if ($application->cover_letter)
            <div class="rounded-card border border-ink-line bg-ink-raised p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-mist">Surat pengantar</p>
                <p class="mt-2 text-sm text-mist">{{ $application->cover_letter }}</p>
            </div>
        @endif
    </div>

    {{-- Interview mendatang (placeholder Fase 10) --}}
    @if ($application->interviews->isNotEmpty())
        <div data-reveal="fade-up">
            <h2 class="mb-3 font-display text-lg text-paper">Interview</h2>
            @foreach ($application->interviews->where('status.value', '!=', 'cancelled') as $interview)
                <div class="rounded-card border border-violet/40 bg-violet/5 p-4">
                    <p class="font-medium text-paper">
                        {{ $interview->scheduled_at->translatedFormat('l, d M Y H:i') }} WIB
                    </p>
                    <p class="text-sm text-mist">{{ $interview->mode->label() }} · {{ $interview->duration_minutes }} menit</p>
                    @if ($interview->meeting_url)
                        <a href="{{ $interview->meeting_url }}" target="_blank" rel="noopener noreferrer"
                           class="link-draw text-sm text-signal">Buka tautan meeting →</a>
                    @elseif ($interview->location)
                        <p class="text-sm text-mist">{{ $interview->location }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Aksi --}}
    @if (! $application->status->isFinal())
        <div>
            <x-ui.button variant="danger" data-dialog-open="withdraw-dialog">Batalkan lamaran</x-ui.button>

            <x-ui.modal id="withdraw-dialog" title="Batalkan lamaran ini?">
                Kamu tidak bisa melamar lagi ke lowongan yang sama setelah membatalkan.
                <x-slot:actions>
                    <form method="POST" action="{{ route('applicant.applications.withdraw', $application) }}">
                        @csrf
                        <x-ui.button variant="danger" type="submit">Ya, batalkan</x-ui.button>
                    </form>
                    <x-ui.button variant="ghost" data-dialog-close>Tidak jadi</x-ui.button>
                </x-slot:actions>
            </x-ui.modal>
        </div>
    @endif
</div>
@endsection
