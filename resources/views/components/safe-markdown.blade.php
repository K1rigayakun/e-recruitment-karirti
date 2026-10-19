{{-- SATU-SATUNYA tempat {!! !!} untuk teks buatan pengguna. Jangan disalin ke tempat lain. --}}
@props(['text' => ''])
<div {{ $attributes->class('prose-karirti') }}>
    {!! \Illuminate\Support\Str::markdown((string) $text, [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
        'max_nesting_level' => 10,
    ]) !!}
</div>
