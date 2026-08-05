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

];
