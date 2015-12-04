<?php
namespace PhalconRest\Libraries\Security;

use Phalcon\DI\Injectable;

/**
 * This is a class which represents a user from the standpoint of group membership and matter assignments
 *
 * @author jking
 *        
 */
final class SecureUser extends Injectable
{
    // this is the user object that was established by the auth service
    private $logged_in_user;
    
    // an array representing all of the groups to which the logged in user has been assigned
    private $user_groups = array();
    
    // an array representing all of the accounts to which the logged in user has been assigned
    private $user_accounts = array();

    public function __construct()
    {
        $auth = $this->getDI()->get('auth');
        $this->logged_in_user = $user = $auth->getProfile();
        
        // assign user groups and accounts
        switch ($user->userType) {
            case 'Attendee':
                // freak out!
                throw new \PhalconRest\Util\HTTPException('Attempted hack by user of type Attendee!', 404, array(
                    'dev' => "User: $user->id  | Name: $user->firstName $user->lastName",
                    'code' => '7984685186161'
                ));
                break;
            
            case 'Employee':
                $this->user_groups = [
                    ADMIN_USER
                ];
                break;
            
            case 'Owner':
                $this->user_groups = [
                    PORTAL_USER
                ];
                break;
            
            default:
                // freak out!
                throw new \PhalconRest\Util\HTTPException('Attempted hack by user of unknown user type!', 404, array(
                    'dev' => "User: $user->id  | Name: $user->firstName $user->lastName  | Type: $user->userType",
                    'code' => '7984685186161'
                ));
                break;
        }
        
        return $this;
    }

    /**
     * This method returns the list of the matters to which the logged in user has been assigned.
     * It received an argument which is a string representing the format in which the data should be returned.
     *
     * @param string $format            
     * @return multitype:
     */
    public function getUserMatters($format = null)
    {
        switch ($format) {
            case 'csv':
                $result = '';
                $i = 1;
                foreach ($this->user_matters as $matter) {
                    $result .= $matter;
                    if ($i != count($this->user_matters)) {
                        $result .= ',';
                    }
                    $i ++;
                }
                break;
            
            default:
                $result = $this->user_matters;
                break;
        }
        
        return $result;
    }
    
    // getter method for user groups
    public function getUserGroups()
    {
        return $this->user_groups;
    }
}