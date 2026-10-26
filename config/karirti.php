<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Aplikasi KarirTI
    |--------------------------------------------------------------------------
    */

    'cv_max_kb' => (int) env('CV_MAX_KB', 2048),
    'max_projects' => 6,
    'max_tech_tags' => 8,
    'admin_email' => env('ADMIN_EMAIL', ''),
    'admin_password' => env('ADMIN_PASSWORD', ''),
    'trusted_proxies' => env('TRUSTED_PROXIES', '*'),

    'roles' => [
        'admin' => 'admin',
        'company' => 'company',
        'applicant' => 'applicant',
    ],

    'pagination' => [
        'per_page' => 10,
        'per_page_admin' => 20,
    ],
];
