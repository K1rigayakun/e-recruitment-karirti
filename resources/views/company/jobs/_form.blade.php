@if ($errors->any())
    <div class="rounded-card border border-coral/40 bg-coral/10 p-4 text-sm text-coral">
        @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
    </div>
@endif

<form method="POST" action="{{ $action }}" novalidate data-validate>
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif

    <div class="space-y-5">
        <x-ui.input name="title" label="Judul lowongan" maxlength="150" required
            :value="old('title', $job?->title)" :error="$errors->first('title')" />

        <x-ui.input name="summary" label="Ringkasan singkat (opsional)" maxlength="220"
            :value="old('summary', $job?->summary)" hint="Maksimal 220 karakter. Tampil di kartu lowongan." />

        <div>
            <label class="mb-1 block text-sm font-medium text-mist">Deskripsi <span class="text-xs">(Markdown didukung)</span></label>
            <x-ui.textarea name="description" label="" maxlength="5000" rows="8"
                :value="old('description', $job?->description)" :error="$errors->first('description')" />
            <p class="mt-1 text-xs text-mist">Gunakan **tebal**, ## judul, dan - daftar.</p>
        </div>

        <x-ui.textarea name="requirements" label="Persyaratan" maxlength="3000" rows="5"
            :value="old('requirements', $job?->requirements)" :error="$errors->first('requirements')" />

        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.input name="location" label="Lokasi" required
                :value="old('location', $job?->location)" :error="$errors->first('location')" />
            <x-ui.select name="job_type" label="Jenis pekerjaan" required
                :options="collect(\App\Enums\JobType::cases())->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                :selected="old('job_type', $job?->job_type?->value)" />
            <x-ui.select name="work_mode" label="Mode kerja" required
                :options="collect(\App\Enums\WorkMode::cases())->mapWithKeys(fn($m) => [$m->value => $m->label()])->toArray()"
                :selected="old('work_mode', $job?->work_mode?->value)" />
        </div>

        {{-- Gaji --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <x-ui.input name="salary_min" label="Gaji minimum (Rp)" type="number" min="0"
                :value="old('salary_min', $job?->salary_min)"
                :error="$errors->first('salary_min')" />
            <x-ui.input name="salary_max" label="Gaji maksimum (Rp)" type="number" min="0"
                :value="old('salary_max', $job?->salary_max)"
                :error="$errors->first('salary_max')" />
        </div>
        <label class="flex items-center gap-2 text-sm text-mist">
            <input type="hidden" name="show_salary" value="0">
            <input type="checkbox" name="show_salary" value="1" class="rounded border-ink-line bg-ink text-signal focus:ring-signal"
                @checked(old('show_salary', $job?->show_salary))>
            Tampilkan gaji kepada pelamar
        </label>

        <x-ui.input name="deadline_at" label="Tenggat lamaran (opsional)" type="date"
            :value="old('deadline_at', $job?->deadline_at?->format('Y-m-d'))"
            :error="$errors->first('deadline_at')" />

        {{-- Skill --}}
        <div>
            <p class="mb-2 text-sm font-medium text-mist">Keahlian yang dibutuhkan (maks 12)</p>
            <x-ui.chip-select name="skills" label="Skill"
                :options="$skills->pluck('name', 'id')->toArray()"
                :selected="collect(old('skills', $job?->skills?->pluck('id') ?? []))->map(fn($id) => (string) $id)->toArray()" />
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <x-ui.button variant="primary" type="submit" loading loading-text="Menyimpan...">Simpan draft</x-ui.button>
        <x-ui.button variant="ghost" href="{{ route('company.jobs.index') }}">Batal</x-ui.button>
    </div>
</form>
