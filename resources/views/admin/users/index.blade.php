@extends('layouts.panel', ['role' => 'admin'])

@section('title', 'Kelola Pengguna')

@section('content')
<div class="space-y-6" data-page-enter>
    <h1 class="font-display text-2xl text-paper">Pengguna</h1>

    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3">
        <input name="q" value="{{ request('q') }}" placeholder="Cari nama / email"
            class="rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper placeholder-mist focus:border-signal focus:outline-none">
        <select name="role" data-autosubmit
            class="rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
            <option value="">Semua role</option>
            @foreach (\App\Enums\UserRole::cases() as $r)
                <option value="{{ $r->value }}" @selected(request('role') === $r->value)>{{ $r->label() }}</option>
            @endforeach
        </select>
        <select name="status" data-autosubmit
            class="rounded-control border border-ink-line bg-ink px-3 py-2 text-sm text-paper focus:border-signal focus:outline-none">
            <option value="">Semua status</option>
            <option value="active" @selected(request('status')==='active')>Aktif</option>
            <option value="inactive" @selected(request('status')==='inactive')>Nonaktif</option>
        </select>
        <x-ui.button variant="secondary" type="submit">Cari</x-ui.button>
    </form>

    @if ($errors->any())
        <div class="rounded-card border border-coral/40 bg-coral/10 p-3 text-sm text-coral">
            @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    <div class="space-y-2" data-reveal="stagger">
        @foreach ($users as $user)
            <div class="flex items-center gap-4 rounded-card border border-ink-line bg-ink-raised p-4">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-paper">{{ $user->name }}</p>
                    <p class="text-xs text-mist">{{ $user->email }} · {{ $user->role->label() }}</p>
                </div>
                <x-ui.badge :tone="$user->is_active ? 'mint' : 'coral'">
                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                </x-ui.badge>
                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                    @csrf @method('PATCH')
                    <x-ui.button variant="ghost" type="submit" size="sm">
                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </x-ui.button>
                </form>
            </div>
        @endforeach
    </div>
    <div>{{ $users->links() }}</div>
</div>
@endsection
