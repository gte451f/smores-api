<?php
$I = new AcceptanceTester($scenario);
// test basic Setting operations

// attempt to login as Owner first
$user = $I->login('Employee');

// load a subset of locations
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
// load a group of settings
$I->sendGet('/settings?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].attributes.name');

// the base account record which we will attempt to save
$newRecord = [
    'data' => [
        'attributes' => [
            'name' => 'sample name',
            'value' => 'sample value'
        ],
        'type' => 'settings'
    ]
];

// add a new user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('settings', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRecordID = $I->grabDataFromResponseByJsonPath('$.data.id');

// now trip validation error
$newRecord['data']['attributes']['name'] = '';
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPUT("settings/$newRecordID[0]", json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(422);

// delete the newly created user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('settings/' . $newRecordID[0]);
$I->seeResponseCodeIs(204);
