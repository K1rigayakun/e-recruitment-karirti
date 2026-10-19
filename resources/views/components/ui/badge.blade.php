@props([
    'status' => null,
    'tone' => 'mist',
])

@php
    $fromStatus = [
        'applied' => 'signal',
        'viewed' => 'sun',
        'interview' => 'violet',
        'accepted' => 'mint',
        'rejected' => 'coral',
        'draft' => 'mist',
        'open' => 'mint',
        'closed' => 'mist',
    ];
    $tone = $status ? ($fromStatus[$status] ?? $tone) : $tone;
    $tones = [
        'signal' => 'bg-signal/15 text-signal',
        'sun' => 'bg-sun/15 text-sun',
        'violet' => 'bg-violet/15 text-violet',
        'mint' => 'bg-mint/15 text-mint',
        'coral' => 'bg-coral/15 text-coral',
        'mist' => 'bg-mist/15 text-mist',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-chip px-2.5 py-1 text-xs font-semibold', $tones[$tone] ?? $tones['mist']]) }}>
    {{ $slot }}
</span>
