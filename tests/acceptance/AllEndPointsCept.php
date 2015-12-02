<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test all end points for basic errors');

$endpoints = array(
    'account_addrs',
    'accounts',
    'attendees',
    'cabins',
    'cards',
    'charges',
    'checks',
    'employees',
    'events',
    'fees',
    'locations',
    'owners',
    'owner_numbers',
    'payments',
    'registrations',
    'requests',
    'sessions',
    'settings',
    'users'
);

// attempt to login as Owner first
$user = $I->login('Employee');

foreach ($endpoints as $endpoint) {
    $I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['token']);
    $I->sendGet("$endpoint?limit=2");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
}

// attempt to logout as Owner
$I->logout($user['token']);