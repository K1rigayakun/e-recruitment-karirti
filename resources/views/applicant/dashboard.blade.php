@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Dashboard')

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
                @csrf <button type="submit" class="underline link-draw">Kirim ulang tautan</button>.
            </form>
        </div>
    @endunless

    {{-- Progress ring + item --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex flex-col items-center gap-3 rounded-card border border-ink-line bg-ink-raised p-5">
            <x-ui.progress-ring :value="$progress['percent']" />
            <p class="text-xs text-mist text-center">Kelengkapan profil</p>
        </div>
        <div class="sm:col-span-1 lg:col-span-3 rounded-card border border-ink-line bg-ink-raised p-5">
            <p class="mb-3 text-sm font-semibold text-paper">Yang perlu dilengkapi:</p>
            <ul class="space-y-2">
                @foreach ($progress['items'] as $item)
                    @unless ($item['done'])
                        <li class="flex items-center gap-3 text-sm">
                            <span class="h-2 w-2 shrink-0 rounded-full bg-coral"></span>
                            <span class="text-mist">{{ $item['label'] }}</span>
                            <a href="{{ $item['href'] }}" class="ml-auto shrink-0 link-draw text-xs text-signal">Lengkapi →</a>
                        </li>
                    @endunless
                @endforeach
                @if (collect($progress['items'])->every(fn($i) => $i['done']))
                    <li class="text-sm text-mint">✓ Profil sudah lengkap!</li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Statistik lamaran --}}
    <div class="grid gap-4 sm:grid-cols-4" data-reveal="stagger">
        @foreach (\App\Enums\ApplicationStatus::cases() as $s)
            <div class="rounded-card border border-ink-line bg-ink-raised p-4 text-center">
                <p class="font-display text-2xl font-bold" style="color:var(--color-{{ $s->color() }})">
                    {{ $applicationCounts[$s->value] ?? 0 }}
                </p>
                <p class="mt-1 text-xs text-mist">{{ $s->label() }}</p>
            </div>
        @endforeach
    </div>

    {{-- Lamaran terbaru --}}
    @if ($recentApplications->isNotEmpty())
        <div>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-display text-lg text-paper">Lamaran terbaru</h2>
                <a href="{{ route('applicant.applications.index') }}" class="link-draw text-xs text-signal">Lihat semua →</a>
            </div>
            <div class="space-y-2" data-reveal="stagger">
                @foreach ($recentApplications as $app)
                    <a href="{{ route('applicant.applications.show', $app) }}"
                       class="flex items-center justify-between gap-3 rounded-card border border-ink-line bg-ink-raised p-3 hover:border-signal/40 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-medium text-paper">{{ $app->jobPosting->title }}</p>
                            <p class="text-xs text-mist">{{ $app->jobPosting->companyProfile->company_name }}</p>
                        </div>
                        <x-ui.badge :tone="$app->status->color()">{{ $app->status->label() }}</x-ui.badge>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <x-ui.empty-state title="Belum ada lamaran." description="Jelajahi lowongan dan kirim lamaran pertamamu.">
            <x-slot:action>
                <x-ui.button variant="primary" href="{{ route('jobs.index') }}">Cari lowongan</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @endif

    <div class="flex flex-wrap gap-3">
        <x-ui.button variant="secondary" href="{{ route('applicant.profile.edit') }}">Edit profil</x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('applicant.cv.index') }}">Kelola CV</x-ui.button>
        <x-ui.button variant="primary" href="{{ route('jobs.index') }}">Cari lowongan</x-ui.button>
    </div>
</div>
@endsection
