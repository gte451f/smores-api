<?php
$I = new AcceptanceTester($scenario);
//test basic CRUD operations on the AccountAddr end point

// attempt to login as Owner first
$user = $I->login('Owner');

// pull a specific account record
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("account_addrs?account_id=$accountId&include=all");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// verify the api side loads expected data
$I->seeResponseJsonMatchesJsonPath('$.data.[0].relationships.account.data.id');
// look for at least one related record
$I->seeResponseJsonMatchesJsonPath('$.included.[0]');

// create new account address as an owner
// the base account address record which we will attempt to save
$newRecord = [
    'data' => [
        'attributes' => [
            'billing' => 1,
            'mailing' => 1,
            'addr_1' => '123 Memory Lane',
            'addr_2' => 'Suite 500',
            'city' => 'Tucker',
            'state' => ' Georgia',
            'country' => 'United States',
            'zip' => '30084'
        ],
        'relationships' => [
            'account' => ['data' => ['id' => $user->attributes->{'account-id'}, 'accounts']]
        ],
        'type' => 'account_addrs'
    ]
];

//add a new account address record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('account_addrs', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRecordID = $I->grabDataFromResponseByJsonPath('$.data.id');


//delete the newly created user record'
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('account_addrs/' . $newRecordID[0]);
$I->seeResponseCodeIs(204);