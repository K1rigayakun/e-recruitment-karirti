<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-dvh" data-ambient>
    @include('layouts.partials.ambient')
    <a class="skip-link" href="#main">Lewati ke konten</a>

    <div class="grid min-h-dvh lg:grid-cols-2">
        <div class="flex flex-col px-4 py-8 sm:px-8 lg:px-12">
            <header>
                <a href="{{ url('/') }}" class="font-display text-lg font-bold text-paper">{{ config('app.name') }}</a>
            </header>
            <main id="main" class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center py-10" data-page-enter>
                @yield('content')
            </main>
            <footer class="text-sm text-mist">
                <button type="button" class="link-draw" data-motion-toggle>
                    <span data-motion-toggle-label>Kurangi animasi</span>
                </button>
            </footer>
        </div>

        <aside class="relative hidden overflow-hidden border-l border-ink-line bg-ink-raised lg:flex lg:flex-col lg:justify-center lg:px-12" aria-hidden="true">
            <p class="mb-6 font-display text-2xl text-paper">Lamaran adalah sebuah pipeline.</p>
            <p class="measure mb-10 text-mist">Dari daftar sampai keputusan, setiap tahap terlihat. Tidak ada status yang menghilang diam-diam.</p>
            @include('layouts.partials.pipeline-art')
        </aside>
    </div>

    <x-ui.toast-region />
</body>
</html>
