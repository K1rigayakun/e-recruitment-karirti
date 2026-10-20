@props([
    'value' => 0,
    'label' => 'Kelengkapan profil',
])

@php
    $pct = max(0, min(100, (int) $value));
    $r = 18;
    $c = 2 * M_PI * $r;
    $dash = $c * ($pct / 100);
@endphp

<div {{ $attributes->class('flex items-center gap-3') }}>
    <svg viewBox="0 0 44 44" class="h-16 w-16 -rotate-90" aria-hidden="true">
        <circle cx="22" cy="22" r="{{ $r }}" fill="none" class="stroke-ink-line" stroke-width="4"></circle>
        <circle
            cx="22"
            cy="22"
            r="{{ $r }}"
            fill="none"
            class="stroke-signal"
            stroke-width="4"
            stroke-linecap="round"
            stroke-dasharray="{{ $dash }} {{ $c }}"
        ></circle>
    </svg>
    <div>
        <p class="font-display text-2xl text-paper">{{ $pct }}%</p>
        <p class="text-sm text-mist">{{ $label }}</p>
    </div>
</div>
