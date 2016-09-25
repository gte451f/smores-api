<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Event operations');

// attempt to login as Owner first
$user = $I->login('Employee');

// load a subset of locations
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->wantTo('load a group of events');
$I->sendGet('/events?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].attributes.fee');