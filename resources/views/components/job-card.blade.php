@props([
    'title' => 'Frontend Developer Magang',
    'company' => 'PT Contoh Digital',
    'location' => 'Bandung',
    'jobType' => 'Magang',
    'workMode' => 'Hybrid',
    'href' => '#',
    'tags' => ['Laravel', 'Tailwind'],
])

<a href="{{ $href }}" {{ $attributes->class('group block rounded-card border border-ink-line bg-ink-raised p-5 shadow-lift') }} data-tilt data-spotlight>
    <div class="flex items-start justify-between gap-3">
        <div>
            <h3 class="font-display text-lg text-paper">{{ $title }}</h3>
            <p class="mt-1 text-sm text-mist">{{ $company }} · {{ $location }}</p>
        </div>
        <svg class="icon-shift h-5 w-5 text-signal" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h9.69L10.22 6.03a.75.75 0 111.06-1.06l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 11-1.06-1.06l3.22-3.22H3.75A.75.75 0 013 10z" clip-rule="evenodd"/>
        </svg>
    </div>
    <div class="mt-4 flex flex-wrap gap-2">
        <x-ui.badge tone="signal">{{ $jobType }}</x-ui.badge>
        <x-ui.badge tone="mist">{{ $workMode }}</x-ui.badge>
        @foreach ($tags as $tag)
            <x-ui.badge tone="violet">{{ $tag }}</x-ui.badge>
        @endforeach
    </div>
</a>
