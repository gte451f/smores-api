<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('verify the end points an employee should have access to and should NOT have access to');

$user = $I->login('Employee');

$allowedEndpoints = [
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
    'settings'
];

$deniedEndpoints = [];

foreach ($allowedEndpoints as $endpoint) {
    $I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user['token']}");
    $I->sendGet("$endpoint?limit=2");
    $I->seeResponseIsJson();
    $I->seeResponseCodeIs(200);
}

foreach ($deniedEndpoints as $endpoint) {
    $I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user['token']}");
    $I->sendGet("$endpoint?limit=2");
    $I->seeResponseIsJson();
    $I->seeResponseCodeIs(404);
}

$I->logout($user['token']);