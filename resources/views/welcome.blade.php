@extends('layouts.app')

@section('title', config('app.name'))

@section('content')
    <div class="mx-auto flex min-h-[70vh] max-w-3xl flex-col items-center justify-center px-4 py-16 text-center">
        <h1 class="font-display text-5xl font-extrabold text-signal">{{ config('app.name') }}</h1>
        <p class="mt-3 text-lg text-mist">Fondasi siap</p>
        @if (app()->environment('local'))
            <p class="mt-8">
                <x-ui.button variant="secondary" href="{{ route('styleguide') }}">Buka sistem desain</x-ui.button>
            </p>
        @endif
    </div>
@endsection
