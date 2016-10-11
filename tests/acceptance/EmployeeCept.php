<?php
$I = new AcceptanceTester($scenario);
// Test Employee CRUD

$email = 'test' . rand(1, 8888) . '@test.com';

$newRecord = [
    'data' => [
        'attributes' => [
            'active' => null,
            'password' => 'password1234',
            'email' => $email,
            'last_name' => 'test4',
            'first_name' => 'test4',
            'user_type' => 'Employee',
            'gender' => 'Female',
            'position' => 'CIO'

        ],
        'relationships' => [
        ],
        'type' => 'employees'
    ]
];

// attempt to login as Owner first
$user = $I->login('Employee');

// disable for now
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendPOST('employees', json_encode($newRecord));
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$newEmployeeID = $I->grabDataFromResponseByJsonPath('$.data.id');

// load a particular employee
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet("/employees/{$newEmployeeID[0]}");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data.id');

// now remove an employee
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendDELETE('employees/' . $newEmployeeID[0]);
$I->seeResponseCodeIs(204);