<?php
error_reporting(E_ALL);

// Override production configs for staging environment
// app/config/staging.php
$staging = [
    'application' => [
        'cacheDir' => '/tmp/cache/',
        'publicUrl' => 'http://localhost:8080/phalcon-json-api/',
        'debugApp' => true
    ],
    // enable security?
    'security' => true,
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'api',
        'password' => 'api',
        'dbname' => 'smores'
    ],
];

return $staging;