@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Profil Perusahaan')

@section('content')
<div class="mx-auto max-w-2xl space-y-6" data-page-enter>
    <h1 class="font-display text-2xl text-paper">Profil perusahaan</h1>

    @if ($errors->any())
        <div class="rounded-card border border-coral/40 bg-coral/10 p-4 text-sm text-coral">
            @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('company.profile.update') }}" enctype="multipart/form-data" novalidate data-validate>
        @csrf @method('PUT')
        <div class="space-y-5">
            <x-ui.input name="company_name" label="Nama perusahaan" required
                :value="old('company_name', $profile->company_name)"
                :error="$errors->first('company_name')" />

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input name="industry" label="Industri"
                    :value="old('industry', $profile->industry)"
                    :error="$errors->first('industry')" />
                <x-ui.select name="size" label="Ukuran perusahaan"
                    :options="['1-10'=>'1–10','11-50'=>'11–50','51-200'=>'51–200','201-500'=>'201–500','500+'=>'500+']"
                    :selected="old('size', $profile->size)" />
            </div>

            <x-ui.textarea name="description" label="Deskripsi perusahaan" maxlength="1500" rows="4"
                :value="old('description', $profile->description)"
                :error="$errors->first('description')" />

            <x-ui.input name="website" label="Website" type="url"
                :value="old('website', $profile->website)"
                hint="Contoh: https://perusahaan.co.id"
                :error="$errors->first('website')" />

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input name="city" label="Kota"
                    :value="old('city', $profile->city)"
                    :error="$errors->first('city')" />
                <x-ui.input name="address" label="Alamat"
                    :value="old('address', $profile->address)"
                    :error="$errors->first('address')" />
            </div>

            {{-- Logo --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-mist">Logo perusahaan (JPG/PNG/WebP, maks 1 MB)</label>
                @if ($profile->logo_path)
                    <img src="{{ Storage::url($profile->logo_path) }}" alt="Logo"
                        class="mb-2 h-16 w-16 rounded-control object-contain border border-ink-line bg-ink-raised p-1">
                @else
                    <div class="mb-2 flex h-16 w-16 items-center justify-center rounded-control border border-ink-line bg-ink-raised font-display text-xl font-bold text-signal">
                        {{ Str::upper(Str::substr($profile->company_name ?? 'P', 0, 1)) }}
                    </div>
                @endif
                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp"
                    class="rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper file:mr-3 file:rounded-control file:border-0 file:bg-signal/20 file:px-3 file:py-1 file:text-xs file:text-signal">
                @error('logo') <p class="mt-1 text-xs text-coral">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <x-ui.button variant="primary" type="submit" loading loading-text="Menyimpan...">Simpan</x-ui.button>
            <x-ui.button variant="ghost" href="{{ route('company.dashboard') }}">Batal</x-ui.button>
        </div>
    </form>
</div>
@endsection
