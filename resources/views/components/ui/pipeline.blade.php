@props([
    'status' => 'applied',
    'dates' => [],
])

@php
    $steps = [
        'applied' => 'Dilamar',
        'viewed' => 'Dilihat',
        'interview' => 'Interview',
        'decision' => 'Keputusan',
    ];
    $currentIndex = match ($status) {
        'viewed' => 1,
        'interview' => 2,
        'accepted', 'rejected' => 3,
        default => 0,
    };
    $xs = [28, 142, 256, 370];
@endphp

<div {{ $attributes->class('space-y-3') }} data-pipeline data-status="{{ $status }}">
    <svg viewBox="0 0 400 56" class="h-auto w-full" aria-hidden="true">
        <path d="M28 28 H370" class="stroke-ink-line" stroke-width="3" fill="none"></path>
        <path data-pipeline-line d="M28 28 H370" class="stroke-signal" stroke-width="3" fill="none"></path>
        @foreach ($steps as $key => $label)
            @php
                $index = array_search($key, array_keys($steps), true);
                $x = $xs[$index];
                $fill = 'fill-ink-line';
                if ($key === 'decision' && $status === 'accepted') {
                    $fill = 'fill-mint';
                } elseif ($key === 'decision' && $status === 'rejected') {
                    $fill = 'fill-coral';
                } elseif ($index <= $currentIndex) {
                    $fill = 'fill-signal';
                }
            @endphp
            <circle data-pipeline-node cx="{{ $x }}" cy="28" r="8" class="{{ $fill }}"></circle>
        @endforeach
    </svg>
    <ol class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach ($steps as $key => $label)
            @php
                $index = array_search($key, array_keys($steps), true);
                $done = $index < $currentIndex;
                $active = $index === $currentIndex;
            @endphp
            <li class="flex flex-col">
                <span class="text-sm font-medium text-paper">{{ $label }}</span>
                @if ($active || $done)
                    <span class="text-xs text-mist">{{ $dates[$key] ?? '' }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</div>
