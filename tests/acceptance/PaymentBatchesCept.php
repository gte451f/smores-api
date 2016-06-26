<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Test Payment Batch CRUD');

$payment_batch = '{"payment_batch":
                    {
                    "min_type":"Outstanding",
                    "min_amount":"25",
                    "selectedAccounts":["100"]
                    }
                  }';

// attempt to login as Owner first
$user = $I->login('Employee');

$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendPOST('payment_batches', $payment_batch);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newBatchID = $I->grabDataFromResponseByJsonPath('$.payment_batch[0].id');

// load a particular employee
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet("/payment_batches/{$newBatchID[0]}");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.payment_batch[0].id');

// now attempt to remove the batch
// hopefully it will NOT fail, but might if there is a real payment on the batch
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendDELETE('payment_batches/' . $newBatchID[0]);
$I->seeResponseCodeIs(204);

// attempt to logout as Owner
$I->logout($user['attributes']['token']);