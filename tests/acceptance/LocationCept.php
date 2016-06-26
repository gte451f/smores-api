<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Location operations');

// attempt to login as Owner first
$user = $I->login('Employee');

// load a subsect of locations
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->wantTo('load a group of locations');
$I->sendGet('/locations?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.locations[*].name');

// attempt to logout as Owner
$I->logout($user['attributes']['token']);