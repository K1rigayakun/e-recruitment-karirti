@extends('layouts.panel', ['role' => 'applicant'])

@section('title', 'Edit Project')

@section('content')
<div class="mx-auto max-w-xl space-y-6" data-page-enter>
    <div>
        <h1 class="font-display text-2xl text-paper">Edit project</h1>
    </div>

    @include('applicant.portfolio._form', ['project' => $project, 'action' => route('applicant.projects.update', $project), 'method' => 'PUT'])
</div>
@endsection
