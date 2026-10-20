@extends('layouts.panel', ['role' => 'admin'])

@section('title', 'Laporan Admin')

@section('content')
<div class="space-y-8" data-page-enter>
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl text-paper">Laporan platform</h1>
        <a href="{{ route('admin.reports.export') }}" class="rounded-control border border-signal/40 bg-signal/10 px-4 py-2 text-sm text-signal hover:bg-signal/20">
            Ekspor CSV
        </a>
    </div>

    {{-- Funnel status --}}
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

    {{-- Chart tren (E19) --}}
    <div class="rounded-card border border-ink-line bg-ink-raised p-5" data-reveal="fade-up">
        <h2 class="mb-4 font-display text-lg text-paper">Tren lamaran per bulan</h2>
        <canvas data-chart data-chart-config="{{ json_encode([
            'type' => 'line',
            'data' => [
                'labels' => array_keys($trend),
                'datasets' => [[
                    'label' => 'Lamaran',
                    'data'  => array_values($trend),
                    'borderColor' => 'rgba(79,140,255,1)',
                    'backgroundColor' => 'rgba(79,140,255,0.1)',
                    'tension' => 0.3, 'fill' => true,
                ]],
            ],
            'options' => ['responsive' => true, 'scales' => ['y' => [], 'x' => []]],
        ]) }}" class="max-h-64"></canvas>
    </div>

    {{-- Diterima per tahun lulus --}}
    @if (!empty($byYear))
        <div class="rounded-card border border-ink-line bg-ink-raised p-5" data-reveal="fade-up">
            <h2 class="mb-4 font-display text-lg text-paper">Diterima per tahun lulus</h2>
            <div class="flex flex-wrap gap-3">
                @foreach ($byYear as $year => $count)
                    <div class="rounded-card border border-ink-line bg-ink p-3 text-center">
                        <p class="font-display text-xl font-bold text-mint">{{ $count }}</p>
                        <p class="text-xs text-mist">{{ $year }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Top lowongan --}}
    @if ($topJobs->isNotEmpty())
        <div class="rounded-card border border-ink-line bg-ink-raised p-5" data-reveal="fade-up">
            <h2 class="mb-4 font-display text-lg text-paper">Lowongan terpopuler</h2>
            <div class="space-y-2">
                @foreach ($topJobs as $i => $job)
                    <div class="flex items-center gap-3">
                        <span class="w-5 text-sm text-mist">{{ $i + 1 }}.</span>
                        <p class="flex-1 text-sm text-paper">{{ $job->title }} — {{ $job->companyProfile->company_name ?? '' }}</p>
                        <span class="rounded-chip bg-signal/20 px-2 py-0.5 text-xs text-signal">{{ $job->applications_count }} pelamar</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
