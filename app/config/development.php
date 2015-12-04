<?php
error_reporting(E_ALL);

// Override production configs for development environment
// app/config/development.php

$development = [
    'application' => [
        // where to store cache related files
        'cacheDir' => '/tmp/',
        // FQDN
        'publicUrl' => 'http://smores.dev:8080',
        // probalby the same FQDN
        'corsOrigin' => 'https://smores.dev:8080',
        // should the api return additional meta data and enable additional server logging?
        'debugApp' => true,
        // where should system temp files go?
        'tempDir' => '/tmp/',
        // where should app generated logs be stored?
        'loggingDir' => '/tmp/'
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
    'security' => false,
    
    // if secuirty is false, which user id to impersonate?
    // set to a user account with access to most routes for automated testing
    // owner access
    'securityUserId' => 595,
    // employee access
    // 'securityUserId' => 768,
    
    // used as a system wide prefix to all file storage paths
    'fileStorage' => [
        'basePath' => '/tmp/'
    ]
];

return $development;