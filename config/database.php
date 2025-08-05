<?php

return [
    'default' => 'mysql',
    
    'connections' => [
        'mysql' => [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'database' => $_ENV['DB_DATABASE'] ?? 'spinwin',
            'port' => $_ENV['DB_PORT'] ?? 3306,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
    
    'migrations' => [
        'table' => 'migrations',
        'path' => 'database/migrations',
    ],
    
    'seeders' => [
        'path' => 'database/seeders',
    ],
]; 