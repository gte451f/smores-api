<?php
namespace PhalconRest\Libraries\Authentication;

use PhalconRest\Util\HTTPException;
use PhalconRest\Util\ValidationException;

/**
 * A few helpful tools to work with a user account
 *
 * @author jjenkins
 *        
 */
class Util
{

    public static function reset($password, $code)
    {
        $search = array(
            'code' => $code
        );
        
        $users = \PhalconRest\Models\Users::query()->where("status = 'Reset'")
            ->andWhere("code = :code:")
            ->bind($search)
            ->execute();
        
        $user = $users->getFirst();        
        if ($user) {
            $user->status = 'Active';
            $user->password = $password;
            $user->code = NULL;
            
            // update record
            if ($user->save() == false) {                
                throw new ValidationException("Could not reset password.", array(
                    'dev' => 'Error updating users account while attempting to reset password',
                    'code' => '4981909148946416'
                ), $user->getMessages());
            } else {
                
                $event = new \PhalconRest\Models\EventLogs();
                $event->description = "UserName:::" . $user->user_name;
                $event->code = 'Password Reset';
                $event->created_by_id = $user->id;
                if ($event->save() == false) {
                    throw new ValidationException("Could not reset password", array(
                        'dev' => 'Reset attempt failed when saving event record',
                        'code' => '798418614686767684'
                    ), $event->getMessages());
                }
                return true;
            }
        } else {
            throw new HTTPException("Bad credentials supplied.", 400, array(
                'dev' => "Could not save new password to account. Code: $code",
                'code' => '891819816131634'
            ));
        }
        return false;
    }

    /**
     * custom function to mark an account for password reset
     * for active accounts, move their status to Reset and create a new CODE
     * otherwise throw an error
     *
     * @param string $email            
     */
    public static function reminder($email, $inactive = false)
    {
        // extra wrinkle to prevent from scenarios from converting an inactive user to active
        // ie if a public user wants to reset an account, they can only reset active accounts
        if ($inactive) {
            $where = "email = :email:";
        } else {
            $where = "email = :email: AND status != 'Inactive'";
        }
        
        // look for either active or password reset
        $query = \PhalconRest\Models\Users::query()->where($where);
        $search = array(
            'email' => $email
        );
        
        $users = $query->bind($search)->execute();
        $user = $users->getFirst();
        
        if ($user) {            
            // mark for password reset
            // this way a user can only attempt to reset the password of an account that has performed this step
            $user->status = 'Reset';
            // generate a pseudo random string for the activation code
            $user->code = substr(md5(rand()) . md5(rand()), 0, 45);
            
            // send email somewhere around here
            
            // update record
            if ($user->save() == false) {
                throw new ValidationException("Could not request reminder.", array(
                    'dev' => 'Could not update user record while resetting the password',
                    'code' => '9891861681618761584684'
                ), $user->getMessages());
            } else {
                return true;
            }
        } else {
            // somehow test for false results
            throw new HTTPException("The identifier you supplied is invalid.", 400, array(
                'dev' => "Supplied identifier was not valid. Email: $email",
                'code' => '89841911385131'
            ));
        }
        return false;
    }
}