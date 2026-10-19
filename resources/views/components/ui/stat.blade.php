@props([
    'label',
    'value',
    'hint' => null,
])

<x-ui.card {{ $attributes }}>
    <p class="text-sm text-mist">{{ $label }}</p>
    <p class="mt-2 font-display text-3xl text-paper" @if (is_numeric($value)) data-count-to="{{ $value }}" @endif>{{ is_numeric($value) ? number_format((int) $value, 0, ',', '.') : $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-mist">{{ $hint }}</p>
    @endif
</x-ui.card>
