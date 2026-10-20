@extends('layouts.guest')

@section('title', 'Daftar sebagai Perusahaan')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="font-display text-3xl text-paper">Pasang lowongan, temukan talenta.</h1>
            <p class="mt-2 text-sm text-mist">Sudah punya akun?
                <a href="{{ route('login') }}" class="link-draw text-signal">Masuk</a>.
                Mendaftar sebagai pelamar?
                <a href="{{ route('register.applicant') }}" class="link-draw text-signal">Di sini</a>.
            </p>
        </div>

        <form method="POST" action="{{ route('register.company') }}" novalidate data-validate>
            @csrf
            <div class="space-y-4">
                <x-ui.input name="name" label="Nama PIC (kontak)" :value="old('name')" autofocus
                    :error="$errors->first('name')" />
                <x-ui.input name="company_name" label="Nama perusahaan" :value="old('company_name')"
                    :error="$errors->first('company_name')" />
                <x-ui.input name="email" type="email" label="Email kerja" :value="old('email')"
                    :error="$errors->first('email')" />
                <x-ui.input name="password" type="password" label="Kata sandi"
                    hint="Minimal 10 karakter, huruf besar, kecil, dan angka." :error="$errors->first('password')" />
                <x-ui.input name="password_confirmation" type="password" label="Konfirmasi kata sandi" />
                <label class="flex items-start gap-2 text-sm text-mist">
                    <input type="checkbox" name="privacy" value="1" class="mt-0.5 rounded border-ink-line bg-ink text-signal focus:ring-signal" @checked(old('privacy'))>
                    <span>Saya menyetujui <a href="{{ url('/privasi') }}" class="link-draw text-signal" target="_blank">kebijakan privasi</a>.</span>
                </label>
                @error('privacy') <p class="text-xs text-coral">{{ $message }}</p> @enderror
            </div>
            <div class="mt-6">
                <x-ui.button variant="primary" type="submit" class="w-full" loading loading-text="Mendaftar...">
                    Buat akun perusahaan
                </x-ui.button>
            </div>
        </form>
    </div>
@endsection
