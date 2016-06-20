<?php
// define security roles
define("PORTAL_USER", "Portal - User");
define("ADMIN_USER", "System - Administrator");

// override default configs with production
$production = [
    'application' => [
        // where to store cache
        'cacheDir' => '##cacheDir##',
        // FQDN
        'publicUrl' => '##publicurl##',
        // probably the same FQDN
        'corsOrigin' => '##publicurl##',
        // should the api return additional meta data and enable additional server logging?
        'debugApp' => false,
        // where to store ephemeral files
        'tempDir' => '##tempDir##',
        // where to store system logs
        'loggingDir' => '##loggingDir##',
        // what is the path after the FQDN?
        'baseUri' => '##partialurl##/v1/'
    ],
    // enable security for controllers marked as secure?
    'security' => true,
    // standard database configuration values
    'database' => [
        'adapter' => 'Mysql',
        'host' => '##host##',
        'username' => '##username##',
        'password' => '##password##',
        'dbname' => '##dbname##',
        'charset' => 'utf8'
    ],
    // used as a system wide prefix to all file storage paths
    'fileStorage' => [
        'basePath' => '##fileDir##'
    ]
];

// Define APPNAME if this is production environment
// - must be defined on each deployed PRODUCTION version
// useful when the production code is deployed in multiple configurations ie. portal or admin
defined('APPLICATION_NAME') || define('APPLICATION_NAME', 'admin');

// load defined security rules based on current environment
return array_merge_recursive_replace($production, require('security_rules/production.php'));