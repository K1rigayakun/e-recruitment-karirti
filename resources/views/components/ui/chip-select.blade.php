@props([
    'label' => 'Keahlian',
    'name' => 'skills',
    'options' => [],
    'selected' => [],
    'error' => null,
])

<fieldset {{ $attributes->class('space-y-2') }}>
    <legend class="text-sm font-medium text-paper">{{ $label }}</legend>
    <div class="flex flex-wrap gap-2">
        @foreach ($options as $value => $text)
            <label class="inline-flex cursor-pointer items-center">
                <input
                    type="checkbox"
                    name="{{ $name }}[]"
                    value="{{ $value }}"
                    class="peer sr-only"
                    @checked(in_array($value, $selected, false))
                >
                <span class="rounded-chip border border-ink-line px-3 py-1.5 text-sm text-mist transition-colors peer-checked:border-signal peer-checked:bg-signal/15 peer-checked:text-signal peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-signal">
                    {{ $text }}
                </span>
            </label>
        @endforeach
    </div>
    @if ($error)
        <p class="text-sm text-coral">{{ $error }}</p>
    @endif
</fieldset>
