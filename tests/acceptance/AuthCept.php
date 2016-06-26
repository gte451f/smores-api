<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Test AUTH related functions and exercise adding/removing members of the account');

// in case you need it
// application/x-www-form-urlencoded
// password_confirm=password01*&email=bbarton123@email.com&first_name=billy&last_name=barton&user_type=Owner&gender=Female&relationship=Mother&password=password01*&number=123-456-7890&primary=1&phone_type=Office&

$i = 8;
$email = $i . 'bbarton@email.com';

$newAccount = [
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
];

// attempt to login as Owner first
$user = $I->login('Employee');

// create a brand new account
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendPOST('auth/create', $newAccount);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);

// now load the owner and pull the account in as well
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user['attributes']['token']}");
$I->sendGet("owners?email=$email");
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);
$newAccountID = $I->grabDataFromResponseByJsonPath("$.data[0].attributes.['account-id']");

/**
 * add a 2nd owner to this account
 * exercise all aspects of the Owner endpoint
 * since we know we'll always have an account to practice on
 */

$owner = [
    'first_name' => 'Billy',
    'last_name' => 'Johnson',
    'email' => 'some_unique_email@address.com',
    'relationship' => 'Father',
    'primary' => 0,
    'gender' => 'Male',
    'account_id' => $newAccountID[0]
];


//{"data":{"attributes":{
//    "primary-contact":null,
//    "relationship":"Mother",
//    "email":"foo@smith.com",
//    "last-name":"Last",
//    "first-name":"First",
//    "user-name":null,
//    "user-type":"Owner",
//    "gender":"Female",
//    "foobar":null},
//    "relationships":{"account":{"data":{"type":"accounts","id":"95"}}},"type":"owners"}
//}

$I->sendPOST('owners', json_encode([
    'owner' => $owner
]));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newOwnerID = $I->grabDataFromResponseByJsonPath('$.data[0].id');

// attempt to edit newly added firm record
$owner['last_name'] = 'A New Last Name';
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendPUT("owners/$newOwnerID[0]", json_encode([
    'owner' => $owner
]));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);

// remove the owner from the system
$I->sendDELETE('owners/' . $newOwnerID[0]);
$I->seeResponseCodeIs(204);

/**
 * add a 2nd attendee to this account
 * exercise all aspects of the Owner endpoint
 * since we know we'll always have an account to practice on
 */

$attendee = [
    'first_name' => 'William',
    'last_name' => 'Johnson ',
    'dob' => '1999-04-23',
    'relationship' => 'Father',
    'active' => 0,
    'gender' => 'Male',
    'account_id' => $newAccountID[0],
    'school_grade' => '7th'
];

$I->sendPOST('attendees', json_encode([
    'attendee' => $attendee
]));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newAttendeeID = $I->grabDataFromResponseByJsonPath('$.data[0].id');

// attempt to edit newly added firm record
$attendee['gender'] = 'Female';
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendPUT("attendees/$newAttendeeID[0]", json_encode([
    'attendee' => $attendee
]));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);

// remove the attendee from the system
$I->sendDELETE('attendees/' . $newAttendeeID[0]);
$I->seeResponseCodeIs(204);

/**
 * clean up account by removing it
 */
$I->wantTo('remove a particular account');
$I->sendDELETE('accounts/' . $newAccountID[0]);
$I->seeResponseCodeIs(204);

// attempt to logout as Owner
$I->logout($user['attributes']['token']);