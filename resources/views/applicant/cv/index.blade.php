@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Kelola CV')

@section('content')
<div class="mx-auto max-w-2xl space-y-8" data-page-enter>
    <div>
        <h1 class="font-display text-2xl text-paper">CV saya</h1>
        <p class="mt-1 text-sm text-mist">Unggah CV dalam format PDF. Maks {{ config('karirti.cv_max_kb') / 1024 }} MB.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-card border border-coral/40 bg-coral/10 p-4 text-sm text-coral">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Upload baru --}}
    <form method="POST" action="{{ route('applicant.cv.store') }}" enctype="multipart/form-data" data-validate>
        @csrf
        <x-ui.file-drop name="cv" accept=".pdf" label="Seret PDF ke sini atau klik untuk pilih file" />
        <div class="mt-4">
            <x-ui.button variant="primary" type="submit" loading loading-text="Mengunggah...">
                Unggah CV
            </x-ui.button>
        </div>
    </form>

    {{-- Daftar CV --}}
    @if ($documents->isEmpty())
        <x-ui.empty-state title="Belum ada CV." description="Unggah CV PDF pertamamu di atas." />
    @else
        <div class="space-y-3" data-reveal="stagger">
            @foreach ($documents as $doc)
                @php $isActive = $profile->current_cv_document_id === $doc->id; @endphp
                <div class="flex items-center gap-4 rounded-card border p-4 {{ $isActive ? 'border-signal/50 bg-signal/5' : 'border-ink-line bg-ink-raised' }}">
                    <svg class="h-8 w-8 shrink-0 text-coral" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2a2 2 0 012 2v1h3a1 1 0 011 1v14a1 1 0 01-1 1H7a1 1 0 01-1-1V6a1 1 0 011-1h3V4a2 2 0 012-2z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="truncate text-sm font-medium text-paper">{{ $doc->original_name }}</p>
                        <p class="text-xs text-mist">{{ number_format($doc->size_bytes / 1024, 1) }} KB · {{ $doc->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if ($isActive)
                            <span class="rounded-chip bg-signal/20 px-2 py-0.5 text-xs text-signal">Aktif</span>
                        @else
                            <form method="POST" action="{{ route('applicant.cv.set-active', $doc) }}">
                                @csrf @method('PATCH')
                                <x-ui.button variant="ghost" type="submit" size="sm">Jadikan aktif</x-ui.button>
                            </form>
                        @endif
                        <a href="{{ route('documents.show', $doc) }}" target="_blank" rel="noopener noreferrer"
                           class="rounded-control border border-ink-line px-3 py-1.5 text-xs text-mist hover:text-paper">Lihat</a>
                        <form method="POST" action="{{ route('applicant.cv.destroy', $doc) }}"
                            data-confirm="Hapus CV ini?">
                            @csrf @method('DELETE')
                            <x-ui.button variant="danger" type="submit" size="sm">Hapus</x-ui.button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
