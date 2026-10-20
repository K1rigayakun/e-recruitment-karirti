@extends('layouts.guest')

@section('title', 'Verifikasi Email')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="font-display text-3xl text-paper">Verifikasi emailmu.</h1>
            <p class="mt-2 text-sm text-mist">
                Kami mengirim tautan verifikasi ke emailmu. Klik tautan itu untuk melanjutkan.
                Di lokal, cek <code class="rounded bg-ink-raised px-1 text-xs">storage/logs/laravel.log</code>.
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <p class="rounded-control border border-mint/40 bg-mint/10 p-3 text-sm text-mint">
                Tautan verifikasi baru telah dikirim ke emailmu.
            </p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button variant="secondary" type="submit" class="w-full">Kirim ulang tautan</x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button variant="ghost" type="submit" class="w-full">Keluar</x-ui.button>
        </form>
    </div>
@endsection
