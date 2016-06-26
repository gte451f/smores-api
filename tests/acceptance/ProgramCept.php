<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Program operations');

// attempt to login as Owner first
$user = $I->login('Employee');

// load a subsect of programs
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->wantTo('load a group of programs');
$I->sendGet('/programs?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.programs[*].name');

// attempt to logout as Owner
$I->logout($user['attributes']['token']);