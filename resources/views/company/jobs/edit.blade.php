@extends('layouts.panel', ['role' => 'company'])

@section('title', 'Edit Lowongan')

@section('content')
<div class="mx-auto max-w-2xl space-y-6" data-page-enter>
    <h1 class="font-display text-2xl text-paper">Edit lowongan</h1>
    @include('company.jobs._form', ['job' => $job, 'action' => route('company.jobs.update', $job), 'method' => 'PUT'])
</div>
@endsection
