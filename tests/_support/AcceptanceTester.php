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
     */
    public function login($userType)
    {
        switch ($userType) {
            case 'Owner':
                $password = 'password1234';
                $username = 'demo@smores.camp';
                break;
            
            case 'Employee':
                $password = 'password5678';
                $username = 'admin@smores.camp';
                break;
            
            default:
                // uh oh, unknown type!
                return false;
                break;
        }
        
        $I = $this;
        
        $I->sendPOST('auth/login', [
            'email' => $username,
            'password' => $password
        ]);
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $authData = $I->grabDataFromResponseByJsonPath('$');
        return $authData[0];
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
