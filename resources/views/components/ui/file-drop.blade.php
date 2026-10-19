@props([
    'name' => 'cv',
    'label' => 'Unggah CV (PDF)',
    'error' => null,
    'maxKb' => null,
    'disabled' => false,
])

@php
    $maxKb = $maxKb ?? config('karirti.cv_max_kb', 2048);
    $id = $attributes->get('id', $name);
@endphp

<div
    class="space-y-2"
    data-file-drop
    data-max-kb="{{ $maxKb }}"
>
    <span class="text-sm font-medium text-paper" id="{{ $id }}-label">{{ $label }}</span>
    <label
        for="{{ $id }}"
        class="flex cursor-pointer flex-col items-center justify-center rounded-card border border-dashed border-ink-line bg-ink/40 px-4 py-8 text-center transition-colors hover:border-signal"
        data-file-drop-zone
        @if ($disabled) aria-disabled="true" @endif
    >
        <input
            id="{{ $id }}"
            type="file"
            name="{{ $name }}"
            accept="application/pdf,.pdf"
            class="sr-only"
            @disabled($disabled)
            aria-labelledby="{{ $id }}-label"
            @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        >
        <p class="text-sm text-paper" data-file-drop-idle>Lepaskan PDF di sini atau pilih file</p>
        <p class="mt-1 text-xs text-mist">Maksimal {{ number_format($maxKb / 1024, 0) }} MB, format PDF.</p>
        <p class="hidden text-sm text-paper" data-file-drop-file></p>
    </label>
    <button type="button" class="hidden text-sm text-coral" data-file-drop-remove>Hapus file</button>
    <p class="hidden text-sm text-coral" data-file-drop-error></p>
    @if ($error)
        <p id="{{ $id }}-error" class="text-sm text-coral">{{ $error }}</p>
    @endif
</div>
