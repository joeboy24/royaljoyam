<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard sidebar navigation
    |--------------------------------------------------------------------------
    |
    | Each item may match multiple URL patterns (Laravel request()->is() rules).
    | The first matching item wins.
    |
    */

    'items' => [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'material-icons',
            'icon_name' => 'dashboard',
            'match' => ['dashboard', 'user_profile', 'user_profile/*'],
        ],
        [
            'key' => 'config',
            'label' => 'Configuration',
            'url' => '/config',
            'icon' => 'fa fa-cogs',
            'match' => ['config', 'config/*'],
        ],
        [
            'key' => 'registry',
            'label' => 'Registry',
            'url' => '/dashuser',
            'icon' => 'fa fa-edit',
            'match' => ['dashuser', 'dashuser/*'],
        ],
        [
            'key' => 'inventory',
            'label' => 'Inventory',
            'url' => '/items',
            'icon' => 'fa fa-archive',
            'match' => ['items', 'items/*'],
        ],
        [
            'key' => 'waybill',
            'label' => 'Waybill',
            'url' => '/waybill',
            'icon' => 'fa fa-truck',
            'match' => [
                'waybill',
                'waybill/*',
                'waybillview',
                'waybillview/*',
                'waybill_dist',
                'waybill_dist/*',
                'waybillprint',
                'waybillprint/*',
                'distribution',
                'distribution/*',
            ],
        ],
        [
            'key' => 'sales',
            'label' => 'Sales',
            'url' => '/sales',
            'icon' => 'fa fa-shopping-basket',
            'match' => ['sales', 'sales/*', 'mpt_cart', 'expenses', 'expenses/*', 'paid_debts', 'paid_debts/*'],
        ],
        [
            'key' => 'report',
            'label' => 'Report',
            'url' => '/reporting',
            'icon' => 'fa fa-file-text',
            'match' => [
                'reporting',
                'reporting/*',
                'saleshistory',
                'saleshistory/*',
                'debts',
                'debts/*',
                'paid_debts',
                'paid_debts/*',
                'returnsreport',
                'returnsreport/*',
                'returns',
                'returns/*',
                'expensereport',
                'expensereport/*',
                'distreport',
                'distreport/*',
                'distreport/export',
                'waybillreport',
                'waybillreport/*',
                'waybillreport/export',
                'reportprinting',
                'reportprinting/*',
                'stockreportprinting',
                'stockreportprinting/*',
                'stockfillprint',
                'stockfillprint/*',
                'expensereportprinting',
                'expensereportprinting/*',
                'returnprint',
                'returnprint/*',
                'debtsreportprinting',
                'debtsreportprinting/*',
                'distreportprint',
                'distreportprint/*',
                'stockbal',
                'stockbal/*',
                'genstockbal',
                'genstockbal/*',
                'stock',
                'stock/*',
            ],
        ],
        [
            'key' => 'closure',
            'label' => 'Closure',
            'url' => '/closure_page',
            'icon' => 'fa fa-calendar',
            'match' => ['closure_page', 'closure_page/*', 'closure', 'closure/*'],
        ],
    ],

];
