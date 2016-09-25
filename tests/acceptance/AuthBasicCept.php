<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test that demo accounts are valid');

// attempt to login as Owner first
$user = $I->login('Owner');