<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test all end points for basic errors');

$endpoints = array(
    'account_addrs',
    'account_billing_summaries',
    // 'account_has_fields',
    'accounts',
    'account_statements',
    // 'attendee_has_fields',
    'attendees',
    'batches',
    'cabins',
    'cards',
    'charges',
    'checks',
    // 'custom_account_fields',
    // 'custom_owner_fields',
    // 'custom_registration_fields',
    'employees',
    'events',
    'fees',
    'fields',
    'locations',
    // 'owner_has_fields',
    'owner_numbers',
    'owners',
    'payment_batches',
    'payments',
    'programs',
    // 'registration_has_fields',
    'registrations',
    'requests',
    'sessions',
    'settings',
    'statement_batches',
    'users'
);

// attempt to login as Owner first
$user = $I->login('Employee');

foreach ($endpoints as $endpoint) {
    $I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
    $I->sendGet("$endpoint?limit=2");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
}

// attempt to logout as Owner
$I->logout($user['attributes']['token']);