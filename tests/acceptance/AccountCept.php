<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Account operations');

// attempt to login as Owner first
$user = $I->login('Owner');

// attempt to logout as Owner
$I->logout($user['token']);