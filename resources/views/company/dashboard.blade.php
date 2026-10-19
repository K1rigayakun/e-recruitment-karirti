@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Dashboard Perusahaan')

@section('content')
    <div class="space-y-6" data-page-enter>
        <div>
            <h1 class="font-display text-2xl text-paper">Halo, {{ $user->name }}!</h1>
            <p class="mt-1 text-sm text-mist">Kelola lowongan dan tinjau pelamar dari sini.</p>
        </div>

        {{-- Banner perusahaan pending --}}
        @if ($profile && $profile->verification_status->value === 'pending')
            <div class="rounded-card border border-sun/40 bg-sun/10 p-4 text-sm text-sun">
                Akun perusahaan menunggu verifikasi admin. Kamu bisa melengkapi profil sementara.
            </div>
        @endif

        {{-- Statistik placeholder --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat label="Lowongan aktif" value="0" />
            <x-ui.stat label="Total pelamar" value="0" />
            <x-ui.stat label="Interview minggu ini" value="0" />
        </div>

        <x-ui.empty-state
            title="Belum ada lowongan."
            description="Buat lowongan pertama dan mulai terima lamaran.">
            <x-slot:action>
                <x-ui.button variant="primary">Buat lowongan</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    </div>
@endsection
