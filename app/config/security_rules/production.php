<?php
/**
 * This array defines rules which will be applied in the SecurityService class.
 * 
 * They are arranged in different arrays based on rule type (read, write, delete]
 * 
 * Structure is as follows: 
 * ACTION => [ 
 *     GROUP NAME => [ 
 *         END POINT 1, 
 *         END POINT 2 
 *     ]
 * ] 
 * 
 * 
 */
$security_rules = [
    'security_rules' => [
        'read' => [
            ADMIN_USER => [
                'cabins'
            ],
            PORTAL_USER => [
                'cabins'
            ]
        ],
        
        'write' => [
            PORTAL_USER => [
                'cabins'
            ],
            ADMIN_USER => [
                'cabins'
            ]
        ],
        
        'delete' => [
            PORTAL_USER => [
                'cabins'
            ],
            ADMIN_USER => [
                'cabins'
            ]
        ]
    ]
];

return $security_rules;