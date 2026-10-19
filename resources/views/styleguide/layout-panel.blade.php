@extends('layouts.panel', ['role' => $role ?? 'applicant'])

@section('title', 'Panel')
@section('heading', 'Ringkasan')

@section('content')
    <p class="mb-6 text-sm text-mist">Contoh panel. Ganti role:
        <a class="link-draw text-signal" href="{{ route('styleguide.layout.panel', ['role' => 'applicant']) }}">pelamar</a>,
        <a class="link-draw text-signal" href="{{ route('styleguide.layout.panel', ['role' => 'company']) }}">perusahaan</a>,
        <a class="link-draw text-signal" href="{{ route('styleguide.layout.panel', ['role' => 'admin']) }}">admin</a>.
    </p>
    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Lamaran" value="3" />
        <x-ui.stat label="Interview" value="1" />
        <x-ui.stat label="Keputusan" value="0" />
    </div>
    <p class="mt-8"><a class="link-draw text-signal" href="{{ route('styleguide') }}">Kembali ke sistem desain</a></p>
@endsection
