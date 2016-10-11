<?php

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    /**
     * login action to generate a token for a user
     * choose a user "TYPE" and this action will login as that user
     * opt to login by type so each test doesn't need to know user credentials
     *
     * @param string $userType
     *            Employee|Owner
     *
     * @return array
     */
    public function login($userType)
    {
        switch ($userType) {
            case 'Owner':
                $response = '{"data":{"id":"1","type":"profile","attributes":{"email":"test-demo@smores.camp","last-name":"Owner","first-name":"TEST-Demo","account-id":"1","user-type":"Owner","user-name":null,"token":"Op6G7d74APOTiKc1Twjph3HoY7BR5IEKgIClL3X2tw3XZ5qwoA5CZRJvnQ5moMEI"}}}';
                break;

            case 'Employee':
                $response = '{"data":{"id":"1","type":"profile","attributes":{"email":"test-admin@smores.camp","last-name":"Employee","first-name":"TEST-Admin","account-id":"1","user-type":"Employee","user-name":null,"token":"ShEpslnket8Ngngnr1gjKgRumdgMmySuJcQ4sX9wfL64DiAEeI7oCSgwMOx93QCv"}}}';
                break;

            case 'Attendee':
                // not supported yet
                return false;

            default:
                // uh oh, unknown type!
                return false;
                break;
        }
        $authData = json_decode($response);
        // print_r($authData);
        return $authData->data;
    }

    /**
     * for a given token, log the user out
     *
     * @param string $token
     */
    public function logout($token)
    {
        $I = $this;

        $I->haveHttpHeader('X_AUTHORIZATION', "Token: $token");
        $I->sendGet("auth/logout");
        $I->seeResponseCodeIs(200);
    }
}
