{{-- SATU-SATUNYA tempat {!! !!} untuk teks buatan pengguna. Jangan disalin ke tempat lain. --}}
@props(['text' => ''])
@php
    // Hapus tag gambar markdown ![alt](url) untuk mencegah pelacak IP pelamar
    $cleanText = preg_replace('/!\[([^\]]*)\]\([^)]*\)/', '$1', (string) $text);
@endphp
<div {{ $attributes->class('prose-karirti') }}>
    {!! \Illuminate\Support\Str::markdown($cleanText, [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
        'max_nesting_level' => 10,
    ]) !!}
</div>
