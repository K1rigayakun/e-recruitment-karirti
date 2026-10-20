@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Lamaran Saya')

@section('content')
<div class="space-y-6" data-page-enter>
    <div>
        <h1 class="font-display text-2xl text-paper">Lamaran saya</h1>
        <p class="mt-1 text-sm text-mist">{{ $applications->total() }} lamaran total</p>
    </div>

    {{-- Filter status --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('applicant.applications.index') }}"
           class="rounded-chip px-3 py-1 text-xs {{ !request('status') ? 'bg-signal text-ink' : 'border border-ink-line text-mist hover:text-paper' }}">Semua</a>
        @foreach (\App\Enums\ApplicationStatus::cases() as $s)
            <a href="{{ route('applicant.applications.index', ['status' => $s->value]) }}"
               class="rounded-chip px-3 py-1 text-xs {{ request('status') === $s->value ? 'bg-signal text-ink' : 'border border-ink-line text-mist hover:text-paper' }}">
                {{ $s->label() }} @if(($counts[$s->value] ?? 0) > 0) ({{ $counts[$s->value] }}) @endif
            </a>
        @endforeach
    </div>

    @if ($applications->isEmpty())
        <x-ui.empty-state title="Belum ada lamaran." description="Jelajahi lowongan dan kirim lamaran pertamamu.">
            <x-slot:action>
                <x-ui.button variant="primary" href="{{ route('jobs.index') }}">Cari lowongan</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="space-y-3" data-reveal="stagger">
            @foreach ($applications as $app)
                <a href="{{ route('applicant.applications.show', $app) }}"
                   class="block rounded-card border border-ink-line bg-ink-raised p-4 hover:border-signal/40 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-paper truncate">{{ $app->jobPosting->title }}</h3>
                            <p class="mt-0.5 text-sm text-mist">{{ $app->jobPosting->companyProfile->company_name }}</p>
                            <p class="mt-1 text-xs text-mist">Dilamar {{ $app->created_at->diffForHumans() }}</p>
                        </div>
                        @php $tone = $app->status->color(); @endphp
                        <x-ui.badge :tone="$tone">{{ $app->status->label() }}</x-ui.badge>
                    </div>
                </a>
            @endforeach
        </div>
        <div>{{ $applications->links() }}</div>
    @endif
</div>
@endsection
