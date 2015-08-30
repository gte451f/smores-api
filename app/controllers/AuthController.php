<?php
namespace PhalconRest\Controllers;

use PhalconRest\Util\HTTPException;
use PhalconRest\Util\ValidationException;

/**
 */
class AuthController extends \Phalcon\DI\Injectable
{

    private $auth;

    public function __construct()
    {
        $di = \Phalcon\DI::getDefault();
        $this->setDI($di);
        
        // what type of auth is the client requesting be performed?
        $type = $this->request->getPost('type');
        
        // load the default auth library, pass in the client requested type employee | account
        $this->auth = $this->getDI()->get('auth', [
            $type
        ]);
    }

    /**
     *
     * @return multitype:string unknown |array
     */
    public function login()
    {
        $email = $this->request->getPost("email", array(
            "email"
        ));
        $password = $this->request->getPost('password');
        
        // $userName = 'foo';
        // $password = '123456789';
        
        if (strlen($password) < 8 or strlen($email) < 3) {
            throw new ValidationException("Bad Credentials Supplied", [
                'dev' => "Supplied credentials were not valid. UserName: $email",
                'code' => '3446567'
            ], [
                'password' => 'The password should be 8 characters or greater',
                'email' => 'The email must be greater than 3 characters'
            ]);
        }
        
        // let's try local auth instead
        $result = $this->auth->authenticate($email, $password);
        
        if ($result == false) {
            throw new HTTPException("Bad credentials supplied.", 401, array(
                'dev' => "Supplied credentials were not valid. Email: $email",
                'code' => '304958034850'
            ));
        } else {
            $profile = $this->auth->getProfile();
        }
        
        // return the basic data needed to authenticate future requests
        // in our case, a token and expiration date
        return (array) $profile;
    }

    /**
     * attempt to create a new account based on supplied values
     * roll back records if the new action fails
     * rely on underlying models to validate data...for the most part
     */
    public function create()
    {
        $request = $this->getDI()->get('request');
        $post = $request->getPost();
        
        // check that the requisite fields exist
        $fieldList = array(
            'last_name',
            'first_name',
            'email',
            'gender',
            'last_name',
            'number',
            'password',
            'password_confirm',
            'phone_type',
            'relationship'
        );
        
        // verify that all the required fields are present before continuing
        foreach ($fieldList as $field) {
            if (! isset($post[$field])) {
                throw new ValidationException("Incomplete account data submitted.", 400, array(
                    'dev' => "Not all required data fields were supplied.  Missing: $field",
                    'code' => '891316819464749684'
                ), [
                    $field => "$field is required, please enter a value for this field."
                ]);
            }
        }
        
        // attempt to create account
        // use transaction here?
        $account = new \PhalconRest\Models\Accounts();
        if (! $account->create()) {
            throw new ValidationException("Internal error adding new account", array(
                'code' => '78498119519',
                'dev' => 'Error while attempting to create a brand new account'
            ), $account->getMessages());
        } else {
            // proceed to next step
            $user = new \PhalconRest\Models\Users();
            $user->first_name = $post['first_name'];
            $user->last_name = $post['last_name'];
            $user->user_type = 'Owner';
            $user->gender = $post['gender'];
            $user->email = $post['email'];
            $user->password = $post['password'];
            if (! $user->create()) {
                throw new ValidationException("Internal error adding new user", array(
                    'code' => '7891351889184',
                    'dev' => 'Error while attempting to create a brand new user'
                ), $user->getMessages());
                // roll back account
                $account->delete();
            } else {
                // proceed to next step
                $owner = new \PhalconRest\Models\Owners();
                $owner->account_id = $account->id;
                $owner->user_id = $user->id;
                $owner->relationship = $post['relationship'];
                // always make the first owner created the primary
                $owner->primary_contact = 1;
                if (! $owner->create()) {
                    throw new ValidationException("Internal error adding new owner", array(
                        'code' => '98616381',
                        'dev' => 'Error while attempting to create a brand new owner'
                    ), $owner->getMessages());
                    // roll back account
                    $account->delete();
                    // roll back user
                    $user->delete();
                } else {
                    // proceed to last step
                    $number = new \PhalconRest\Models\OwnerNumbers();
                    $number->owner_id = $user->id;
                    $number->phone_type = $post['phone_type'];
                    $number->number = $post['number'];
                    $number->primary = 1;
                    if (! $number->create()) {
                        throw new ValidationException("Internal error adding new phone number", array(
                            'code' => '8941351968151313546494',
                            'dev' => 'Error while attempting to create a brand new phone number'
                        ), $number->getMessages());
                        // roll back account
                        $account->delete();
                        // roll back user
                        $user->delete();
                        // roll back owner
                        $owner->delete();
                    } else {
                        // success!
                        return array(
                            'status' => 'Success'
                        );
                    }
                }
            }
        }
    }

