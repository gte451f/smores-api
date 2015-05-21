<?php
// Ensure production specific settings
// app/config/production.php
$production = [
    'application' => [
        'cacheDir' => '/tmp/cache/',
        'publicUrl' => '##publicurl##',
        'debugApp' => false
    ],
    'security' => true,
    'database' => [
        'adapter' => 'Mysql',
        'host' => '##host##',
        'username' => '##username##',
        'password' => '##password##',
        'dbname' => '##dbname##'
    ],
];

return $production;