<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test Field CRUD operations');

// attempt to login as Employee first
$user = $I->login('Employee');

// {"field":{"name":null,"display":"Color","input":"select","table":"attendees","allowed_data":"string","possible_values":"Blue, Green, Yellow","private":1}}
// the base registration record which we'll modify as we attempt various saves
$newField = [
    'data' => [
        'attributes' => [
            'name' => 'foobar' . rand(1, 9999),
            'display' => 'foobar' . rand(1, 9999),
            'input' => 'select',
            'table' => 'attendees',
            'allowed_data' => 'string',
            'possible_values' => 'Blue, Green, Yellow',
            'private' => 1
        ],
        'relationships' => [],
        'type' => 'fields'
    ]
];


$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('fields', json_encode($newField));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newFieldID = $I->grabDataFromResponseByJsonPath('$.data.id');

// ask for the newly created field
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('fields/' . $newFieldID[0]);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data.id');

// attempt to edit newly added field record
$newField['attributes']['display'] = 'Just Color';
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPUT("fields/$newFieldID[0]", json_encode($newField));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);

// test basic validation rules
$newField['attributes']['table'] = 'asdfasdfasdf';
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('fields', json_encode($newField));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(422);

// move this to just before delete so there is at least one record to display
// load a group of fields that side load everything
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/fields?limit=2&with=all');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');

// now remove newly created record
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('fields/' . $newFieldID[0]);
$I->seeResponseCodeIs(204);