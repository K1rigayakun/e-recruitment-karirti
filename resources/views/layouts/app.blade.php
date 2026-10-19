<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-dvh" @if (! empty($ambient)) data-ambient @endif>
    <div class="pointer-events-none fixed top-0 left-0 z-[60] h-0.5 w-full bg-signal" data-scroll-progress></div>
    <a class="skip-link" href="#main">Lewati ke konten</a>
    @if (! empty($ambient))
        @include('layouts.partials.ambient')
    @endif

    <header class="sticky top-0 z-40 border-b border-transparent" data-nav>
        <div class="mx-auto flex h-full max-w-6xl items-center justify-between gap-4 px-4">
            <a href="{{ url('/') }}" class="font-display text-lg font-bold tracking-tight text-paper">
                {{ config('app.name') }}
            </a>

            <nav class="hidden items-center gap-6 md:flex" aria-label="Utama">
                <a class="link-draw text-sm text-mist hover:text-paper" href="{{ url('/') }}">Lowongan</a>
                <a class="link-draw text-sm text-mist hover:text-paper" href="{{ url('/') }}">Untuk Perusahaan</a>
            </nav>

            <div class="hidden items-center gap-2 md:flex">
                <x-ui.button variant="ghost" href="{{ url('/') }}">Masuk</x-ui.button>
                <x-ui.button variant="primary" href="{{ url('/') }}">Daftar</x-ui.button>
            </div>

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-control border border-ink-line text-paper md:hidden"
                data-nav-toggle
                aria-expanded="false"
                aria-controls="mobile-nav"
                aria-label="Buka menu"
            >
                <span class="sr-only">Menu</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M3 5h14v2H3V5zm0 4h14v2H3V9zm0 4h14v2H3v-2z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </header>

    <div class="fixed inset-0 z-50 hidden md:hidden" data-nav-overlay>
        <div class="absolute inset-0 bg-ink/70" data-nav-close></div>
        <nav
            id="mobile-nav"
            class="absolute inset-y-0 right-0 flex w-[min(20rem,88vw)] flex-col gap-4 border-l border-ink-line bg-ink-raised p-6"
            data-nav-panel
            aria-label="Menu seluler"
        >
            <button type="button" class="self-end rounded-control px-3 py-2 text-sm text-mist" data-nav-close>Tutup</button>
            <a class="text-lg text-paper" href="{{ url('/') }}" data-nav-close>Lowongan</a>
            <a class="text-lg text-paper" href="{{ url('/') }}" data-nav-close>Untuk Perusahaan</a>
            <a class="text-lg text-paper" href="{{ url('/') }}" data-nav-close>Masuk</a>
            <x-ui.button variant="primary" href="{{ url('/') }}">Daftar</x-ui.button>
        </nav>
    </div>

    <main id="main" data-page-enter>
        @yield('content')
    </main>

    @include('layouts.partials.footer')
    <x-ui.toast-region />
</body>
</html>
