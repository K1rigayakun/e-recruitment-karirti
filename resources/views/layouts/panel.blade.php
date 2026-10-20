@php
    $role = $role ?? "applicant";
    $menus = [
        "applicant" => [
            ["Ringkasan",  route("applicant.dashboard")],
            ["Profil",     route("applicant.profile.edit")],
            ["CV",         route("applicant.cv.index")],
            ["Portofolio", route("applicant.projects.index")],
            ["Lamaran",    route("applicant.applications.index")],
            ["Notifikasi", route("notifications.index")],
        ],
            ["Profil",     route("applicant.profile.edit")],
            ["CV",         route("applicant.cv.index")],
            ["Portofolio", route("applicant.projects.index")],
            ["Lamaran",    "#"],
            ["Interview",  "#"],
        ],
        "company" => [
            ["Ringkasan", route("company.dashboard")],
            ["Profil",    route("company.profile.edit")],
            ["Lowongan",  route("company.jobs.index")],
            ["Pelamar",   "#"],
            ["Laporan",   "#"],
        ],
            ["Profil",    "#"],
            ["Lowongan",  "#"],
            ["Pelamar",   "#"],
            ["Laporan",   "#"],
        ],
        "admin" => [
            ["Ringkasan",  route("admin.dashboard")],
            ["Pengguna",   "#"],
            ["Perusahaan", "#"],
            ["Moderasi",   "#"],
            ["Laporan",    "#"],
        ],
    ];
    $items = $menus[$role] ?? $menus["applicant"];
    $roleLabel = ["applicant" => "Pelamar", "company" => "Perusahaan", "admin" => "Admin"][$role] ?? "Pengguna";
@endphp
<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    @include("layouts.partials.head")
</head>
<body class="min-h-dvh">
    <a class="skip-link" href="#main">Lewati ke konten</a>

    <div class="flex min-h-dvh">
        {{-- Sidebar desktop --}}
        <aside class="hidden w-60 shrink-0 border-r border-ink-line bg-ink-raised/60 lg:flex lg:flex-col" aria-label="Navigasi panel">
            <div class="flex h-[4.5rem] items-center px-5">
                <a href="{{ route("home") }}" class="font-display font-bold text-paper">{{ config("app.name") }}</a>
            </div>
            <nav class="flex flex-1 flex-col gap-1 px-3 py-4">
                @foreach ($items as [$label, $href])
                    <a href="{{ $href }}"
                       class="rounded-control px-3 py-2 text-sm transition-colors {{ request()->url() === $href ? "bg-ink text-paper" : "text-mist hover:bg-ink hover:text-paper" }}">
                       {{ $label }}
                    </a>
                @endforeach
            </nav>
            <div class="px-5 py-4 space-y-2">
                <p class="text-xs text-mist">Masuk sebagai {{ $roleLabel }}</p>
                <form method="POST" action="{{ route("logout") }}">
                    @csrf
                    <button type="submit" class="text-xs text-mist hover:text-coral link-draw">Keluar</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 flex h-[4.5rem] items-center justify-between gap-3 border-b border-ink-line bg-ink/80 px-4 backdrop-blur lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-control border border-ink-line lg:hidden"
                        data-nav-toggle aria-expanded="false" aria-controls="panel-drawer" aria-label="Buka menu">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 5h14v2H3V5zm0 4h14v2H3V9zm0 4h14v2H3v-2z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <span class="font-display text-base font-semibold text-paper">@yield("title", "Dashboard")</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="rounded-chip border border-ink-line px-3 py-1.5 text-sm text-mist hover:text-paper" data-motion-toggle>
                        <span data-motion-toggle-label>Animasi</span>
                    </button>
                    <form method="POST" action="{{ route("logout") }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="text-sm text-mist hover:text-coral">Keluar</button>
                    </form>
                </div>
            </header>

            {{-- Mobile drawer --}}
            <div class="fixed inset-0 z-50 hidden lg:hidden" data-nav-overlay>
                <div class="absolute inset-0 bg-ink/70" data-nav-close></div>
                <nav id="panel-drawer" class="absolute inset-y-0 left-0 flex w-[min(18rem,88vw)] flex-col gap-2 border-r border-ink-line bg-ink-raised p-5"
                    data-nav-panel aria-label="Menu panel">
                    <button type="button" class="self-end text-sm text-mist" data-nav-close>Tutup</button>
                    @foreach ($items as [$label, $href])
                        <a href="{{ $href }}" class="rounded-control px-3 py-2 text-paper hover:bg-ink" data-nav-close>{{ $label }}</a>
                    @endforeach
                    <form method="POST" action="{{ route("logout") }}" class="mt-auto">
                        @csrf
                        <button type="submit" class="text-sm text-mist hover:text-coral">Keluar</button>
                    </form>
                </nav>
            </div>

            <main id="main" class="flex-1 px-4 py-6 lg:px-8">
                @yield("content")
            </main>

            @include("layouts.partials.footer")
        </div>
    </div>

    <x-ui.toast-region />
</body>
</html>