    /**
     * wipe the token
     *
     * @return multitype:
     */
    public function logout()
    {
        // TODO wipe session data here
        $token = $this->request->getHeader("X_AUTHORIZATION");
        $token = str_ireplace("Token: ", '', $token);
        $token = trim($token);
        
        $this->auth = $this->getDI()->get('auth');
        // $token = "LWHb27fjRW80ccymhb1mfOeSmaefl7H6vcXaTUw52cLJHc0MeaE5A01bM6bfWagd";
        $result = $this->auth->logUserOut($token);
        return array();
    }

    /**
     * temp function to see the stored session
     *
     * @return multitype:unknown
     */
    public function session_check()
    {
        $token = "LWHb27fjRW80ccymhb1mfOeSmaefl7H6vcXaTUw52cLJHc0MeaE5A01bM6bfWagd";
        
        if ($this->auth->isLoggedIn($token)) {
            $profile = $this->auth->getProfile();
            $profileArray = array(
                'userName' => $profile->userName,
                'id' => $profile->id,
                'firstName' => $profile->firstName,
                'lastName' => $profile->lastName,
                'token' => $profile->token,
                'expiresOn' => $profile->expiresOn
            );
            return $profileArray;
        } else {
            // TODO throw error here
            throw new HTTPException("Unauthorized, please authenticate first.", 401, array(
                'dev' => "Must be authenticated to access.",
                'code' => '30945680384502037'
            ));
        }
    }

    /**
     * custom function to take in a email and activation code
     * if a match is found on three criteria
     * 1)active
     * 2)code
     * 3)email
     * ....switch the account from inactive to active
     *
     * @throws HTTPException
     * @return array
     */
    public function activate()
    {
        $email = $this->request->getPost("email", array(
            "email"
        ));
        $code = $this->request->getPost("code", array(
            "string",
            "alphanum"
        ));
        
        if (strlen($code) < 25 or strlen($email) < 6) {
            throw new ValidationException("Bad activation data supplied", [
                'dev' => "Supplied activation email or code were not valid. Email: $email",
                'code' => '98411916891891'
            ], [
                'code' => 'The could should be 25 characters or greater',
                'email' => 'The email must be greater than 5 characters'
            ]);
        }
        
        $search = array(
            'email' => $email,
            'code' => $code
        );
        
        $users = \PhalconRest\Models\Users::query()->where("email = :email:")
            ->andWhere("active = 0")
            ->andWhere("code = :code:")
            ->bind($search)
            ->execute();
        
        $user = $users->getFirst();
        
        if ($user) {
            $user->active = 1;
            $user->code = NULL;
            $result = $user->save();
            
            // update account as well
            if ($user->user_type == 'Owner') {
                $owner = $user->Owners;
                $account = $owner->Accounts;
                $account->active = 1;
                $result = $account->save();
                
                if ($result) {
                    return array(
                        'status' => 'Active',
                        'result' => $result
                    );
                } else {
                    throw new ValidationException("Internal error activating user", array(
                        'code' => '6456513131',
                        'dev' => 'Error while attempting to activate account'
                    ), $account->getMessages());
                }
            }
            
            return array(
                'status' => 'Active',
                'result' => $result
            );
        } else {
            throw new HTTPException("Bad activation data supplied", 400, array(
                'dev' => "Could not find valid account Email: $email",
                'code' => '2168546681'
            ));
        }
    }

    /**
     * check for a code and password
     * attempt to reset the accounts password so long as the code is valid
     * use shared library for actual reset
     *
     * @throws HTTPException
     * @return array
     *
     */
    public function reset()
    {
        $password = $this->request->getPost("password", array(
            "string",
            "alphanum"
        ));
        $confirm = $this->request->getPost("confirm", array(
            "string",
            "alphanum"
        ));
        $code = $this->request->getPost("code", array(
            "string",
            "alphanum"
        ));
        
        if ($password != $confirm) {
            throw new ValidationException("Passwords do not match.", array(
                'dev' => "Confirm & Password values did not match.",
                'code' => '9498498946846'
            ), [
                'password' => "Password and Confirmation do not match"
            ]);
        }
        
        $result = \PhalconRest\Libraries\Authentication\Util::reset($password, $code);
        return array(
            'status' => 'Active',
            'result' => $result
        );
    }

    /**
     * custom function to mark an account for password reset
     * for active accounts, move their status to Reset and create a new CODE
     * otherwise throw an error
     *
     * @throws HTTPException
     * @return array
     */
    public function reminder()
    {
        $email = $this->request->getPost("email", array(
            "email"
        ));
        
        // $email = $this->request->get('email');
        $result = \PhalconRest\Libraries\Authentication\Util::reminder($email, false);
        
        return array(
            'status' => 'Reset',
            'result' => $result
        );
    }
}