<?php
$I = new AcceptanceTester($scenario);
// test basic Event operations

// attempt to login as Owner first
$user = $I->login('Employee');

// load a subset of locations
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
// load a group of events
$I->sendGet('/events?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].attributes.fee');

// the base account record which we will attempt to save
$newRecord = [
    'data' => [
        'attributes' => [
            'program_id' => 1,
            'location_id' => 1,
            'session_id' => 1,
            'cabin_id' => 1,
            'fee' => 34,
            'fee_description' => 'This is a sample event created through testing.'
        ],
        'type' => 'events'
    ]
];

// add a new user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('events', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRecordID = $I->grabDataFromResponseByJsonPath('$.data.id');


// delete the newly created user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('events/' . $newRecordID[0]);
$I->seeResponseCodeIs(204);
