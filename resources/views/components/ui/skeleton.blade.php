@props([
    'lines' => 3,
])

<div {{ $attributes->class('space-y-3') }} aria-hidden="true">
    @for ($i = 0; $i < $lines; $i++)
        <div class="skeleton h-4 rounded-control {{ $i === $lines - 1 ? 'w-2/3' : 'w-full' }}"></div>
    @endfor
</div>
