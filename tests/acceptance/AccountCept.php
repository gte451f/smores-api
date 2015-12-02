<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Account operations');

// attempt to login as Owner first
$user = $I->login('Employee');

// load a subsect of accounts
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['token']);
$I->sendGet('/accounts?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.accounts[*].created_on');

// attempt to logout as Owner
$I->logout($user['token']);