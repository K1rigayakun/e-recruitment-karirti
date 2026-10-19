@extends('layouts.guest')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="font-display text-3xl text-paper">Lupa kata sandi?</h1>
            <p class="mt-2 text-sm text-mist">
                Masukkan emailmu dan kami akan mengirim tautan untuk mengatur ulang kata sandi.
            </p>
        </div>

        @if (session('status'))
            {{-- Pesan netral — tidak membocorkan apakah email terdaftar --}}
            <p class="rounded-control border border-mint/40 bg-mint/10 p-3 text-sm text-mint">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('password.email') }}" novalidate data-validate>
            @csrf
            <div class="space-y-4">
                <x-ui.input name="email" type="email" label="Email" :value="old('email')" autofocus
                    :error="$errors->first('email')" />
            </div>
            <div class="mt-6">
                <x-ui.button variant="primary" type="submit" class="w-full" loading loading-text="Mengirim...">
                    Kirim tautan reset
                </x-ui.button>
            </div>
        </form>

        <p class="text-center text-sm">
            <a href="{{ route('login') }}" class="link-draw text-signal">Kembali ke halaman masuk</a>
        </p>
    </div>
@endsection
