@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Pelamar — '.$job->title)

@section('content')
<div class="space-y-6" data-page-enter>
    <div>
        <p class="text-sm text-mist"><a href="{{ route('company.jobs.index') }}" class="link-draw text-signal">← Lowongan</a></p>
        <h1 class="mt-1 font-display text-2xl text-paper">Pelamar: {{ $job->title }}</h1>
    </div>

    {{-- Funnel status (count-up) --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6" data-reveal="stagger">
        @foreach (\App\Enums\ApplicationStatus::cases() as $s)
            <div class="rounded-card border border-ink-line bg-ink-raised p-3 text-center">
                <p class="font-display text-xl font-bold" style="color:var(--color-{{ $s->color() }})"
                   data-count-to="{{ $funnel[$s->value] ?? 0 }}">{{ $funnel[$s->value] ?? 0 }}</p>
                <p class="text-xs text-mist">{{ $s->label() }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('company.applicants.index', $job) }}" class="flex flex-wrap gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama..."
            class="rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper placeholder-mist focus:border-signal focus:outline-none">
        <select name="status" data-autosubmit
            class="rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
            <option value="">Semua status</option>
            @foreach (\App\Enums\ApplicationStatus::cases() as $s)
                <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
            @endforeach
        </select>
        <x-ui.button variant="secondary" type="submit">Cari</x-ui.button>
    </form>

    @if ($applications->isEmpty())
        <x-ui.empty-state title="Belum ada pelamar." description="Belum ada yang melamar lowongan ini." />
    @else
        <div class="space-y-2" data-reveal="stagger">
            @foreach ($applications as $app)
                <a href="{{ route('company.applicants.show', $app) }}"
                   class="flex items-center gap-4 rounded-card border border-ink-line bg-ink-raised p-4 hover:border-signal/40 transition-colors">
                    {{-- Avatar inisial --}}
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-signal/20 font-display text-sm font-bold text-signal">
                        {{ Str::upper(Str::substr($app->applicantProfile->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-medium text-paper">{{ $app->applicantProfile->user->name }}</p>
                            @if ($app->applicantProfile->alumni_verified_at)
                                <span class="rounded-chip bg-mint/20 px-2 py-0.5 text-xs text-mint">Alumni ✓</span>
                            @endif
                        </div>
                        <p class="text-xs text-mist mt-0.5">
                            {{ $app->applicantProfile->headline ?? 'Pelamar' }}
                            @if ($app->applicantProfile->graduation_year) · Lulus {{ $app->applicantProfile->graduation_year }} @endif
                        </p>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach ($app->applicantProfile->skills->take(4) as $skill)
                                <span class="rounded-chip border border-ink-line px-2 py-0.5 text-xs text-mist">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        @php $tone = $app->status->color(); @endphp
                        <x-ui.badge :tone="$tone">{{ $app->status->label() }}</x-ui.badge>
                        <p class="mt-1 text-xs text-mist">{{ $app->created_at->diffForHumans() }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div>{{ $applications->links() }}</div>
    @endif
</div>
@endsection
