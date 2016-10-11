<?php
$I = new AcceptanceTester($scenario);
// test various search examples');

// attempt to login as Owner first
$user = $I->login('Employee');

// simple search of child table
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/attendees?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');

// complex search on child table
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/attendees?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');

// simple search of complex table
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/events?page=1&per_page=5');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');

// test with syntax
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/events?page=1&per_page=3&with=cabins,locations,programs,sessions');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');
$I->seeResponseJsonMatchesJsonPath('$.data[*].relationships.location.data.id');
$I->seeResponseJsonMatchesJsonPath('$.data[*].relationships.program.data.id');
$I->seeResponseJsonMatchesJsonPath('$.data[*].relationships.session.data.id');
$I->seeResponseJsonMatchesJsonPath('$.data[*].relationships.cabin.data.id');

// test with + single individual syntax
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/events/1?page=1&per_page=5&with=cabins,locations,programs,sessions');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data.id');
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.location.data.id');
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.program.data.id');
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.session.data.id');
$I->seeResponseJsonMatchesJsonPath('$.data.relationships.cabin.data.id');

// test searching related hasOne records
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/attendees?limit=5&first_name=camper');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[0].id');

// test searching OR, related hasOne and wildcards
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/attendees?limit=5&first_name||last_name=*al*');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');

// test searching OR, related hasOne and wildcards
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/attendees?limit=5&first_name||last_name=*al*&page=2&limit=2');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');

// previously failed search
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/registrations?attendees%3Aaccount_id=95&with=all');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');


// previously failed search
$I->haveHttpHeader('X_AUTHORIZATION', "Token: {$user->attributes->token}");
$I->sendGet('/registrations?page=1&per_page=10&sortField=id&with=users');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.data[*].id');
$count = $I->grabDataFromResponseByJsonPath('$.meta[0].total_record_count');
