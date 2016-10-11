<?php

/**
 * test common payment operations such as
 * - a one time credit payment
 * - a check payment
 *
 */

$I = new AcceptanceTester($scenario);
// test basic Card operations like GET/POST from the perspective of an owner

// attempt to login as Owner first
$user = $I->login('Owner');

// the base card record & payment POST
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
            'cvc' => 123,
            'amount' => 11,
            'mode' => 'Credit',
            'zip' => '12345',
            'address' => '123 Memory Lane'
        ],
        'relationships' => [
            'account' => ['data' => ['id' => $user->attributes->{'account-id'}, 'accounts']]
        ],
        'type' => 'payments'
    ]
];
// add new one time payment record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('payments', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newPaymentID = $I->grabDataFromResponseByJsonPath('$.data.id');


// load specific payment
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("payments/{$newPaymentID[0]}?include=accounts");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// verify the api side loads expected data
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.account.data.id');
$I->seeResponseJsonMatchesJsonPath('$.included.[0].id');


// attempt to save a check payment
//$newRecord = [
//    'data' => [
//        'attributes' => [
//            'amount' => 10,
//            'mode' => 'Credit'
//        ],
//        'relationships' => [
//            'account' => ['data' => ['id' => $user->attributes->{'account-id'}, 'accounts']],
//            'card' => ['data' => ['id' => $newPaymentID[0], 'cards']],
//            'check' => ['data' => null],
//            'payment-batch' => ['data' => null]
//        ],
//        'type' => 'payments'
//    ]
//];
//
//// add new payment to an existing card
//$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
//$I->sendPOST('payments', json_encode($newRecord));
//$I->seeResponseIsJson();
//$I->seeResponseCodeIs(201);
//$newPaymentID = $I->grabDataFromResponseByJsonPath('$.data.id');


// don't forget to push a one time charge or card+payment
// {"data":{"attributes":{"amount":"78","mode":"Credit","cvc":"897","name_on_card":"lkjlkjlkjl jlkjlkj","expiration_month":3,"expiration_year":2019,"number":"4012888888881881","vendor":"visa","address":"123 strange","zip":"12345"},"relationships":{"account":{"type":"accounts","id":"1"}},"type":"payments"}}


// now attempt to refund the payment
// TODO... hold off since it doesn't look like refunds are implemented in the system yet
