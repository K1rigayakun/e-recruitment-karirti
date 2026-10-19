@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Dashboard — '.$user->name)

@section('content')
<div class="space-y-6" data-page-enter>
    <div>
        <h1 class="font-display text-2xl text-paper">Halo, {{ $user->name }}!</h1>
        <p class="mt-1 text-sm text-mist">Pantau lamaran dan lengkapi profilmu dari sini.</p>
    </div>

    @unless ($user->hasVerifiedEmail())
        <div class="rounded-card border border-sun/40 bg-sun/10 p-4 text-sm text-sun">
            Email kamu belum diverifikasi.
            <form method="POST" action="{{ route('verification.send') }}" class="inline">
                @csrf <button type="submit" class="link-draw underline">Kirim ulang tautan</button>.
            </form>
        </div>
    @endunless

    @if (session('toast'))
        <div x-data x-init="window.karirti?.showToast('{{ session('toast') }}', 'mint')" class="hidden"></div>
    @endif

    {{-- Progress ring --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="col-span-full sm:col-span-1 flex flex-col items-center gap-3 rounded-card border border-ink-line bg-ink-raised p-6">
            <x-ui.progress-ring :value="$progress['percent']" />
            <p class="text-sm text-mist text-center">Kelengkapan profil</p>
        </div>

        <div class="sm:col-span-1 lg:col-span-3 rounded-card border border-ink-line bg-ink-raised p-6">
            <p class="mb-3 text-sm font-semibold text-paper">Yang perlu dilengkapi:</p>
            <ul class="space-y-2">
                @foreach ($progress['items'] as $item)
                    @unless ($item['done'])
                        <li class="flex items-center gap-3 text-sm">
                            <span class="h-2 w-2 rounded-full bg-coral shrink-0"></span>
                            <span class="text-mist">{{ $item['label'] }}</span>
                            <a href="{{ $item['href'] }}" class="ml-auto link-draw text-signal text-xs shrink-0">Lengkapi →</a>
                        </li>
                    @endunless
                @endforeach
                @if (collect($progress['items'])->every(fn($i) => $i['done']))
                    <li class="text-sm text-mint">✓ Profil sudah lengkap!</li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Statistik placeholder --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Lamaran aktif" value="0" />
        <x-ui.stat label="Interview mendatang" value="0" />
        <x-ui.stat label="Kelengkapan profil" value="{{ $progress['percent'] }}%" />
    </div>

    {{-- Pintasan --}}
    <div class="flex flex-wrap gap-3">
        <x-ui.button variant="secondary" href="{{ route('applicant.profile.edit') }}">Edit profil</x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('applicant.cv.index') }}">Kelola CV</x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('applicant.projects.index') }}">Portofolio</x-ui.button>
        <x-ui.button variant="primary" href="{{ route('jobs.index') }}">Cari lowongan</x-ui.button>
    </div>
</div>
@endsection
