<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Test Employee CRUD');

$employee = '{"employee":
                {"active":null,
                "password":"password123",
                "email":"test4@test.com",
                "last_name":"test4",
                "first_name":"test4",
                "user_type":"Employee",
                "gender":"Female",
                "position": "CIO"}
             }';

// disable for now
$I->sendPOST('employees', $employee);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
// $newEmployeeID = $I->grabDataFromResponse('$.employee[0].id');
$newEmployeeID = $I->grabDataFromResponseByJsonPath('$.employee[0].id');

// load a particular employee
$I->sendGet("/employees/{$newEmployeeID[0]}");
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.employee[0].id');

// now remove an employee
$I->sendDELETE('employees/' . $newEmployeeID[0]);
$I->seeResponseCodeIs(204);