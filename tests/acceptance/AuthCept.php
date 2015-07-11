<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Test AUTH related functions');

$newAccount = array(
    'password_confirm'=>'password01*',
    'email'=>'bbarton@email.com',
    'first_name'=>'billy',
    'last_name'=>'barton',
    'user_type'=>'Owner',
    'gender'=>'Female',
    'relationship'=>'Mother',
    'user'=>'',
    'password'=>'password01*',
    'number'=>'123-456-7890',
    'primary'=>1,
    'phone_type'=>'Office'
);

// create a brand new account
$I->sendPOST('auth/create', $newAccount);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(201);
$result = $I->grabDataFromResponseByJsonPath('$.status');

// load a particular employee
// $I->sendGet("/employees/{$newEmployeeID[0]}");
// $I->seeResponseCodeIs(200);
// $I->seeResponseIsJson();
// $I->seeResponseJsonMatchesJsonPath('$.employee[0].id');

// now remove an employee
// $I->sendDELETE('employees/' . $newEmployeeID[0]);
// $I->seeResponseCodeIs(204);