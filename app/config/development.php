<?php
error_reporting(E_ALL);

// define security roles
define("PORTAL_USER", "Portal - User");
define("ADMIN_USER", "System - Administrator");

// Override production configs for development environment
// this development environment is configured for docker usage
$environmentConfig = [
    'application' => [
        // where to store cache related files
        'cacheDir' => '/tmp/',
        // FQDN
        'publicUrl' => 'http://localhost:8080',
        // probably the same FQDN
        'corsOrigin' => 'https://localhost:8080',
        // should the api return additional meta data and enable additional server logging?
        'debugApp' => true,
        // where should system temp files go?
        'tempDir' => '/tmp/',
        // where should app generated logs be stored?
        'loggingDir' => '/tmp/',
        // what is the path after the FQDN?
        'baseUri' => '##partialurl##/v1/'
    ],
    // standard database configuration values
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'db',
        'username' => 'api',
        'password' => 'api',
        'dbname' => 'smores',
        'charset' => 'utf8'
    ],
    // enable security for controllers marked as secure?
    'security' => true,

    // if security is false, which user id to impersonate?
    // set to a user account with access to most routes for automated testing
    // demo@smores.camp
    'securityUserId' => 595,
    // admin@smores.camp
    //'securityUserId' => 768,

    // used as a system wide prefix to all file storage paths
    'fileStorage' => [
        'basePath' => '/file_storage/'
    ]
];

// Define APPNAME if this is production environment
// - must be defined on each deployed PRODUCTION version
// useful when the production code is deployed in multiple configurations ie. portal or admin
defined('APPLICATION_NAME') || define('APPLICATION_NAME', 'admin');
