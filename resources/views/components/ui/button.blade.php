@props([
    'variant' => 'primary',
    'loading' => false,
    'loadingText' => 'Menyimpan...',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $isDisabled = $disabled || $loading;
    $variants = [
        'primary' => 'btn-sheen bg-sun text-ink hover:bg-sun/90',
        'secondary' => 'border border-signal text-signal hover:bg-signal/10',
        'ghost' => 'text-paper hover:bg-ink-raised',
        'danger' => 'bg-coral text-ink hover:bg-coral/90',
    ];
    $classes = [
        'btn-press inline-flex items-center justify-center gap-2 rounded-control px-4 py-2.5 text-sm font-semibold',
        $variants[$variant] ?? $variants['primary'],
        $isDisabled ? 'cursor-not-allowed opacity-60' : '',
    ];
@endphp

@if ($href)
    <a
        href="{{ $isDisabled ? '#' : $href }}"
        {{ $attributes->class($classes) }}
        @if ($isDisabled) aria-disabled="true" tabindex="-1" @endif
        @if ($loading) aria-busy="true" data-loading-text="{{ $loadingText }}" @endif
    >
        @if ($loading)
            <span class="inline-block h-4 w-4 animate-spin rounded-chip border-2 border-current border-r-transparent" aria-hidden="true"></span>
            <span>{{ $loadingText }}</span>
        @else
            {{ $slot }}
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->class($classes) }}
        @disabled($isDisabled)
        @if ($loading) aria-busy="true" data-loading-text="{{ $loadingText }}" @endif
    >
        @if ($loading)
            <span class="inline-block h-4 w-4 animate-spin rounded-chip border-2 border-current border-r-transparent" aria-hidden="true"></span>
            <span>{{ $loadingText }}</span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
