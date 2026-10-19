<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/styleguide', function () {
    abort_unless(app()->environment('local'), 404);

    return view('styleguide.index');
})->name('styleguide');

Route::get('/styleguide/layout/app', function () {
    abort_unless(app()->environment('local'), 404);

    return view('styleguide.layout-app');
})->name('styleguide.layout.app');

Route::get('/styleguide/layout/guest', function () {
    abort_unless(app()->environment('local'), 404);

    return view('styleguide.layout-guest');
})->name('styleguide.layout.guest');

Route::get('/styleguide/layout/panel', function () {
    abort_unless(app()->environment('local'), 404);

    $role = request()->query('role', 'applicant');
    if (! in_array($role, ['applicant', 'company', 'admin'], true)) {
        $role = 'applicant';
    }

    return view('styleguide.layout-panel', ['role' => $role]);
})->name('styleguide.layout.panel');
