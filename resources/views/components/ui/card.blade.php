@props([
    'interactive' => false,
])

<div
    {{ $attributes->class([
        'rounded-card border border-ink-line bg-ink-raised p-5 shadow-lift',
        'transition-transform duration-200 hover:-translate-y-0.5' => $interactive,
    ]) }}
    @if ($interactive) data-tilt data-spotlight @endif
>
    {{ $slot }}
</div>
