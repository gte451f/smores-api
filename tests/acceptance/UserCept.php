<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic User operations');

// load a subsect of users
$I->wantTo('load a group of users');
$I->sendGet('/users?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.users[*].first_name');