@props([
    'label',
    'name',
    'error' => null,
    'hint' => null,
    'disabled' => false,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Pilih',
])

@php
    $id = $attributes->get('id', $name);
    $invalid = (bool) $error;
@endphp

<div class="field">
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @disabled($disabled)
        aria-invalid="{{ $invalid ? 'true' : 'false' }}"
        @if ($error) aria-describedby="{{ $id }}-error" @elseif ($hint) aria-describedby="{{ $id }}-hint" @endif
        {{ $attributes->class('field-input pt-6')->except('id') }}
    >
        <option value="" @selected($selected === null || $selected === '')>{{ $placeholder }}</option>
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $text }}</option>
        @endforeach
        {{ $slot }}
    </select>
    <label class="field-label" for="{{ $id }}">{{ $label }}</label>
    @if ($hint && ! $error)
        <p id="{{ $id }}-hint" class="mt-1 text-sm text-mist">{{ $hint }}</p>
    @endif
    @if ($error)
        <p id="{{ $id }}-error" class="mt-1 text-sm text-coral" data-field-error>{{ $error }}</p>
    @endif
</div>
