@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Edit Profil')

@section('content')
<div class="mx-auto max-w-2xl space-y-8" data-page-enter>
    <div>
        <h1 class="font-display text-2xl text-paper">Edit profil</h1>
        <p class="mt-1 text-sm text-mist">Kelengkapan profil: <strong class="text-signal">{{ $progress['percent'] }}%</strong></p>
    </div>

    @if ($errors->any())
        <div class="rounded-card border border-coral/40 bg-coral/10 p-4 text-sm text-coral">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('applicant.profile.update') }}" novalidate data-validate>
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <x-ui.input name="headline" label="Headline (singkat tentang kamu)" maxlength="120"
                :value="old('headline', $profile->headline)"
                hint="Contoh: Junior Web Developer | Laravel & Vue"
                :error="$errors->first('headline')" />

            <x-ui.textarea name="about" label="Tentang saya" maxlength="1000" rows="4"
                :value="old('about', $profile->about)"
                hint="Maksimal 1000 karakter."
                :error="$errors->first('about')" />

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input name="phone" label="Nomor telepon" type="tel"
                    :value="old('phone', $profile->phone)"
                    :error="$errors->first('phone')" />
                <x-ui.input name="city" label="Kota domisili"
                    :value="old('city', $profile->city)"
                    :error="$errors->first('city')" />
                <x-ui.input name="graduation_year" label="Tahun lulus" type="number"
                    :value="old('graduation_year', $profile->graduation_year)"
                    placeholder="{{ date('Y') }}"
                    :error="$errors->first('graduation_year')" />
                <x-ui.input name="nim" label="NIM" maxlength="20"
                    :value="old('nim', $profile->nim)"
                    :error="$errors->first('nim')" />
            </div>

            <x-ui.input name="github_url" label="URL GitHub profil" type="url"
                :value="old('github_url', $profile->github_url)"
                hint="Contoh: https://github.com/username"
                :error="$errors->first('github_url')" />

            <x-ui.input name="linkedin_url" label="URL LinkedIn (opsional)" type="url"
                :value="old('linkedin_url', $profile->linkedin_url)"
                hint="Contoh: https://linkedin.com/in/username"
                :error="$errors->first('linkedin_url')" />

            {{-- Skill chip-select --}}
            <div>
                <p class="mb-2 text-sm font-medium text-mist">Keahlian (maks 15)</p>
                <x-ui.chip-select
                    name="skills"
                    label="Keahlian"
                    :options="$skills->pluck('name', 'id')->toArray()"
                    :selected="$profile->skills->pluck('id')->map(fn($id) => (string) $id)->toArray()"
                />
                @error('skills') <p class="mt-1 text-xs text-coral">{{ $message }}</p> @enderror
            </div>

            {{-- Toggle portofolio publik --}}
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_public" value="0">
                <input type="checkbox" name="is_public" value="1" class="rounded border-ink-line bg-ink text-signal focus:ring-signal"
                    @checked(old('is_public', $profile->is_public))>
                <div>
                    <span class="text-sm text-paper">Tampilkan portofolio ke publik</span>
                    <p class="text-xs text-mist">Halaman <code class="text-signal">/p/{{ $profile->slug }}</code> bisa diakses siapa saja.</p>
                </div>
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <x-ui.button variant="primary" type="submit" loading loading-text="Menyimpan...">
                Simpan perubahan
            </x-ui.button>
            <x-ui.button variant="ghost" href="{{ route('applicant.dashboard') }}">Batal</x-ui.button>
        </div>
    </form>
</div>
@endsection
