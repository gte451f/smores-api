<?php
// TODO fix this when updating payment batch logic
$I = new AcceptanceTester($scenario);
// Test Payment Batch CRUD');

$newRecord = [
    'data' => [
        'attributes' => [
            'min_type' => 'Outstanding',
            'min_amount' => 25,
            'selectedAccounts' => [100]
        ],
        'relationships' => [],
        'type' => 'payment_batch'
    ]
];


// attempt to login as Owner first
$user = $I->login('Employee');

//$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
//$I->sendPOST('payment_batches', $payment_batch);
//$I->seeResponseIsJson();
//$I->seeResponseCodeIs(201);
//$newBatchID = $I->grabDataFromResponseByJsonPath('$.payment_batch[0].id');

// load a particular employee
//$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
//$I->sendGet("/payment_batches/{$newBatchID[0]}");
//$I->seeResponseCodeIs(200);
//$I->seeResponseIsJson();
//$I->seeResponseJsonMatchesJsonPath('$.payment_batch[0].id');

// now attempt to remove the batch
// hopefully it will NOT fail, but might if there is a real payment on the batch
//$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
//$I->sendDELETE('payment_batches/' . $newBatchID[0]);
//$I->seeResponseCodeIs(204);