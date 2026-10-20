@props([
    'align' => 'left',
    'label' => 'Menu',
    'id' => null,
])

@php
    $panelId = $id ?? 'dropdown-'.uniqid();
@endphp

<div class="relative inline-block" data-dropdown>
    <button
        type="button"
        class="inline-flex items-center gap-2 rounded-control border border-ink-line px-3 py-2 text-sm text-paper hover:bg-ink-raised"
        aria-haspopup="menu"
        aria-expanded="false"
        aria-controls="{{ $panelId }}"
        data-dropdown-trigger
    >
        {{ $trigger ?? $label }}
        <svg class="h-4 w-4 text-mist" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
        </svg>
    </button>
    <div
        id="{{ $panelId }}"
        role="menu"
        hidden
        class="{{ $align === 'right' ? 'right-0' : 'left-0' }} absolute z-30 mt-2 min-w-44 overflow-hidden rounded-card border border-ink-line bg-ink-raised py-1 shadow-lift"
        data-dropdown-panel
    >
        {{ $slot }}
    </div>
</div>
