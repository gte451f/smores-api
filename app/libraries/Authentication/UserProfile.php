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

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $firstName;

    /**
     *
     * @var string
     */
    public $lastName;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var int
     */
    public $accountId;

    /**
     * is this user a staff member or a client for the portal?
     * Attendee|Employee|Owner
     *
     * @var string
     */
    public $userType;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Authentication\UserProfile::loadProfileByToken()
     */
    public function loadProfile($search)
    {
        /**
         * backdoor to run app w/o security...yeah
         */
        if ($search == "token = 'HACKYHACKERSON'") {
            // load config defined user id
            $config = $this->getDI()->get('config');
            $id = $config['securityUserId'];
            $search = "id = $id";
        } else {
            $search .= " and active = 1";
        }
        
        $users = \PhalconRest\Models\Users::find($search);
        
        switch (count($users)) {
            case 0:
                throw new HTTPException("No user found", 401, array(
                    'dev' => "No valid user was found",
                    'code' => '347589347598'
                ));
                break;
            
            case 1:
                foreach ($users as $user) {
                    $this->id = $user->id;
                    $this->firstName = $user->first_name;
                    $this->lastName = $user->last_name;
                    $this->email = $user->email;
                    $this->userType = $user->user_type;
                    
                    if ($user->user_type == 'Owner') {
                        $this->accountId = $user->owners->account_id;
                    }
                    
                    $this->gender = $user->gender;
                    $this->expiresOn = $this->generateExpiration();
                    $this->token = $user->token;
                }
                break;
            
            default:
                throw new HTTPException("Multiple users found!", 401, array(
                    'dev' => "More than one user was found, when only one was expected.",
                    'code' => '347589347598'
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
        $search = "email = '{$this->email}' and active = '1'";
        $user = \PhalconRest\Models\Users::findFirst($search);
        
        if (! $user) {
            throw new HTTPException("No valid user account was found", 401, array(
                'dev' => "This has to be a bug to have made it this far.",
                'internalCode' => '760708898897686'
            ));
            break;
        }
        
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