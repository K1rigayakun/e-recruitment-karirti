@extends('layouts.panel', ['role' => 'admin'])

@section('title', 'Log Audit')

@section('content')
<div class="space-y-4" data-page-enter>
    <h1 class="font-display text-2xl text-paper">Log audit</h1>

    <div class="space-y-2" data-reveal="stagger">
        @forelse ($logs as $log)
            <div class="flex items-start gap-4 rounded-card border border-ink-line bg-ink-raised p-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-paper">{{ $log->action }}</p>
                    <p class="text-xs text-mist">
                        {{ $log->user?->name ?? 'Sistem' }} · {{ $log->ip }} · {{ $log->created_at?->diffForHumans() }}
                    </p>
                    @if ($log->meta)
                        <p class="mt-1 text-xs text-mist">{{ json_encode($log->meta) }}</p>
                    @endif
                </div>
            </div>
        @empty
            <x-ui.empty-state title="Belum ada log." description="Tindakan admin akan tercatat di sini." />
        @endforelse
    </div>
    <div>{{ $logs->links() }}</div>
</div>
@endsection
