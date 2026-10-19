@extends('layouts.guest')

@section('title', 'Daftar sebagai Pelamar')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="font-display text-3xl text-paper">Buat akun pelamar.</h1>
            <p class="mt-2 text-sm text-mist">Sudah punya akun?
                <a href="{{ route('login') }}" class="link-draw text-signal">Masuk</a>.
                Mendaftar sebagai perusahaan?
                <a href="{{ route('register.company') }}" class="link-draw text-signal">Di sini</a>.
            </p>
        </div>

        <form method="POST" action="{{ route('register.applicant') }}" novalidate data-validate>
            @csrf
            <div class="space-y-4">
                <x-ui.input name="name" label="Nama lengkap" :value="old('name')" autofocus autocomplete="name"
                    :error="$errors->first('name')" />
                <x-ui.input name="email" type="email" label="Email" :value="old('email')" autocomplete="email"
                    :error="$errors->first('email')" />
                <x-ui.input name="password" type="password" label="Kata sandi" autocomplete="new-password"
                    hint="Minimal 10 karakter, huruf besar, kecil, dan angka." :error="$errors->first('password')" />
                <x-ui.input name="password_confirmation" type="password" label="Konfirmasi kata sandi" autocomplete="new-password" />
                <div class="grid grid-cols-2 gap-3">
                    <x-ui.input name="graduation_year" type="number" label="Tahun lulus (opsional)"
                        :value="old('graduation_year')" placeholder="2024" :error="$errors->first('graduation_year')" />
                    <x-ui.input name="nim" label="NIM (opsional)" :value="old('nim')" :error="$errors->first('nim')" />
                </div>
                <label class="flex items-start gap-2 text-sm text-mist">
                    <input type="checkbox" name="privacy" value="1" class="mt-0.5 rounded border-ink-line bg-ink text-signal focus:ring-signal" @checked(old('privacy'))>
                    <span>Saya menyetujui <a href="{{ url('/privasi') }}" class="link-draw text-signal" target="_blank">kebijakan privasi</a>.</span>
                </label>
                @error('privacy') <p class="text-xs text-coral">{{ $message }}</p> @enderror
            </div>
            <div class="mt-6">
                <x-ui.button variant="primary" type="submit" class="w-full" loading loading-text="Mendaftar...">
                    Buat akun
                </x-ui.button>
            </div>
        </form>
    </div>
@endsection
