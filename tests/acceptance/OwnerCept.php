<?php
$I = new AcceptanceTester($scenario);
// test basic Owner operations like GET/POST/PUT/DELETE

// attempt to login as Owner first
$user = $I->login('Owner');

// the base owner record on which we'll modify before attempting various saves
$newRecord = [
    'data' => [
        'attributes' => [
            'first_name' => 'mary',
            'last_name' => 'jane',
            'email' => 'unique@email.com',
            'relationship' => 'Mother',
            'primary-contact' => 1,
            'dob' => '1911-02-10',
            'gender' => 'Female'
        ],
        'relationships' => [
            'account' => ['data' => ['id' => $user->attributes->{'account-id'}, 'accounts']]
        ],
        'type' => 'owners'
    ]
];

// load specific account
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("owners/1?include=all");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// verify the api side loads expected data
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.account.data.id');
$I->seeResponseJsonMatchesJsonPath('$.included.[0].id');

// add new owner record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('owners', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newOwnerID = $I->grabDataFromResponseByJsonPath('$.data.id');


// now trip validation error
$newRecord['data']['attributes']['first_name'] = '';
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPUT("owners/$newOwnerID[0]", json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(422);


// add a phone number since it's so closely tied to an owner
// the base owner record on which we'll modify before attempting various saves
$newRecord = [
    'data' => [
        'attributes' => [
            'number' => '456-789-7890',
            'phone-type' => 'Mobile',
            'primary' => 1,
        ],
        'relationships' => [
            'owner' => ['data' => ['id' => $newOwnerID[0], 'owners']]
        ],
        'type' => 'owner-numbers'
    ]
];

// add new owner phone record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('owner_numbers', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newPhoneID = $I->grabDataFromResponseByJsonPath('$.data.id');

// delete the newly created owner record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('owner_numbers/' . $newPhoneID[0]);
$I->seeResponseCodeIs(204);

// delete the newly created owner record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('owners/' . $newOwnerID[0]);
$I->seeResponseCodeIs(204);




