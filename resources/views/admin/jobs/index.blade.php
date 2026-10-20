@extends('layouts.panel', ['role' => 'admin'])

@section('title', 'Moderasi Lowongan')

@section('content')
<div class="space-y-6" data-page-enter>
    <h1 class="font-display text-2xl text-paper">Moderasi lowongan</h1>

    <form method="GET" class="flex gap-3">
        <select name="status" onchange="this.form.submit()"
            class="rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
            <option value="">Semua</option>
            @foreach (\App\Enums\JobStatus::cases() as $s)
                <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
            @endforeach
        </select>
    </form>

    <div class="space-y-2" data-reveal="stagger">
        @forelse ($jobs as $job)
            <div class="flex items-center gap-4 rounded-card border border-ink-line bg-ink-raised p-4">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-paper truncate">{{ $job->title }}</p>
                    <p class="text-xs text-mist">{{ $job->companyProfile->company_name }}</p>
                    @if ($job->admin_note)
                        <p class="mt-1 text-xs text-coral">Catatan admin: {{ $job->admin_note }}</p>
                    @endif
                </div>
                @php $tone = match($job->status->value) { 'open' => 'mint', 'closed' => 'coral', default => 'mist' }; @endphp
                <x-ui.badge :tone="$tone">{{ $job->status->label() }}</x-ui.badge>
                @if ($job->status->value === 'open')
                    <x-ui.button variant="danger" size="sm" data-dialog-open="takedown-{{ $job->id }}">Turunkan</x-ui.button>
                    <x-ui.modal id="takedown-{{ $job->id }}" title="Turunkan lowongan ini?">
                        <form method="POST" action="{{ route('admin.jobs.takedown', $job) }}" id="td-{{ $job->id }}">
                            @csrf
                            <x-ui.textarea name="note" label="Alasan (wajib, terlihat perusahaan)" required />
                        </form>
                        <x-slot:actions>
                            <x-ui.button variant="danger" type="submit" form="td-{{ $job->id }}">Turunkan</x-ui.button>
                            <x-ui.button variant="ghost" data-dialog-close>Batal</x-ui.button>
                        </x-slot:actions>
                    </x-ui.modal>
                @endif
            </div>
        @empty
            <x-ui.empty-state title="Tidak ada lowongan." description="Belum ada lowongan yang perlu ditinjau." />
        @endforelse
    </div>
    <div>{{ $jobs->links() }}</div>
</div>
@endsection
