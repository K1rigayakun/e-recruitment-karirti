@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Portofolio Project')

@section('content')
<div class="mx-auto max-w-3xl space-y-6" data-page-enter>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl text-paper">Project portofolio</h1>
            <p class="mt-1 text-sm text-mist">{{ $projects->count() }} / {{ config('karirti.max_projects', 6) }} project</p>
        </div>
        @if ($projects->count() < config('karirti.max_projects', 6))
            <x-ui.button variant="primary" href="{{ route('applicant.projects.create') }}">+ Tambah project</x-ui.button>
        @endif
    </div>

    @error('limit')
        <div class="rounded-card border border-coral/40 bg-coral/10 p-3 text-sm text-coral">{{ $message }}</div>
    @enderror

    @if ($projects->isEmpty())
        <x-ui.empty-state title="Belum ada project." description="Tambahkan project GitHub pertamamu untuk memperkuat profil.">
            <x-slot:action>
                <x-ui.button variant="primary" href="{{ route('applicant.projects.create') }}">Tambah project</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="space-y-4" data-reveal="stagger">
            @foreach ($projects as $i => $project)
                <div class="rounded-card border border-ink-line bg-ink-raised p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-display text-lg text-paper truncate">{{ $project->title }}</h3>
                            <p class="mt-1 text-sm text-mist line-clamp-2">{{ $project->description }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($project->tech_stack ?? [] as $tag)
                                    <x-ui.badge tone="violet">{{ $tag }}</x-ui.badge>
                                @endforeach
                            </div>
                            <div class="mt-3 flex gap-3">
                                <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer"
                                   class="link-draw text-xs text-signal">GitHub →</a>
                                @if ($project->demo_url)
                                    <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer"
                                       class="link-draw text-xs text-mist">Demo →</a>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 shrink-0">
                            {{-- Tombol urutan --}}
                            @if ($i > 0)
                                <form method="POST" action="{{ route('applicant.projects.move-up', $project) }}">
                                    @csrf
                                    <button type="submit" class="rounded-control border border-ink-line px-2 py-1 text-xs text-mist hover:text-paper" title="Naik">↑</button>
                                </form>
                            @endif
                            @if ($i < $projects->count() - 1)
                                <form method="POST" action="{{ route('applicant.projects.move-down', $project) }}">
                                    @csrf
                                    <button type="submit" class="rounded-control border border-ink-line px-2 py-1 text-xs text-mist hover:text-paper" title="Turun">↓</button>
                                </form>
                            @endif
                        </div>

                        <div class="flex gap-2 shrink-0">
                            <x-ui.button variant="ghost" href="{{ route('applicant.projects.edit', $project) }}" size="sm">Edit</x-ui.button>
                            <form method="POST" action="{{ route('applicant.projects.destroy', $project) }}"
                                onsubmit="return confirm('Hapus project ini?')">
                                @csrf @method('DELETE')
                                <x-ui.button variant="danger" type="submit" size="sm">Hapus</x-ui.button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
