<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('maybe test some specific GETS but the PUT/POST is done from Registrations');

// attempt to login as Owner first
$user = $I->login('Owner');

// first load all requests
// disable for now
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("/requests");
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);
$registrationID = $I->grabDataFromResponseByJsonPath('$.data[0].attributes');

// for some reason the jsonpath wasn't working as expected, here is some PHP to get the right value
$registrationID = $registrationID[0]['registration-id'];

// now gather just one request with all
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("/requests?registration_id=$registrationID[0]&with=all");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

