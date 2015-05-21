<?php
// Ensure production specific settings
// app/config/production.php
$production = [
    'application' => [
        'debugApp' => true
    ],
    'security' => true,
    'database' => [
        'adapter' => 'Mysql',
        'host' => '##localhost##',
        'username' => '##username##',
        'password' => '##password##',
        'dbname' => '##dbname##'
    ],
];

return $production;