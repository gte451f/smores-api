<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('test various search examples');

// simple search of child table
$I->sendGet('/attendees?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');

// complex search on child table
$I->sendGet('/attendees?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');

// simple search of complex table
$I->sendGet('/events?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.events[*].id');

// test with syntax
$I->sendGet('/events?page=1&per_page=5&with=cabins,locations,programs,sessions');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.events[*].id');
$I->seeResponseJsonMatchesJsonPath('$.locations[*].id');
$I->seeResponseJsonMatchesJsonPath('$.programs[*].id');
$I->seeResponseJsonMatchesJsonPath('$.cabins[*].id');
$I->seeResponseJsonMatchesJsonPath('$.sessions[*].id');

// test with + single individual syntax
$I->sendGet('/events/1?page=1&per_page=5&with=cabins,locations,programs,sessions');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.event[0].id');
$I->seeResponseJsonMatchesJsonPath('$.locations[*].id');
$I->seeResponseJsonMatchesJsonPath('$.programs[*].id');
$I->seeResponseJsonMatchesJsonPath('$.cabins[*].id');
$I->seeResponseJsonMatchesJsonPath('$.sessions[*].id');

// test searching related hasOne records
$I->sendGet('/attendees?limit=5&first_name=rogan');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');

// test searching OR, related hasOne and wildcards
$I->sendGet('/attendees?limit=5&first_name||last_name=*jo*');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');

// test searching OR, related hasOne and wildcards
$I->sendGet('/attendees?limit=5&first_name||last_name=*jo*&page=2&limit=2');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.attendees[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');

// test searching OR, related hasOne and wildcards
$I->sendGet('/accounts?limit=5&with=owners');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.accounts[*].id');
$I->seeResponseJsonMatchesJsonPath('$.owners[*].id');
