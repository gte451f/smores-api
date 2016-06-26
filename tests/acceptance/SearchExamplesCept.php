<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test various search examples');

// attempt to login as Owner first
$user = $I->login('Employee');

// simple search of child table
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/attendees?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');

// complex search on child table
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/attendees?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');

// simple search of complex table
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/events?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.events[*].id');

// test with syntax
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/events?page=1&per_page=5&with=cabins,locations,programs,sessions');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.events[*].id');
$I->seeResponseJsonMatchesJsonPath('$.locations[*].id');
$I->seeResponseJsonMatchesJsonPath('$.programs[*].id');
$I->seeResponseJsonMatchesJsonPath('$.cabins[*].id');
$I->seeResponseJsonMatchesJsonPath('$.sessions[*].id');

// test with + single individual syntax
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/events/1?page=1&per_page=5&with=cabins,locations,programs,sessions');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.event[0].id');
$I->seeResponseJsonMatchesJsonPath('$.locations[*].id');
$I->seeResponseJsonMatchesJsonPath('$.programs[*].id');
$I->seeResponseJsonMatchesJsonPath('$.cabins[*].id');
$I->seeResponseJsonMatchesJsonPath('$.sessions[*].id');

// test searching related hasOne records
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/attendees?limit=5&first_name=rogan');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');

// test searching OR, related hasOne and wildcards
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/attendees?limit=5&first_name||last_name=*jo*');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');

// test searching OR, related hasOne and wildcards
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/attendees?limit=5&first_name||last_name=*jo*&page=2&limit=2');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');

// test searching OR, related hasOne and wildcards
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/accounts?limit=5&with=owners');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.accounts[*].id');
$I->seeResponseJsonMatchesJsonPath('$.owners[*].id');

// previously failed search
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/registrations?attendees%3Aaccount_id=95&with=all');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.registrations[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');

// previously failed search
$I->haveHttpHeader('X_AUTHORIZATION', "Token: " . $user['attributes']['token']);
$I->sendGet('/registrations?page=1&per_page=10&sortField=id&with=users');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.registrations[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');


// attempt to logout as Owner
$I->logout($user['attributes']['token']);
