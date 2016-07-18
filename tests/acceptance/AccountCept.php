<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Account operations');

// attempt to login as Owner first
$user = $I->login('Owner');

// load specific account
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("accounts/$accountId?include=owners,attendees");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// verify the api side loads expected data
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.owners.data.[0]');
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.attendees.data.[0]');
$I->seeResponseJsonMatchesJsonPath('$.included.[0]');