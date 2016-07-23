<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Card operations like GET/POST/PUT/DELETE from the perspective of an owner');

// attempt to login as Owner first
$user = $I->login('Owner');

// the base card record on which we'll modify before attempting various saves
$newRecord = [
    'data' => [
        'attributes' => [
            'allow_reocurring' => false,
            'is_debit' => false,
            'number' => '4242424242424242',
            'vendor' => 'visa',
            'expiration_month' => '12',
            'expiration_year' => '2020',
            'active' => 1,
            'name-on-card' => 'Test Name',
            'cvc' => 123
        ],
        'relationships' => [
            'account' => ['data' => ['id' => $user->attributes->{'account-id'}, 'accounts']]
        ],
        'type' => 'cards'
    ]
];
// add new card record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('cards', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newCardID = $I->grabDataFromResponseByJsonPath('$.data.id');


// load specific account
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("cards/{$newCardID[0]}?include=accounts");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// verify the api side loads expected data
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.account.data.id');
$I->seeResponseJsonMatchesJsonPath('$.included.[0].id');


// delete the newly created card record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('cards/' . $newCardID[0]);
$I->seeResponseCodeIs(204);




