<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('verify the end points an owner should have access to and should NOT have access to');

$user = $I->login('Owner');

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
    "users"
];

$deniedEndpoints = [
    'settings'
];

foreach ($allowedEndpoints as $endpoint) {
    $I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
    $I->sendGet("$endpoint?limit=2");
    $I->seeResponseIsJson();
    $I->seeResponseCodeIs(200);
}

foreach ($deniedEndpoints as $endpoint) {
    $I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
    $I->sendGet("$endpoint?limit=2");
    $I->seeResponseIsJson();
    $I->seeResponseCodeIs(404);
}