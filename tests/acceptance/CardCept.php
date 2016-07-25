<?php

/**
 * test common credit card related operations such as
 * - create a new credit card (and indirectly test the remote API)
 * - test creating a payment based on an existing card
 *
 */

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


// while we are here submit a payment with an existing card
// the base card record on which we'll modify before attempting various saves

//{"data":{"attributes":{"amount":10,"mode":"Credit"},
//"relationships":{
//        "account":{"data":{"type":"accounts","id":"1"}},
// "card":{"data":{"type":"cards","id":"30"}},
//  "check":{"data":null},
//  "payment-batch":{"data":null}
//  },
//"type":"payments"}}

$newRecord = [
    'data' => [
        'attributes' => [
            'amount' => 10,
            'mode' => 'Credit'
        ],
        'relationships' => [
            'account' => ['data' => ['id' => $user->attributes->{'account-id'}, 'accounts']],
            'card' => ['data' => ['id' => $newCardID[0], 'cards']],
            'check' => ['data' => null],
            'payment-batch' => ['data' => null]
        ],
        'type' => 'payments'
    ]
];

// add new payment to an existing card
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('payments', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newPaymentID = $I->grabDataFromResponseByJsonPath('$.data.id');


// now attempt to refund the payment
// TODO... hold off since it doesn't look like refunds are implemented in the system yet


// delete the newly created card record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('cards/' . $newCardID[0]);
$I->seeResponseCodeIs(204);




