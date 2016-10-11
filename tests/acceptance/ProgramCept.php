<?php
$I = new AcceptanceTester($scenario);
// test basic Program operations

// attempt to login as Owner first
$user = $I->login('Employee');

// load a subset of programs
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
// load a group of programs
$I->sendGet('/programs?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].attributes.name');


// the base account record which we will attempt to save
$newRecord = [
    'data' => [
        'attributes' => [
            'name' => 'Test Program',
            'description' => 'Test Program Description',
            'fee' => 23
        ],
        'type' => 'programs'
    ]
];

// add a new user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('programs', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRecordID = $I->grabDataFromResponseByJsonPath('$.data.id');


// delete the newly created user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('programs/' . $newRecordID[0]);
$I->seeResponseCodeIs(204);