<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Test AUTH related functions');

// in case you need it
// application/x-www-form-urlencoded
// password_confirm=password01*&email=bbarton123@email.com&first_name=billy&last_name=barton&user_type=Owner&gender=Female&relationship=Mother&password=password01*&number=123-456-7890&primary=1&phone_type=Office&

$i = 8;
$email = $i . 'bbarton@email.com';

$newAccount = array(
    'password_confirm' => 'password01*',
    'email' => $email,
    'first_name' => 'billy',
    'last_name' => 'barton',
    'user_type' => 'Owner',
    'gender' => 'Female',
    'relationship' => 'Mother',
    'user' => '',
    'password' => 'password01*',
    'number' => '123-456-7890',
    'primary' => 1,
    'phone_type' => 'Office'
);

// attempt to login as Owner first
$user = $I->login('Employee');

// create a brand new account
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['token']);
$I->sendPOST('auth/create', $newAccount);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);

// now load the owner account to delete the account
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user['token']}");
$I->sendGet("owners?email=$email");
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);
$newAccountID = $I->grabDataFromResponseByJsonPath('$.owners[0].account_id');

// now remove an account
$I->wantTo('remove a particular account');
$I->sendDELETE('accounts/' . $newAccountID[0]);
$I->seeResponseCodeIs(204);

// attempt to logout as Owner
$I->logout($user['token']);