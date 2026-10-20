@extends('layouts.app')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
<div class="flex min-h-[60vh] items-center justify-center px-4 py-20" data-reveal="fade-up">
    <div class="text-center space-y-6 max-w-md">
        <p class="font-display text-8xl font-bold text-signal opacity-30">404</p>
        <h1 class="font-display text-3xl text-paper">Halaman tidak ditemukan.</h1>
        <p class="text-mist">Tautan ini tidak ada atau sudah dipindahkan.</p>
        <div class="flex justify-center gap-3">
            <x-ui.button variant="primary" href="{{ route('home') }}">Ke beranda</x-ui.button>
            <x-ui.button variant="ghost" href="{{ route('jobs.index') }}">Cari lowongan</x-ui.button>
        </div>
    </div>
</div>
@endsection
