<?php
$I = new AcceptanceTester($scenario);
// test basic Location operations

// attempt to login as Owner first
$user = $I->login('Employee');

// load a subset of locations
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
// load a group of locations');
$I->sendGet('/locations?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].attributes.name');

// the base account record which we will attempt to save
$newRecord = [
    'data' => [
        'attributes' => [
            'addr_1' => '123 Memory Lane',
            'addr_2' => 'Suite 102',
            'city' => 'Gotham',
            'zip' => '21548',
            'name' => 'Special Location',
            'description' => 'Special Description'
        ],
        'type' => 'locations'
    ]
];

// add a new user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('locations', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRecordID = $I->grabDataFromResponseByJsonPath('$.data.id');


// delete the newly created user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('locations/' . $newRecordID[0]);
$I->seeResponseCodeIs(204);