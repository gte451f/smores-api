<?php
error_reporting(E_ALL);

// define security roles
define("PORTAL_USER", "Portal - User");
define("ADMIN_USER", "System - Administrator");

// Override default configs for staging environment
$staging = [
    'application' => [
        // where to store cache related files
        'cacheDir' => '/tmp/',
        // FQDN
        'publicUrl' => 'http://smores.dev:8080',
        // probalby the same FQDN
        'corsOrigin' => 'https://smores.dev:8080',
        // should the api return additional meta data and enable additional server loggin?
        'debugApp' => true,
        // where should system temp files go?
        'tempDir' => '/tmp/',
        // where should app generated logs be stored?
        'loggingDir' => '/tmp/'
    ],
    // enable security for controllers marked as secure?
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
    // used as a system wide prefix to all file storage paths
    'fileStorage' => [
        'basePath' => '/tmp/'
    ]
];

// load defined security rules based on current environment
return array_merge_recursive_replace($staging, require('security_rules/staging.php'));