<footer class="border-t border-ink-line/80 bg-ink">
    <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-mist">{{ config('app.name') }} — bursa kerja lulusan D3 TI.</p>
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <button type="button" class="link-draw text-mist hover:text-paper" data-motion-toggle>
                <span data-motion-toggle-label>Kurangi animasi</span>
            </button>
            <a class="link-draw text-mist hover:text-paper" href="{{ url('/') }}">Beranda</a>
        </div>
    </div>
</footer>
