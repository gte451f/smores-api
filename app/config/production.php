<?php
// Ensure production specific settings
// app/config/production.php
$production = [
    'application' => [
        'cacheDir' => '##/tmp/##',
        // FQDN
        'publicUrl' => '##publicurl##',
        // probalby the same FQDN
        'corsOrigin' => '##publicurl##',
        // should the api return additional meta data and enable additional server logging?
        'debugApp' => true
    ],
    
    // enable security for controllers marked as secure?
    'security' => true,
    
    'database' => [
        'adapter' => 'Mysql',
        'host' => '##host##',
        'username' => '##username##',
        'password' => '##password##',
        'dbname' => '##dbname##',
        'charset' => 'utf8'
    ]
];

return $production;