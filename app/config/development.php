<?php
error_reporting(E_ALL);

// Override production configs for development environment
// app/config/development.php

$development = [
    'application' => [
        // where to store temporary files
        'cacheDir' => '/tmp/',
        // FQDN
        'publicUrl' => 'http://smores.dev:8080',
        // probalby the same FQDN
        'corsOrigin' => 'https://smores.dev:8080',
        // should the api return additional meta data and enable additional server loggin?
        'debugApp' => true
    ],
    // standard database configuration values
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'api',
        'password' => 'api',
        'dbname' => 'smores',
        'charset' => 'utf8'
    ],    
    // enable security for controllers marked as secure?
    'security' => true,    
    // used as a system wide prefix to all file storage paths
    'fileStorage' => [
        'basePath' => '/tmp/'
    ]
];

return $development;