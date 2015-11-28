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
            'System - Administrator' => [
                'cabins'
            ],
            'Portal - User' => [
                'cabins'
            ]
        ],
        
        'write' => [
            'Portal - User' => [
                'cabins'
            ],
            'System - Administrator' => [
                'cabins'
            ]
        ],
        
        'delete' => [
            'Portal - User' => [
                'cabins'
            ],
            'System - Administrator' => [
                'cabins'
            ]
        ]
    ]
];

return $security_rules;