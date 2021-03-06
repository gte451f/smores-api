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
            PORTAL_USER => [
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
                "users",
                "fields"
            ],
            ADMIN_USER => [
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
                "users",
                "payment_batches",
                "statement_batches",
                "account_billing_summaries",
                "fields"
            ]
        ],

        'write' => [
            PORTAL_USER => [
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
            ADMIN_USER => [
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
                "users",
                "payment_batches",
                "statement_batches",
                "fields"
            ]
        ],

        'delete' => [
            PORTAL_USER => [
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
            ADMIN_USER => [
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
                "users",
                "payment_batches",
                "statement_batches",
                "fields"
            ]
        ]
    ]
];

return $security_rules;