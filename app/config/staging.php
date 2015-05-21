<?php
// Override production configs for staging environment
// app/config/staging.php
$staging = [
    'application' => [
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
];

return $staging;