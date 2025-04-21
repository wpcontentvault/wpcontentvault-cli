<?php

declare(strict_types=1);

use App\Configuration\DatabaseConfiguration;

return [
    'default' => 'sqlite',
    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => null,
            'database' => DatabaseConfiguration::resolveDatabaseFile(env('WPCONTENTVAULT_DATABASE', 'database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => true,
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];
