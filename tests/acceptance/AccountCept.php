<?php
$I = new AcceptanceTester($scenario);
// test basic CRUD operations on the Account end point

// attempt to login as Owner first
$user = $I->login('Owner');

// pull a specific account record'
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("accounts/$accountId?include=all");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// verify the api side loads expected data
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.owners.data.[0]');
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.attendees.data.[0]');
$I->seeResponseJsonMatchesJsonPath('$.included.[0]');


// attempt to login as Admin
$user = $I->login('Employee');

// load accounts filtered by name
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("accounts/?name=*mad*&include=all");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// verify the api side loads expected data
$I->seeResponseJsonMatchesJsonPath('$.data.[0].relationships.owners.data.[0]');
$I->seeResponseJsonMatchesJsonPath('$.data.[0].relationships.attendees.data.[0]');
$I->seeResponseJsonMatchesJsonPath('$.included.[0]');

// create new account as an employee

// the base account record which we will attempt to save
$newRecord = [
    'data' => [
        'attributes' => [
            'name' => 'The Johnson Family',
            'notes' => 'test family'
        ],
        'type' => 'accounts'
    ]
];

// add a new user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('accounts', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRecordID = $I->grabDataFromResponseByJsonPath('$.data.id');


// delete the newly created user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('accounts/' . $newRecordID[0]);
$I->seeResponseCodeIs(204);