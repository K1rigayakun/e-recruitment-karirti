@props([
    'id',
    'title',
])

<dialog id="{{ $id }}" class="ui-dialog" aria-labelledby="{{ $id }}-title">
    <div class="p-6">
        <h2 id="{{ $id }}-title" class="font-display text-xl">{{ $title }}</h2>
        <div class="mt-3 text-sm text-mist">
            {{ $slot }}
        </div>
        <div class="mt-6 flex flex-wrap justify-end gap-2">
            {{ $actions ?? '' }}
            <x-ui.button variant="ghost" data-dialog-close>Tutup</x-ui.button>
        </div>
    </div>
</dialog>
