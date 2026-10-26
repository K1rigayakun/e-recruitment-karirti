@extends('layouts.app')

@section('title', ($profile->headline ?? $profile->user->name).' — Portofolio')

@section('head-extra')
{{-- Portofolio tidak diindex agar tidak bocor di mesin pencari --}}
<meta name="robots" content="noindex">
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12" data-page-enter>
    {{-- Header profil - TIDAK tampilkan email, telp, NIM --}}
    <div class="mb-10 space-y-4" data-reveal="fade-up">
        <h1 class="font-display text-4xl text-paper">{{ $profile->user->name }}</h1>
        @if ($profile->headline)
            <p class="text-lg text-mist">{{ $profile->headline }}</p>
        @endif
        @if ($profile->city)
            <p class="text-sm text-mist">{{ $profile->city }}</p>
        @endif
        @if ($profile->about)
            <p class="measure text-mist">{{ $profile->about }}</p>
        @endif

        {{-- Skill chips --}}
        @if ($profile->skills->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($profile->skills as $skill)
                    <x-ui.badge tone="signal">{{ $skill->name }}</x-ui.badge>
                @endforeach
            </div>
        @endif

        @if ($profile->github_url)
            <a href="{{ $profile->github_url }}" target="_blank" rel="noopener noreferrer"
               class="link-draw text-sm text-signal">GitHub →</a>
        @endif

        {{-- Tombol salin tautan --}}
        <button type="button"
            class="inline-flex items-center gap-2 text-sm text-mist hover:text-paper"
            data-copy-current-url>
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M7 3.5A1.5 1.5 0 018.5 2h3.879a1.5 1.5 0 011.06.44l3.122 3.12A1.5 1.5 0 0117 6.622V12.5a1.5 1.5 0 01-1.5 1.5h-1v-3.379a3 3 0 00-.879-2.121L10.5 5.379A3 3 0 008.379 4.5H7v-1z"/>
            </svg>
            Salin tautan portofolio
        </button>
    </div>

    {{-- Project cards --}}
    @if ($profile->portfolioProjects->isNotEmpty())
        <h2 class="mb-6 font-display text-2xl text-paper" data-reveal="split">Project</h2>
        <div class="grid gap-5 sm:grid-cols-2" data-reveal="stagger">
            @foreach ($profile->portfolioProjects as $project)
                <x-project-card
                    :title="$project->title"
                    :description="$project->description"
                    :github-url="$project->github_url"
                    :demo-url="$project->demo_url"
                    :tech-stack="$project->tech_stack ?? []"
                />
            @endforeach
        </div>
    @else
        <x-ui.empty-state title="Belum ada project." description="Pengguna ini belum menambahkan project." />
    @endif
</div>
@endsection
