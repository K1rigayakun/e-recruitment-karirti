@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="font-display text-3xl text-paper">Selamat datang kembali.</h1>
            <p class="mt-2 text-sm text-mist">Belum punya akun?
                <a href="{{ route('register.applicant') }}" class="link-draw text-signal">Daftar sebagai pelamar</a>
                atau
                <a href="{{ route('register.company') }}" class="link-draw text-signal">perusahaan</a>.
            </p>
        </div>

        @if (session('status'))
            <p class="rounded-control border border-mint/40 bg-mint/10 p-3 text-sm text-mint">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate data-validate>
            @csrf

            <div class="space-y-4">
                <x-ui.input name="email" type="email" label="Email" :value="old('email')" autofocus autocomplete="email"
                    :error="$errors->first('email')" />

                <x-ui.input name="password" type="password" label="Kata sandi" autocomplete="current-password"
                    :error="$errors->first('password')">
                    <x-slot:hint>
                        <a href="{{ route('password.request') }}" class="link-draw text-xs text-signal">Lupa kata sandi?</a>
                    </x-slot:hint>
                </x-ui.input>

                <label class="flex items-center gap-2 text-sm text-mist">
                    <input type="checkbox" name="remember" class="rounded border-ink-line bg-ink text-signal focus:ring-signal">
                    Ingat saya
                </label>
            </div>

            <div class="mt-6">
                <x-ui.button variant="primary" type="submit" class="w-full" loading loading-text="Masuk...">
                    Masuk
                </x-ui.button>
            </div>
        </form>
    </div>
@endsection
