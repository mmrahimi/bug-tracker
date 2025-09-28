<?php

return [
    'pdo' => [
        'driver' => 'mysql',
        'host' => $_ENV['DB_HOST'],
        'database' => $_ENV['DB_NAME'],
        'username' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASS'],
    ],

    'pdo_testing' => [
        'driver' => 'mysql',
        'host' => $_ENV['TEST_DB_HOST'],
        'database' => $_ENV['TEST_DB_NAME'],
        'username' => $_ENV['TEST_DB_USER'],
        'password' => $_ENV['TEST_DB_PASS'],
    ]
];
