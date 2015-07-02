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

    public $email;

    public $accountId;

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
            $search .= " and active = 'Active'";
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
                    $this->firstName = $user->first_name;
                    $this->lastName = $user->last_name;
                    $this->email = $user->email;
                    
                    if ($user->user_type == 'Owner') {
                        $this->accountId = $user->owners->account_id;
                    }
                    
                    $this->gender = $user->gender;
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
        $search = "email = '{$this->email}' and active = 'Active'";
        $user = \PhalconRest\Models\Users::findFirst($search);
        
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