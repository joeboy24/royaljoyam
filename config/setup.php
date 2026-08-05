<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enforce first-run setup gate
    |--------------------------------------------------------------------------
    |
    | When true, incomplete installs are redirected to /setup. Feature tests
    | disable this by default (see phpunit.xml) so existing suites stay focused.
    | SetupWizardTest re-enables it explicitly.
    |
    */

    'enforce' => (bool) env('SETUP_ENFORCE', true),

    /*
    |--------------------------------------------------------------------------
    | Default category seeded during first-run
    |--------------------------------------------------------------------------
    */

    'default_category' => env('SETUP_DEFAULT_CATEGORY', 'General'),

    /*
    |--------------------------------------------------------------------------
    | Completion flag path
    |--------------------------------------------------------------------------
    |
    | Written when the first-run administrator is created. isComplete() still
    | derives readiness from database state; this file is an ops marker.
    |
    */

    'completion_flag' => storage_path('framework/setup.complete'),

];
