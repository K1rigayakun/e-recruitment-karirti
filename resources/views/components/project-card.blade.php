@props([
    "title"       => "Sistem Antrian Kampus",
    "description" => "Aplikasi antrian layanan akademik dengan dashboard admin.",
    "githubUrl"   => "https://github.com/example/repo",
    "demoUrl"     => null,
    "techStack"   => ["PHP", "MySQL", "JavaScript"],
])

<x-ui.card interactive {{ $attributes }} data-tilt>
    <h3 class="font-display text-lg text-paper">{{ $title }}</h3>
    <p class="mt-2 text-sm text-mist line-clamp-3">{{ $description }}</p>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach ($techStack as $tech)
            <x-ui.badge tone="mist">{{ $tech }}</x-ui.badge>
        @endforeach
    </div>
    <div class="mt-4 flex gap-3">
        <a href="{{ $githubUrl }}" class="group inline-flex items-center gap-1 text-sm text-signal link-draw"
           rel="noopener noreferrer" target="_blank">
            GitHub
            <svg class="icon-shift h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h9.69L10.22 6.03a.75.75 0 111.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 11-1.06-1.06l3.22-3.22H3.75A.75.75 0 013 10z" clip-rule="evenodd"/>
            </svg>
        </a>
        @if ($demoUrl)
            <a href="{{ $demoUrl }}" class="text-sm text-mist hover:text-paper link-draw"
               rel="noopener noreferrer" target="_blank">Demo →</a>
        @endif
    </div>
</x-ui.card>
