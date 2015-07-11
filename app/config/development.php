<?php
error_reporting(E_ALL);

// Override production configs for development environment
// app/config/development.php

$development = [
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
    // enable security for controllers marked as secure?
    'security' => false
];

return $development;