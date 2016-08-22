<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic registration requests');

// attempt to login as Owner first
$user = $I->login('Owner');

// load specific account
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("/registrations?attendees:account_id=$accountId&with=all");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
