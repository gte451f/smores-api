<?php
$I = new AcceptanceTester($scenario);
// test basic CRUD operations on the User end point

// attempt to login as Owner first
$user = $I->login('Employee');

// load a sub set of users
// pull a sub set of user records
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
// load a group of users
$I->sendGet('/users?page=1&per_page=2&with=all&page=2');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].attributes.email');


// the base owner record on which we'll modify before attempting various saves
$newRecord = [
    'data' => [
        'attributes' => [
            'first_name' => 'mary',
            'last_name' => 'jane',
            'email' => 'unique@email99.com',
            'relationship' => 'Mother',
            'dob' => '1911-02-10',
            'gender' => 'Female',
            'user_type' => 'Owner'
        ],
        'type' => 'users'
    ]
];

// add a new user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('users', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRecordID = $I->grabDataFromResponseByJsonPath('$.data.id');


// delete the newly created user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('users/' . $newRecordID[0]);
$I->seeResponseCodeIs(204);