@if ($errors->any())
    <div class="rounded-card border border-coral/40 bg-coral/10 p-4 text-sm text-coral">
        @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
    </div>
@endif

<form method="POST" action="{{ $action }}" novalidate data-validate>
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif

    <div class="space-y-5">
        <x-ui.input name="title" label="Judul project" maxlength="120"
            :value="old('title', $project?->title)"
            :error="$errors->first('title')" />

        <x-ui.textarea name="description" label="Deskripsi" maxlength="600" rows="3"
            :value="old('description', $project?->description)"
            hint="Maksimal 600 karakter."
            :error="$errors->first('description')" />

        <x-ui.input name="github_url" label="URL GitHub repository" type="url"
            :value="old('github_url', $project?->github_url)"
            hint="Contoh: https://github.com/username/repo"
            :error="$errors->first('github_url')" />

        <x-ui.input name="demo_url" label="URL Demo (opsional)" type="url"
            :value="old('demo_url', $project?->demo_url)"
            hint="Contoh: https://myproject.vercel.app"
            :error="$errors->first('demo_url')" />

        {{-- Tech stack input (tag sederhana) --}}
        <div>
            <label class="mb-1 block text-sm font-medium text-mist">Tech stack (maks 8 tag)</label>
            <div class="space-y-2">
                @foreach (range(0, 7) as $idx)
                    @php $val = old("tech_stack.{$idx}", $project?->tech_stack[$idx] ?? ''); @endphp
                    <input type="text" name="tech_stack[]" value="{{ $val }}"
                        class="w-full rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper placeholder-mist focus:border-signal focus:outline-none"
                        placeholder="Tag {{ $idx + 1 }}" maxlength="24">
                @endforeach
            </div>
            @error('tech_stack') <p class="mt-1 text-xs text-coral">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <x-ui.button variant="primary" type="submit" loading loading-text="Menyimpan...">Simpan</x-ui.button>
        <x-ui.button variant="ghost" href="{{ route('applicant.projects.index') }}">Batal</x-ui.button>
    </div>
</form>
