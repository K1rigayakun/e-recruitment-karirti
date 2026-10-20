@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Buat Lowongan')

@section('content')
<div class="mx-auto max-w-2xl space-y-6" data-page-enter>
    <h1 class="font-display text-2xl text-paper">Buat lowongan baru</h1>
    @include('company.jobs._form', ['job' => null, 'action' => route('company.jobs.store'), 'method' => 'POST'])
</div>
@endsection
