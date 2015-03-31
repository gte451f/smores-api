<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Location operations');

// load a subsect of locations
$I->wantTo('load a group of locations');
$I->sendGet('/locations?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.locations[*].name');