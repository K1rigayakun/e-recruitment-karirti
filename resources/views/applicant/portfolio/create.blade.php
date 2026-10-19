@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Tambah Project')

@section('content')
<div class="mx-auto max-w-xl space-y-6" data-page-enter>
    <div>
        <h1 class="font-display text-2xl text-paper">Tambah project</h1>
        <p class="mt-1 text-sm text-mist">Tautkan project GitHub kamu.</p>
    </div>

    @include('applicant.portfolio._form', ['project' => null, 'action' => route('applicant.projects.store'), 'method' => 'POST'])
</div>
@endsection
