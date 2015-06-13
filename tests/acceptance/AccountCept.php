<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Account operations');

// load a subsect of accounts
$I->wantTo('load a group of accounts');
$I->sendGet('/accounts?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.accounts[*].user_name');