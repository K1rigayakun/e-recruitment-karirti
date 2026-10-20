@extends('layouts.panel', ['role' => 'admin'])

@section('title', 'Verifikasi Perusahaan')

@section('content')
<div class="space-y-6" data-page-enter>
    <h1 class="font-display text-2xl text-paper">Antrian verifikasi perusahaan</h1>

    @if ($pending->isEmpty())
        <x-ui.empty-state title="Tidak ada antrian." description="Semua perusahaan sudah diverifikasi." />
    @else
        <div class="space-y-3" data-reveal="stagger">
            @foreach ($pending as $company)
                <div class="rounded-card border border-ink-line bg-ink-raised p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="font-medium text-paper">{{ $company->company_name }}</p>
                            <p class="text-sm text-mist">{{ $company->user->email }}</p>
                            @if ($company->city) <p class="text-xs text-mist">{{ $company->city }}</p> @endif
                            @if ($company->website)
                                <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer"
                                   class="link-draw text-xs text-signal">{{ $company->website }}</a>
                            @endif
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <form method="POST" action="{{ route('admin.companies.approve', $company) }}">
                                @csrf
                                <x-ui.button variant="primary" type="submit" size="sm">Setujui</x-ui.button>
                            </form>
                            <x-ui.button variant="danger" size="sm" data-dialog-open="reject-{{ $company->id }}">Tolak</x-ui.button>
                        </div>
                    </div>
                </div>

                <x-ui.modal id="reject-{{ $company->id }}" title="Tolak {{ $company->company_name }}">
                    <form method="POST" action="{{ route('admin.companies.reject', $company) }}" id="reject-form-{{ $company->id }}">
                        @csrf
                        <x-ui.textarea name="note" label="Alasan penolakan (wajib)" required />
                    </form>
                    <x-slot:actions>
                        <x-ui.button variant="danger" type="submit" form="reject-form-{{ $company->id }}">Tolak</x-ui.button>
                        <x-ui.button variant="ghost" data-dialog-close>Batal</x-ui.button>
                    </x-slot:actions>
                </x-ui.modal>
            @endforeach
        </div>
        <div>{{ $pending->links() }}</div>
    @endif
</div>
@endsection
