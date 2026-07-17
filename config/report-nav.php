<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Report section sub-navigation
    |--------------------------------------------------------------------------
    |
    | Tabs shown across /reporting and related report pages.
    | The first matching pattern wins when resolving the active tab.
    |
    */

    'items' => [
        [
            'key' => 'sales',
            'label' => 'Sales',
            'description' => 'General sales report',
            'url' => '/reporting',
            'icon' => 'fa fa-shopping-basket',
            'match' => ['reporting', 'reporting/*', 'saleshistory', 'saleshistory/*'],
        ],
        [
            'key' => 'stock',
            'label' => 'Stock',
            'description' => 'General stock balances',
            'url' => '/stockbal',
            'icon' => 'fa fa-bar-chart',
            'match' => ['stockbal', 'stockbal/*', 'genstockbal', 'genstockbal/*', 'branchtransfers', 'branchtransfers/*'],
        ],
        [
            'key' => 'expenses',
            'label' => 'Expenses',
            'description' => 'General expenses report',
            'url' => '/expensereport',
            'icon' => 'fa fa-suitcase',
            'match' => ['expensereport', 'expensereport/*'],
        ],
        [
            'key' => 'debts',
            'label' => 'Debts',
            'description' => 'Debts (Post Payments)',
            'url' => '/debts',
            'icon' => 'fa fa-folder-open',
            'match' => ['debts', 'debts/*'],
        ],
        [
            'key' => 'waybill',
            'label' => 'Waybill',
            'description' => 'Waybill report',
            'url' => '/waybillreport',
            'icon' => 'fa fa-truck',
            'match' => ['waybillreport', 'waybillreport/*'],
        ],
        [
            'key' => 'returns',
            'label' => 'Returns',
            'description' => 'Returns report',
            'url' => '/returnsreport',
            'icon' => 'fa fa-warning',
            'match' => ['returnsreport', 'returnsreport/*', 'returns', 'returns/*'],
        ],
        [
            'key' => 'distribution',
            'label' => 'Distribution',
            'description' => 'Distribution report',
            'url' => '/distreport',
            'icon' => 'fa fa-share-alt',
            'match' => ['distreport', 'distreport/*', 'distreportprint', 'distreportprint/*'],
        ],
    ],

];
