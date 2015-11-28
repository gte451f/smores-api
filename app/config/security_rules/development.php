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
            'Portal - User' => [
                "account_addrs",
                "accounts",
                "attendees",
                "cabins",
                "cards",
                "charges",
                "checks",
                "employees",
                "events",
                "fees",
                "locations",
                "owner_numbers",
                "owners",
                "payments",
                "programs",
                "registrations",
                "requests",
                "sessions",
                "users"
            ],
            'System - Administrator' => [
                "account_addrs",
                "accounts",
                "attendees",
                "cabins",
                "cards",
                "charges",
                "checks",
                "employees",
                "events",
                "fees",
                "locations",
                "owner_numbers",
                "owners",
                "payments",
                "programs",
                "registrations",
                "requests",
                "sessions",
                "settings",
                "users"
            ]
        ],
        
        'write' => [
            'Portal - User' => [
                "account_addrs",
                "accounts",
                "attendees",
                "cards",
                "charges",
                "owner_numbers",
                "owners",
                "payments",
                "registrations",
                "requests"
            ],
            'System - Administrator' => [
                "account_addrs",
                "accounts",
                "attendees",
                "cabins",
                "cards",
                "charges",
                "checks",
                "employees",
                "events",
                "fees",
                "locations",
                "owner_numbers",
                "owners",
                "payments",
                "programs",
                "registrations",
                "requests",
                "sessions",
                "settings",
                "users"
            ]
        ],
        
        'delete' => [
            'Portal - User' => [
                "account_addrs",
                "accounts",
                "attendees",
                "cards",
                "charges",
                "owner_numbers",
                "owners",
                "payments",
                "registrations",
                "requests"
            ],
            'System - Administrator' => [
                "account_addrs",
                "accounts",
                "attendees",
                "cabins",
                "cards",
                "charges",
                "checks",
                "employees",
                "events",
                "fees",
                "locations",
                "owner_numbers",
                "owners",
                "payments",
                "programs",
                "registrations",
                "requests",
                "sessions",
                "settings",
                "users"
            ]
        ]
    ]
];

return $security_rules;