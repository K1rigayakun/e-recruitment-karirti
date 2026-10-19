<div
    data-toast-region
    class="pointer-events-none fixed right-4 bottom-4 z-[80] flex w-[min(22rem,calc(100vw-2rem))] flex-col gap-2"
    aria-live="polite"
    aria-relevant="additions"
    role="region"
    aria-label="Notifikasi"
>
    @foreach (['success' => 'mint', 'error' => 'coral', 'status' => 'signal'] as $key => $tone)
        @if (session($key))
            <div
                data-toast
                data-toast-tone="{{ $tone }}"
                class="pointer-events-auto rounded-card border border-ink-line bg-ink-raised px-4 py-3 text-sm text-paper shadow-lift"
                role="status"
            >
                {{ session($key) }}
            </div>
        @endif
    @endforeach
</div>
