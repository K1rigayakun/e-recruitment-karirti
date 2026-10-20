@extends('layouts.panel', ['role' => 'admin'])

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6" data-page-enter>
    <h1 class="font-display text-2xl text-paper">Dashboard Admin</h1>

    <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-6" data-reveal="stagger">
        <x-ui.stat label="Pelamar" :value="$stats['users_applicant']" />
        <x-ui.stat label="Perusahaan" :value="$stats['users_company']" />
        <x-ui.stat label="Pending verifikasi" :value="$stats['pending']" />
        <x-ui.stat label="Lowongan aktif" :value="$stats['jobs_open']" />
        <x-ui.stat label="Total lamaran" :value="$stats['applications']" />
        <x-ui.stat label="Diterima" :value="$stats['accepted']" />
    </div>

    {{-- Grafik tren E19 (Chart.js dinamis) --}}
    <div class="rounded-card border border-ink-line bg-ink-raised p-5" data-reveal="fade-up">
        <h2 class="mb-4 font-display text-lg text-paper">Tren lamaran 12 bulan</h2>
        <canvas data-chart data-chart-config="{{ json_encode([
            'type' => 'bar',
            'data' => [
                'labels' => array_keys($trend),
                'datasets' => [[
                    'label' => 'Lamaran',
                    'data'  => array_values($trend),
                    'backgroundColor' => 'rgba(79,140,255,0.5)',
                    'borderColor' => 'rgba(79,140,255,1)',
                    'borderWidth' => 1,
                ]],
            ],
            'options' => ['responsive' => true, 'scales' => ['y' => [], 'x' => []]],
        ]) }}" class="max-h-72"></canvas>
    </div>

    <div class="flex flex-wrap gap-3">
        <x-ui.button variant="secondary" href="{{ route('admin.companies.index') }}">Verifikasi perusahaan</x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('admin.reports.index') }}">Lihat laporan</x-ui.button>
    </div>
</div>
@endsection
