<?php
error_reporting(E_ALL);

// Override production configs for development environment
// app/config/development.php

$development = [
    'application' => [
        'cacheDir' => '/tmp/',
        // FQDN
        'publicUrl' => 'http://localhost:8080',
        // probalby the same FQDN
        'corsOrigin' => 'https://localhost:8080',
        // should the api return additional meta data and enable additional server loggin?
        'debugApp' => true
    ],
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'api',
        'password' => 'api',
        'dbname' => 'smores',
        'charset' => 'utf8'
    ],
    // enable security for controllers marked as secure?
    'security' => false
];

return $development;