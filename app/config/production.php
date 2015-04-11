<?php
// Ensure production specific settings
// app/config/production.php
$staging = [
    'application' => [
        'debugApp' => true
    ],
    'security' => true
];

return $staging;