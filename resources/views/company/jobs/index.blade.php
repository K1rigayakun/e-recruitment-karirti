@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Kelola Lowongan')

@section('content')
<div class="space-y-6" data-page-enter>
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl text-paper">Lowongan saya</h1>
        <x-ui.button variant="primary" href="{{ route('company.jobs.create') }}">+ Buat lowongan</x-ui.button>
    </div>

    {{-- Filter status --}}
    <div class="flex flex-wrap gap-2">
        @foreach (['', 'draft', 'open', 'closed'] as $s)
            <a href="{{ route('company.jobs.index', $s ? ['status' => $s] : []) }}"
               class="rounded-chip px-3 py-1 text-xs {{ request('status', '') === $s ? 'bg-signal text-ink' : 'border border-ink-line text-mist hover:text-paper' }}">
                {{ $s ? ucfirst($s) : 'Semua' }}
            </a>
        @endforeach
    </div>

    @if ($jobs->isEmpty())
        <x-ui.empty-state title="Belum ada lowongan." description="Buat lowongan pertama dan mulai terima lamaran.">
            <x-slot:action>
                <x-ui.button variant="primary" href="{{ route('company.jobs.create') }}">Buat lowongan</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="space-y-3" data-reveal="stagger">
            @foreach ($jobs as $job)
                <div class="flex items-center gap-4 rounded-card border border-ink-line bg-ink-raised p-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-medium text-paper truncate">{{ $job->title }}</h3>
                            @php
                                $tone = match($job->status->value) {
                                    'open' => 'mint', 'closed' => 'coral', default => 'mist'
                                };
                            @endphp
                            <x-ui.badge :tone="$tone">{{ $job->status->label() }}</x-ui.badge>
                        </div>
                        <p class="mt-1 text-xs text-mist">
                            {{ $job->location }} · {{ $job->job_type->label() }} · {{ $job->applications_count }} pelamar
                            @if ($job->deadline_at) · Tenggat {{ $job->deadline_at->format('d M Y') }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if ($job->status->value === 'draft')
                            <form method="POST" action="{{ route('company.jobs.publish', $job) }}">
                                @csrf
                                <x-ui.button variant="secondary" type="submit" size="sm">Publikasi</x-ui.button>
                            </form>
                        @elseif ($job->status->value === 'open')
                            <form method="POST" action="{{ route('company.jobs.close', $job) }}">
                                @csrf
                                <x-ui.button variant="ghost" type="submit" size="sm">Tutup</x-ui.button>
                            </form>
                        @endif
                        <x-ui.button variant="ghost" href="{{ route('company.jobs.edit', $job) }}" size="sm">Edit</x-ui.button>
                        <a href="{{ route('jobs.show', $job->slug) }}" target="_blank" rel="noopener noreferrer"
                           class="rounded-control border border-ink-line px-2 py-1 text-xs text-mist hover:text-paper">Pratinjau</a>
                        @if ($job->status->value === 'draft' && $job->applications_count === 0)
                            <form method="POST" action="{{ route('company.jobs.destroy', $job) }}"
                                onsubmit="return confirm('Hapus lowongan ini?')">
                                @csrf @method('DELETE')
                                <x-ui.button variant="danger" type="submit" size="sm">Hapus</x-ui.button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div>{{ $jobs->links() }}</div>
    @endif
</div>
@endsection
