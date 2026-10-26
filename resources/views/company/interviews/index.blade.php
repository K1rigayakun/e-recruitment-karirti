@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Agenda Interview')

@section('content')
<div class="space-y-6" data-page-enter>
    <div class="flex items-center justify-between">
        <h1 class="font-display text-2xl text-paper">Agenda interview</h1>
        <div class="flex gap-2">
            <a href="{{ route('company.interviews.index', ['filter' => '']) }}"
               class="rounded-chip px-3 py-1 text-xs {{ !request('filter') ? 'bg-signal text-ink' : 'border border-ink-line text-mist' }}">Mendatang</a>
            <a href="{{ route('company.interviews.index', ['filter' => 'past']) }}"
               class="rounded-chip px-3 py-1 text-xs {{ request('filter')==='past' ? 'bg-signal text-ink' : 'border border-ink-line text-mist' }}">Selesai</a>
        </div>
    </div>

    {{-- Kalender bulan sederhana --}}
    @php
        $firstDay  = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDow  = $firstDay->dayOfWeek; // 0=Minggu
        $prevMonth = $firstDay->copy()->subMonth();
        $nextMonth = $firstDay->copy()->addMonth();
    @endphp
    <div class="rounded-card border border-ink-line bg-ink-raised p-4">
        <div class="mb-3 flex items-center justify-between">
            <a href="{{ route('company.interviews.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
               class="rounded-control border border-ink-line px-3 py-1 text-sm text-mist hover:text-paper">←</a>
            <p class="font-display text-paper">{{ $firstDay->translatedFormat('F Y') }}</p>
            <a href="{{ route('company.interviews.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
               class="rounded-control border border-ink-line px-3 py-1 text-sm text-mist hover:text-paper">→</a>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center">
            @foreach (['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
                <div class="text-xs text-mist py-1">{{ $day }}</div>
            @endforeach
            @for ($i = 0; $i < $startDow; $i++)
                <div></div>
            @endfor
            @for ($d = 1; $d <= $daysInMonth; $d++)
                <div class="relative rounded-control py-1 text-sm {{ $calendarDates->contains($d) ? 'bg-signal/20 font-bold text-signal' : 'text-mist' }}">
                    {{ $d }}
                    @if ($calendarDates->contains($d))
                        <span class="absolute bottom-0.5 left-1/2 -translate-x-1/2 h-1 w-1 rounded-full bg-signal"></span>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    @if ($interviews->isEmpty())
        <x-ui.empty-state title="Tidak ada interview." description="Jadwalkan interview dari halaman detail pelamar." />
    @else
        <div class="space-y-3" data-reveal="stagger">
            @foreach ($interviews as $interview)
                <div class="rounded-card border border-ink-line bg-ink-raised p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-medium text-paper">
                                {{ $interview->application->applicantProfile->user->name }}
                            </p>
                            <p class="text-sm text-mist">{{ $interview->application->jobPosting->title }}</p>
                            <p class="mt-2 text-sm text-paper">
                                {{ $interview->scheduled_at->setTimezone('Asia/Jakarta')->translatedFormat('l, d M Y H:i') }} WIB
                            </p>
                            <p class="text-xs text-mist">
                                {{ $interview->duration_minutes }} menit · {{ $interview->mode->label() }}
                                @if ($interview->meeting_url)
                                    · <a href="{{ $interview->meeting_url }}" target="_blank" rel="noopener noreferrer" class="link-draw text-signal">Tautan meeting</a>
                                @elseif ($interview->location)
                                    · {{ $interview->location }}
                                @endif
                            </p>
                        </div>
                        @php
                            $tone = match($interview->status->value) {
                                'confirmed' => 'mint', 'cancelled' => 'coral',
                                'reschedule_requested' => 'sun', default => 'signal'
                            };
                        @endphp
                        <div class="shrink-0 space-y-2 text-right">
                            <x-ui.badge :tone="$tone">{{ $interview->status->label() }}</x-ui.badge>
                            @if (!in_array($interview->status->value, ['cancelled', 'completed']))
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('company.interviews.destroy', $interview) }}"
                                        data-confirm="Batalkan interview ini?">
                                        @csrf @method('DELETE')
                                        <x-ui.button variant="ghost" type="submit" size="sm">Batalkan</x-ui.button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div>{{ $interviews->links() }}</div>
    @endif
</div>
@endsection
