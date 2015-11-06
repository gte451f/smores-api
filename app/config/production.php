<?php
// Ensure production specific settings
// app/config/production.php
$production = [
    'application' => [
        // where to store temporary files
        'cacheDir' => '##cacheDir##',
        // FQDN
        'publicUrl' => '##publicurl##',
        // probalby the same FQDN
        'corsOrigin' => '##publicurl##',
        // should the api return additional meta data and enable additional server logging?
        'debugApp' => false
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

return $production;