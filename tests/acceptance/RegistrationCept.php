<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test basic registration requests');

// this should be a safe user record right?
$attendeeId = 201;

// attempt to login as Owner first
$user = $I->login('Owner');

// load specific account's registrations
$accountId = $user->attributes->{'account-id'};
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("/registrations?attendees:account_id=$accountId&with=all");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

//{"data":{
//    "attributes":{"notes":null,"created-on":null,"updated-on":null},
//    "relationships":{
//        "attendee":{
//            "data":{
//                "type":"attendees","id":"201"
//                }
//        },"type":"registrations"}
//}

// the base registration record which we'll modify as we attempt various saves
$newRegistration = [
    'data' => [
        'attributes' => [
            'notes' => 'Here are some test notes.',
        ],
        'relationships' => [
            'attendee' => ['data' => ['id' => 201, 'type' => 'attendees']]
        ],
        'type' => 'registrations'
    ]
];

// add new registration record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('registrations', json_encode($newRegistration));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
// edit here..but not sure how
$newRegistrationID = $I->grabDataFromResponseByJsonPath('$.data.id');
$newRegistration['data']['attributes']['notes'] = 'Let us modify the notes!';


// the base registration record which we'll modify as we attempt various saves
$newRequest = [
    'data' => [
        'attributes' => [
            'priority' => 1,
            'note' => 'Request specific request'
        ],
        'relationships' => [
            'event' => ['data' => ['id' => 24, 'type' => 'events']],
            'registration' => ['data' => ['id' => $newRegistrationID[0], 'type' => 'registrations']]
        ],
        'type' => 'requests'
    ]
];

// add new request record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('requests', json_encode($newRequest));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newRequestID = $I->grabDataFromResponseByJsonPath('$.data.id');


// delete the newly created request record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('requests/' . $newRequestID[0]);
$I->seeResponseCodeIs(204);


// delete the newly created registration record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('registrations/' . $newRegistrationID[0]);
$I->seeResponseCodeIs(204);

