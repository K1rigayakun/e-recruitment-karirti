@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Notifikasi')

@section('content')
<div class="mx-auto max-w-2xl space-y-4" data-page-enter>
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl text-paper">Notifikasi</h1>
        @if (auth()->user()->unreadNotifications->isNotEmpty())
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <x-ui.button variant="ghost" type="submit" size="sm">Tandai semua dibaca</x-ui.button>
            </form>
        @endif
    </div>

    @if ($notifications->isEmpty())
        <x-ui.empty-state title="Belum ada notifikasi." description="Notifikasi status lamaran akan muncul di sini." />
    @else
        <div class="space-y-2" data-reveal="stagger">
            @foreach ($notifications as $notif)
                <div class="flex items-start gap-3 rounded-card border p-4 {{ is_null($notif->read_at) ? 'border-signal/40 bg-signal/5' : 'border-ink-line bg-ink-raised' }}">
                    <div class="flex-1">
                        <p class="text-sm text-paper">
                            @if ($notif->data['type'] === 'application_received')
                                Lamaran ke <strong>{{ $notif->data['job_title'] }}</strong> diterima.
                            @elseif ($notif->data['type'] === 'application_status_changed')
                                Status lamaran <strong>{{ $notif->data['job_title'] }}</strong> berubah menjadi <strong>{{ $notif->data['status_label'] }}</strong>.
                            @else
                                {{ $notif->data['message'] ?? 'Notifikasi baru.' }}
                            @endif
                        </p>
                        <p class="mt-1 text-xs text-mist">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if (is_null($notif->read_at))
                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                            @csrf
                            <button type="submit" class="text-xs text-signal hover:text-paper link-draw">Tandai dibaca</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
        <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
