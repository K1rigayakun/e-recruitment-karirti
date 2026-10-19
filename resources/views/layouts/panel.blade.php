@php
    $role = $role ?? 'applicant';
    $menus = [
        'applicant' => [
            ['Ringkasan', url('/')],
            ['Profil', url('/')],
            ['Portofolio', url('/')],
            ['Lamaran', url('/')],
            ['Interview', url('/')],
        ],
        'company' => [
            ['Ringkasan', url('/')],
            ['Profil', url('/')],
            ['Lowongan', url('/')],
            ['Pelamar', url('/')],
            ['Laporan', url('/')],
        ],
        'admin' => [
            ['Ringkasan', url('/')],
            ['Pengguna', url('/')],
            ['Perusahaan', url('/')],
            ['Moderasi', url('/')],
            ['Laporan', url('/')],
        ],
    ];
    $items = $menus[$role] ?? $menus['applicant'];
    $roleLabel = [
        'applicant' => 'Pelamar',
        'company' => 'Perusahaan',
        'admin' => 'Admin',
    ][$role] ?? 'Pelamar';
@endphp
<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-dvh">
    <a class="skip-link" href="#main">Lewati ke konten</a>

    <div class="flex min-h-dvh">
        <aside class="hidden w-60 shrink-0 border-r border-ink-line bg-ink-raised/60 lg:flex lg:flex-col" aria-label="Navigasi panel">
            <div class="flex h-[4.5rem] items-center px-5">
                <a href="{{ url('/') }}" class="font-display font-bold text-paper">{{ config('app.name') }}</a>
            </div>
            <nav class="flex flex-1 flex-col gap-1 px-3 py-4">
                @foreach ($items as [$label, $href])
                    <a href="{{ $href }}" class="rounded-control px-3 py-2 text-sm text-mist hover:bg-ink hover:text-paper">{{ $label }}</a>
                @endforeach
            </nav>
            <p class="px-5 py-4 text-xs text-mist">Masuk sebagai {{ $roleLabel }}</p>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-[4.5rem] items-center justify-between gap-3 border-b border-ink-line bg-ink/80 px-4 backdrop-blur lg:px-8">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-control border border-ink-line lg:hidden"
                        data-nav-toggle
                        aria-expanded="false"
                        aria-controls="panel-drawer"
                        aria-label="Buka menu"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 5h14v2H3V5zm0 4h14v2H3V9zm0 4h14v2H3v-2z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <h1 class="font-display text-lg font-semibold">@yield('heading', 'Ringkasan')</h1>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="rounded-chip border border-ink-line px-3 py-1.5 text-sm text-mist" aria-label="Notifikasi">Bel</button>
                    <x-ui.dropdown align="right">
                        <x-slot:trigger>Akun</x-slot:trigger>
                        <a href="{{ url('/') }}" class="block px-3 py-2 text-sm text-paper hover:bg-ink" role="menuitem">Profil</a>
                        <button type="button" class="block w-full px-3 py-2 text-left text-sm text-paper hover:bg-ink" role="menuitem" data-motion-toggle>
                            <span data-motion-toggle-label>Kurangi animasi</span>
                        </button>
                    </x-ui.dropdown>
                </div>
            </header>

            <div class="fixed inset-0 z-50 hidden lg:hidden" data-nav-overlay>
                <div class="absolute inset-0 bg-ink/70" data-nav-close></div>
                <nav
                    id="panel-drawer"
                    class="absolute inset-y-0 left-0 flex w-[min(18rem,88vw)] flex-col gap-2 border-r border-ink-line bg-ink-raised p-5"
                    data-nav-panel
                    aria-label="Menu panel"
                >
                    <button type="button" class="self-end text-sm text-mist" data-nav-close>Tutup</button>
                    @foreach ($items as [$label, $href])
                        <a href="{{ $href }}" class="rounded-control px-3 py-2 text-paper" data-nav-close>{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            <main id="main" class="flex-1 px-4 py-6 lg:px-8" data-page-enter>
                @yield('content')
            </main>
            <footer class="border-t border-ink-line px-4 py-4 text-sm text-mist lg:px-8">
                {{ config('app.name') }} · panel
            </footer>
        </div>
    </div>

    <x-ui.toast-region />
</body>
</html>
