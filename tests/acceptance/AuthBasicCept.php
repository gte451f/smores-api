<?php
$I = new AcceptanceTester($scenario);
// test that demo accounts are valid

// attempt to login as Owner first
$user = $I->login('Owner');


// test the new account form since this is where it's hosted from


// the base account record which we will attempt to save
// password_confirm=password1234&email=foo%40bar.com&first_name=Foo&last_name=Bar&user_type=Owner&gender=Female&relationship=Mother&user=&password=password1234&number=789-789-7890&primary=1&phone_type=Other


$newRecord = [
    'password_confirm' => 'password1234',
    'email' => 'foo@barb3.com',
    'first_name' => 'Foo',
    'last_name' => 'Barb3',
    'user_type' => 'Owner',
    'gender' => 'Female',
    'relationship' => 'Mother',
    'user' => '',
    'password' => 'password1234',
    'number' => '789-789-7890',
    'primary' => '1',
    'phone_type' => 'Other'
];

// add a new user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('auth/create', $newRecord);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRecordID = $I->grabDataFromResponseByJsonPath('$.account_id');


// delete the newly created user record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('accounts/' . $newRecordID[0]);
$I->seeResponseCodeIs(204);