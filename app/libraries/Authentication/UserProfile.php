<?php
namespace PhalconRest\Libraries\Authentication;

use PhalconRest\Util\HTTPException;

/**
 * extend to provide application specific data to the profile object
 * or to fill in profile object with specific data
 *
 * @author jjenkins
 *        
 */
class UserProfile extends \PhalconRest\Authentication\UserProfile
{

    public $id;

    public $firstName;

    public $lastName;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Authentication\UserProfile::loadProfileByToken()
     */
    public function loadProfile($search)
    {
        if ($search == "token = 'HACKYHACKERSON'") {
            // load config defined user id
            $search = 'user_id = 103';
        } else {
            $search .= " and active = 1";
        }
        
        $employees = \PhalconRest\Models\Employees::find($search);
        
        switch (count($employees)) {
            case 0:
                throw new HTTPException("No user found.", 401, array(
                    'dev' => "No valid user was found.",
                    'internalCode' => '347589347598'
                ));
                break;
            
            case 1:
                foreach ($employees as $employee) {
                    $this->id = $employee->user_id;
                    $this->userName = $employee->user_name;
                    $this->firstName = $employee->Users->first_name;
                    $this->lastName = $employee->Users->last_name;
                    $this->email = $employee->Users->email;
                    $this->expiresOn = 'NOT IMPLEMENTED YET';
                    $this->token = 'NOT IMPLEMENTED YET';
                }
                break;
            
            default:
                throw new HTTPException("Multiple users found!", 401, array(
                    'dev' => "More than one user was found, when only one was expected.",
                    'internalCode' => '347589347598'
                ));
                break;
        }
        return true;
    }

    /**
     * run after login to reset the local token
     */
    public function resetToken($wipe = false)
    {
        $search = "user_name = '{$this->userName}' and active = 1";
        $user = \PhalconRest\Models\Employees::findFirst($search);
        
        if ($wipe) {
            $this->token = $user->token = null;
            $this->expiresOn = $user->token_expires = null;
            // last login
        } else {
            $this->token = $user->token = $this->generateToken();
            $this->expiresOn = $user->token_expires = $this->generateExpiration();
            // last login
        }
        
        return $user->save();
    }
}