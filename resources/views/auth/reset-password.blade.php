@extends('layouts.guest')

@section('title', 'Atur Ulang Kata Sandi')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="font-display text-3xl text-paper">Buat kata sandi baru.</h1>
        </div>

        <form method="POST" action="{{ route('password.store') }}" novalidate data-validate>
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="space-y-4">
                <x-ui.input name="email" type="email" label="Email" :value="old('email', $request->email)"
                    autofocus autocomplete="email" :error="$errors->first('email')" />
                <x-ui.input name="password" type="password" label="Kata sandi baru"
                    hint="Minimal 10 karakter, huruf besar, kecil, dan angka."
                    autocomplete="new-password" :error="$errors->first('password')" />
                <x-ui.input name="password_confirmation" type="password" label="Konfirmasi kata sandi baru"
                    autocomplete="new-password" />
            </div>
            <div class="mt-6">
                <x-ui.button variant="primary" type="submit" class="w-full" loading loading-text="Menyimpan...">
                    Simpan kata sandi baru
                </x-ui.button>
            </div>
        </form>
    </div>
@endsection
