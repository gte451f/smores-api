<?php
$I = new AcceptanceTester($scenario);
// test that demo accounts are valid

// attempt to login as Owner first
$user = $I->login('Owner');