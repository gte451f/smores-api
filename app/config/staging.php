<?php
// Override production configs for staging environment
// app/config/staging.php
$staging = [
    'application' => [
        'debugApp' => true
    ],
    // enable security?
    'security' => true
];

return $staging;