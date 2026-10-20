@extends('layouts.app')

@section('title', 'Contoh layout publik')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="font-display text-4xl">Layout publik</h1>
        <p class="mt-3 measure text-mist">Navbar, konten, dan footer. Menu seluler muncul di lebar 375px.</p>
        <p class="mt-6"><a class="link-draw text-signal" href="{{ route('styleguide') }}">Kembali ke sistem desain</a></p>
    </div>
@endsection
