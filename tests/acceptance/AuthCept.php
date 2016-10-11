<?php
$I = new AcceptanceTester($scenario);
// Test AUTH related functions and exercise adding/removing and account and then members of the account

$i = rand(1, 9999);
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
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('auth/create', $newAccount);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);

// now load the owner and pull the account in as well
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("owners?email=$email");
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);
$newAccountID = $I->grabDataFromResponseByJsonPath("$.data[0].attributes.['account-id']");

/**
 * add a 2nd owner to this account
 * exercise all aspects of the Owner endpoint
 * since we know we'll always have an account to practice on
 */

$newOwner = [
    'data' => [
        'attributes' => [
            'first_name' => 'Billy',
            'last_name' => 'Johnson',
            'email' => 'some_unique_email@address.com',
            'relationship' => 'Father',
            'primary' => 0,
            'gender' => 'Male'
        ],
        'relationships' => [
            'account' => ['data' => ['id' => $newAccountID[0], 'accounts']]
        ],
        'type' => 'owners'
    ]
];


$I->sendPOST('owners', json_encode($newOwner));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newOwnerID = $I->grabDataFromResponseByJsonPath('$.data.id');

// attempt to edit newly added owner record
$newOwner['data']['attributes']['last_name'] = 'A New Last Name';
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPUT("owners/$newOwnerID[0]", json_encode($newOwner));
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

$newAttendee = [
    'data' => [
        'attributes' => [
            'first_name' => 'William',
            'last_name' => 'Johnson ',
            'dob' => '1999-04-23',
            'relationship' => 'Father',
            'active' => 0,
            'gender' => 'Male',
            'school_grade' => '7th'
        ],
        'relationships' => [
            'account' => ['data' => ['id' => $newAccountID[0], 'accounts']]
        ],
        'type' => 'attendees'
    ]
];


$I->sendPOST('attendees', json_encode($newAttendee));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newAttendeeID = $I->grabDataFromResponseByJsonPath('$.data.id');

// attempt to edit newly added firm record
$newAttendee['gender'] = 'Female';
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPUT("attendees/$newAttendeeID[0]", json_encode($newAttendee));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);

// remove the attendee from the system
$I->sendDELETE('attendees/' . $newAttendeeID[0]);
$I->seeResponseCodeIs(204);

/**
 * clean up account by removing it
 */
// remove a particular account
$I->sendDELETE('accounts/' . $newAccountID[0]);
$I->seeResponseCodeIs(204);