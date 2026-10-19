@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <h1 class="font-display text-3xl">Masuk</h1>
    <p class="mt-2 text-sm text-mist">Ini contoh layar tamu. Auth belum aktif.</p>
    <form class="mt-8 space-y-4" method="get" action="{{ route('styleguide.layout.guest') }}" data-loading-submit>
        <x-ui.input name="email" type="email" label="Email" />
        <x-ui.input name="password" type="password" label="Kata sandi" />
        <x-ui.button type="submit" variant="primary" class="w-full">Masuk</x-ui.button>
    </form>
    <p class="mt-6 text-sm"><a class="link-draw text-signal" href="{{ route('styleguide') }}">Kembali ke sistem desain</a></p>
@endsection
