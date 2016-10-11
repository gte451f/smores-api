<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic Attendee operations like GET/POST/PUT/DELETE from the perspective of an owner');

// attempt to login as Owner first
$user = $I->login('Owner');

// the base attendee record on which we'll modify before attempting various saves
$newRecord = [
    'data' => [
        'attributes' => [
            'first_name' => 'tommy',
            'last_name' => 'twotone',
            'grade' => '5th',
            'active' => true,
            'dob' => '1911-02-10',
            'gender' => 'Female'
        ],
        'relationships' => [
            'account' => ['data' => ['id' => $user->attributes->{'account-id'}, 'accounts']]
        ],
        'type' => 'attendees'
    ]
];

// load specific account
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("attendees/2?include=all");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// verify the api side loads expected data
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.account.data.id');
$I->seeResponseJsonMatchesJsonPath('$.included.[0].id');

// add new attendee record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('attendees', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newAttendeeID = $I->grabDataFromResponseByJsonPath('$.data.id');

// delete the newly created attendee record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('attendees/' . $newAttendeeID[0]);
$I->seeResponseCodeIs(204);




