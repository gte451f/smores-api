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
        
        if ($search == "token = 'HACKYHACKERSON'"){
            // load config defined user id
            $search = 'id = 103';    
        } else {
            $search .= " and status = 'Active'";            
        }
                
        $users = \PhalconRest\Models\Users::find($search);
        
        switch (count($users)) {
            case 0:
                throw new HTTPException("No user found.", 401, array(
                    'dev' => "No valid user was found.",
                    'internalCode' => '347589347598'
                ));
                break;
            
            case 1:
                foreach ($users as $user) {
                    $this->id = $user->id;
                    $this->userName = $user->user_name;
                    $this->firstName = $user->first_name;
                    $this->lastName = $user->last_name;
                    $this->email = $user->email;
                    $this->expiresOn = $user->token_expires;
                    $this->token = $user->token;
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
        $search = "user_name = '{$this->userName}' and status = 'Active'";
        $user = \PhalconRest\Models\Users::findFirst($search);
        
        if ($wipe) {
            $this->token = $user->token = null;
            $this->expiresOn = $user->token_expires = null;
            // last login
        } else {
            $this->token = $user->token = $this->generateToken();
            $this->expoiresOn = $user->token_expires = $this->generateExpiration();
            // last login
        }
        
        return $user->save();
    }
}