@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Dashboard Pelamar')

@section('content')
    <div class="space-y-6" data-page-enter>
        <div>
            <h1 class="font-display text-2xl text-paper">Halo, {{ $user->name }}!</h1>
            <p class="mt-1 text-sm text-mist">Pantau lamaran dan lengkapi profilmu dari sini.</p>
        </div>

        {{-- Banner verifikasi email jika belum --}}
        @unless ($user->hasVerifiedEmail())
            <div class="rounded-card border border-sun/40 bg-sun/10 p-4 text-sm text-sun">
                Email kamu belum diverifikasi. Cek kotak masuk atau
                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                    @csrf
                    <button type="submit" class="link-draw">kirim ulang tautan</button>.
                </form>
            </div>
        @endunless

        {{-- Statistik placeholder --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat label="Lamaran aktif" value="0" />
            <x-ui.stat label="Interview mendatang" value="0" />
            <x-ui.stat label="Profil lengkap" value="0%" />
        </div>

        <x-ui.empty-state
            title="Belum ada lamaran."
            description="Jelajahi lowongan dan kirim lamaran pertamamu.">
            <x-slot:action>
                <x-ui.button variant="primary" href="{{ url('/lowongan') }}">Cari lowongan</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    </div>
@endsection
