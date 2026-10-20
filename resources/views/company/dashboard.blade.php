@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Dashboard Perusahaan')

@section('content')
<div class="space-y-6" data-page-enter>
    <div>
        <h1 class="font-display text-2xl text-paper">{{ $profile?->company_name ?? $user->name }}</h1>
        <p class="mt-1 text-sm text-mist">Kelola lowongan dan pantau pelamar dari sini.</p>
    </div>

    @if ($profile && $profile->verification_status->value === 'pending')
        <div class="rounded-card border border-sun/40 bg-sun/10 p-4 text-sm text-sun">
            Akun perusahaan menunggu verifikasi admin. Kamu bisa melengkapi profil sementara.
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Lowongan aktif" :value="$stats['jobs_open'] ?? 0" />
        <x-ui.stat label="Total pelamar" :value="$stats['applicants'] ?? 0" />
        <x-ui.stat label="Menunggu ditinjau" :value="$stats['pending'] ?? 0" />
    </div>

    <div class="flex flex-wrap gap-3">
        <x-ui.button variant="primary" href="{{ route('company.jobs.create') }}">+ Buat lowongan</x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('company.profile.edit') }}">Edit profil</x-ui.button>
    </div>
</div>
@endsection
