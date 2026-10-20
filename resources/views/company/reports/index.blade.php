@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Laporan Perusahaan')

@section('content')
<div class="space-y-8" data-page-enter>
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl text-paper">Laporan — {{ $profile->company_name }}</h1>
        <a href="{{ route('company.reports.export') }}"
           class="rounded-control border border-signal/40 bg-signal/10 px-4 py-2 text-sm text-signal hover:bg-signal/20">
            Ekspor CSV
        </a>
    </div>

    {{-- Funnel --}}
    <div class="rounded-card border border-ink-line bg-ink-raised p-5" data-reveal="fade-up">
        <h2 class="mb-4 font-display text-lg text-paper">Funnel status lamaran</h2>
        <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
            @foreach (\App\Enums\ApplicationStatus::cases() as $s)
                <div class="rounded-card border border-ink-line bg-ink p-3 text-center">
                    <p class="font-display text-2xl font-bold" style="color:var(--color-{{ $s->color() }})"
                       data-count-to="{{ $funnel[$s->value] ?? 0 }}">{{ $funnel[$s->value] ?? 0 }}</p>
                    <p class="text-xs text-mist">{{ $s->label() }}</p>
                </div>
            @endforeach
        </div>
    </div>

    @if ($topJobs->isNotEmpty())
        <div class="rounded-card border border-ink-line bg-ink-raised p-5" data-reveal="fade-up">
            <h2 class="mb-4 font-display text-lg text-paper">Lowongan terpopuler</h2>
            <div class="space-y-2">
                @foreach ($topJobs as $i => $job)
                    <div class="flex items-center gap-3">
                        <span class="w-5 text-sm text-mist">{{ $i + 1 }}.</span>
                        <p class="flex-1 text-sm text-paper">{{ $job->title }}</p>
                        <span class="rounded-chip bg-signal/20 px-2 py-0.5 text-xs text-signal">{{ $job->applications_count }} pelamar</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
