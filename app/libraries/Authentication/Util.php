<?php
namespace PhalconRest\Libraries\Authentication;

use Phalcon\DI\Injectable;
use PhalconRest\Util\HTTPException;
use PhalconRest\Util\ValidationException;

/**
 * A few helpful tools to work with a user account
 *
 * @author jjenkins
 *
 */
class Util extends \Phalcon\DI\Injectable
{

    public static function reset($password, $code)
    {
        $search = array(
            'code' => $code
        );

        $accounts = \PhalconRest\Models\Users::query()->where("active = 2")
            ->andWhere("code = :code:")
            ->bind($search)
            ->execute();

        $user = $accounts->getFirst();

        if ($user) {
            // $account = $accounts->getFirst();
            $user->active = 1;
            $user->password = $password;
            $user->code = NULL;

            // update record
            if ($user->save() == false) {
                throw new ValidationException("Could not reset password.", array(
                    'dev' => 'Error updating users account while attempting to reset password',
                    'code' => '4981909148946416'
                ), $user->getMessages());
            } else {
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
            $where = "email = :email: AND active <> 0";
        }

        // look for either active or password reset
        $query = \PhalconRest\Models\Users::query()->where($where);
        $search = array(
            'email' => $email
        );

        $users = $query->bind($search)->execute();
        $user = $users->getFirst();

        if ($user) {
            // brief check that account is active to begin with
            if ($user->user_type == 'Owner') {
                $owner = $user->Owners;
                $account = $owner->Accounts;

                // this way a user can only attempt to reset the password of an account that has performed this step
                // check that account is valid
                if (!$account or $account->active == 0) {
                    // modify the user and return the code
                    throw new HTTPException("Bad activation data supplied.", 400, array(
                        'dev' => "Account is not eligable for password resets. Email: $email",
                        'code' => '2168546681'
                    ));
                }
            }

            // should work for either Owner or Employee
            $user->active = 2;
            // generate a pseudo random string for the activation code
            $user->code = substr(md5(rand()) . md5(rand()), 0, 45);

            //
            // send email somewhere around here
            //

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