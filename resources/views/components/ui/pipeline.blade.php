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
@endphp

<ol {{ $attributes->class('grid grid-cols-2 gap-3 sm:grid-cols-4') }} data-pipeline data-status="{{ $status }}">
    @foreach ($steps as $key => $label)
        @php
            $index = array_search($key, array_keys($steps), true);
            $done = $index < $currentIndex;
            $active = $index === $currentIndex;
            $dot = 'bg-ink-line';
            if ($key === 'decision' && $status === 'accepted') {
                $dot = 'bg-mint';
            } elseif ($key === 'decision' && $status === 'rejected') {
                $dot = 'bg-coral';
            } elseif ($active) {
                $dot = 'bg-signal';
            } elseif ($done) {
                $dot = 'bg-paper';
            }
        @endphp
        <li class="flex flex-col gap-1">
            <span class="flex h-3 w-3 rounded-chip {{ $dot }}"></span>
            <span class="text-sm font-medium text-paper">{{ $label }}</span>
            @if ($active || $done)
                <span class="text-xs text-mist">{{ $dates[$key] ?? '' }}</span>
            @endif
        </li>
    @endforeach
</ol>
