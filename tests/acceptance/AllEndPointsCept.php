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

foreach ($endpoints as $endpoint) {
    $I->sendGet("$endpoint?limit=2");
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
}