<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Program operations');

// load a subsect of programs
$I->wantTo('load a group of programs');
$I->sendGet('/programs?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.programs[*].name');