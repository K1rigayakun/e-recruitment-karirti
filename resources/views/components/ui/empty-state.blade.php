@props([
    'title' => 'Belum ada data',
    'description' => null,
])

<div {{ $attributes->class('rounded-card border border-dashed border-ink-line bg-ink/30 px-6 py-12 text-center') }}>
    <p class="font-display text-xl text-paper">{{ $title }}</p>
    @if ($description)
        <p class="mx-auto mt-2 max-w-md text-sm text-mist">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
