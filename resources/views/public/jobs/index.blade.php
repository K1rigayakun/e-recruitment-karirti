@extends('layouts.app')

@section('title', 'Lowongan Kerja D3 TI — '.config('app.name'))
@section('meta-description', 'Temukan lowongan kerja untuk lulusan D3 Teknik Informatika. Filter berdasarkan jenis, mode, lokasi, dan keahlian.')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10">
    <div class="mb-8">
        <h1 class="font-display text-3xl text-paper" data-reveal="split">Cari lowongan.</h1>
        <p class="mt-2 text-sm text-mist">{{ $jobs->total() }} lowongan tersedia</p>
    </div>

    {{-- Filter bar atas --}}
    <form method="GET" action="{{ route('jobs.index') }}" id="filter-form" novalidate>
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-48">
                <label class="sr-only" for="q">Cari lowongan</label>
                <input id="q" name="q" type="search" value="{{ request('q') }}"
                    class="w-full rounded-control border border-ink-line bg-ink px-4 py-2 text-sm text-paper placeholder-mist focus:border-signal focus:outline-none focus:ring-1 focus:ring-signal"
                    placeholder="Cari judul, perusahaan, skill…" maxlength="80">
            </div>
            <select name="sort" onchange="this.form.submit()"
                class="rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
                <option value="latest" @selected(request('sort','latest')==='latest')>Terbaru</option>
                <option value="deadline" @selected(request('sort')==='deadline')>Tenggat terdekat</option>
            </select>
            <x-ui.button variant="secondary" type="submit">Cari</x-ui.button>

            {{-- Tombol filter mobile --}}
            <x-ui.button variant="ghost" type="button" class="md:hidden" data-dialog-open="filter-dialog">
                Filter @if(request()->hasAny(['type','mode','location','skill'])) <span class="ml-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-signal text-[10px] text-ink">!</span> @endif
            </x-ui.button>
        </div>

        {{-- Chip filter aktif --}}
        @php
            $activeFilters = array_filter([
                'type' => request('type'),
                'mode' => request('mode'),
                'location' => request('location'),
            ]);
        @endphp
        @if ($activeFilters)
            <div class="mb-4 flex flex-wrap gap-2">
                @foreach ($activeFilters as $key => $val)
                    <a href="{{ request()->fullUrlWithoutQuery([$key]) }}"
                       class="inline-flex items-center gap-1 rounded-chip border border-signal/40 bg-signal/10 px-3 py-1 text-xs text-signal hover:bg-signal/20">
                        {{ $val }} <span aria-hidden="true">×</span>
                        <span class="sr-only">Hapus filter {{ $key }}</span>
                    </a>
                @endforeach
                <a href="{{ route('jobs.index') }}" class="text-xs text-mist hover:text-paper">Hapus semua</a>
            </div>
        @endif

        <div class="grid gap-8 md:grid-cols-[240px_1fr]">
            {{-- Sidebar filter (desktop) --}}
            <aside class="hidden space-y-6 md:block" aria-label="Filter lowongan">
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-mist">Jenis</p>
                    @foreach (\App\Enums\JobType::cases() as $type)
                        <label class="flex cursor-pointer items-center gap-2 py-1 text-sm text-mist hover:text-paper">
                            <input type="radio" name="type" value="{{ $type->value }}" @checked(request('type')===$type->value) onchange="this.form.submit()" class="text-signal focus:ring-signal">
                            {{ $type->label() }}
                        </label>
                    @endforeach
                    @if(request('type'))
                        <a href="{{ request()->fullUrlWithoutQuery(['type']) }}" class="mt-1 block text-xs text-mist hover:text-signal">Hapus ×</a>
                    @endif
                </div>
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-mist">Mode kerja</p>
                    @foreach (\App\Enums\WorkMode::cases() as $mode)
                        <label class="flex cursor-pointer items-center gap-2 py-1 text-sm text-mist hover:text-paper">
                            <input type="radio" name="mode" value="{{ $mode->value }}" @checked(request('mode')===$mode->value) onchange="this.form.submit()" class="text-signal focus:ring-signal">
                            {{ $mode->label() }}
                        </label>
                    @endforeach
                    @if(request('mode'))
                        <a href="{{ request()->fullUrlWithoutQuery(['mode']) }}" class="mt-1 block text-xs text-mist hover:text-signal">Hapus ×</a>
                    @endif
                </div>
            </aside>

            {{-- Daftar lowongan --}}
            <div>
                @if ($jobs->isEmpty())
                    <x-ui.empty-state
                        title="Tidak ada lowongan yang sesuai."
                        description="Coba hapus beberapa filter atau cari dengan kata kunci lain.">
                        <x-slot:action>
                            <x-ui.button variant="secondary" href="{{ route('jobs.index') }}">Lihat semua lowongan</x-ui.button>
                        </x-slot:action>
                    </x-ui.empty-state>
                @else
                    <div class="grid gap-4 sm:grid-cols-2" data-reveal="stagger" data-list-enter>
                        @foreach ($jobs as $job)
                            <x-job-card
                                :title="$job->title"
                                :company="$job->companyProfile->company_name"
                                :location="$job->location"
                                :jobType="$job->job_type->label()"
                                :workMode="$job->work_mode->label()"
                                :href="route('jobs.show', $job->slug)"
                                :tags="$job->skills->take(3)->pluck('name')->toArray()"
                            />
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $jobs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </form>

    {{-- Dialog filter mobile --}}
    <x-ui.modal id="filter-dialog" title="Filter lowongan">
        <form method="GET" action="{{ route('jobs.index') }}" class="space-y-5">
            <div>
                <p class="mb-2 text-sm font-semibold text-paper">Jenis</p>
                @foreach (\App\Enums\JobType::cases() as $type)
                    <label class="flex items-center gap-2 py-1 text-sm text-mist">
                        <input type="radio" name="type" value="{{ $type->value }}" @checked(request('type')===$type->value) class="text-signal">
                        {{ $type->label() }}
                    </label>
                @endforeach
            </div>
            <div>
                <p class="mb-2 text-sm font-semibold text-paper">Mode kerja</p>
                @foreach (\App\Enums\WorkMode::cases() as $mode)
                    <label class="flex items-center gap-2 py-1 text-sm text-mist">
                        <input type="radio" name="mode" value="{{ $mode->value }}" @checked(request('mode')===$mode->value) class="text-signal">
                        {{ $mode->label() }}
                    </label>
                @endforeach
            </div>
            <x-slot:actions>
                <x-ui.button variant="primary" type="submit">Terapkan filter</x-ui.button>
                <a href="{{ route('jobs.index') }}" class="text-sm text-mist hover:text-paper">Reset</a>
            </x-slot:actions>
        </form>
    </x-ui.modal>
</div>
@endsection
