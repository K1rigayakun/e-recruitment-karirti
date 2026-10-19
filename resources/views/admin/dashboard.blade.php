@extends('layouts.panel', ['role' => 'admin'])

@section('title', 'Dashboard Admin')

@section('content')
    <div class="space-y-6" data-page-enter>
        <div>
            <h1 class="font-display text-2xl text-paper">Dashboard Admin</h1>
            <p class="mt-1 text-sm text-mist">Pantau platform dan kelola pengguna dari sini.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat label="Total pengguna" value="0" />
            <x-ui.stat label="Perusahaan pending" value="0" />
            <x-ui.stat label="Lowongan aktif" value="0" />
        </div>

        <x-ui.empty-state
            title="Tidak ada antrian verifikasi."
            description="Semua perusahaan sudah diverifikasi." />
    </div>
@endsection
