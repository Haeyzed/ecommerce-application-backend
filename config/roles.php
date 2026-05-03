<?php

return [
    'guards' => ['web', 'sanctum'],

    'central' => [
        'super-admin' => ['*'],
        'admin'       => [
            'tenants.*', 'plans.*', 'subscriptions.*', 'invoices.*',
            'audit-logs.view', 'users.*', 'domains.*',
        ],
        'support'     => ['tenants.view', 'subscriptions.view', 'audit-logs.view'],
    ],

    'tenant' => [
        'owner'    => ['*'],
        'manager'  => [
            'products.*', 'categories.*', 'orders.*', 'customers.*',
            'inventory.*', 'reports.view', 'employees.*', 'leaves.*',
            'payroll.*', 'departments.*', 'positions.*',
        ],
        'admin'    => ['products.view', 'orders.view', 'orders.update', 'inventory.view'],
    ],
];
