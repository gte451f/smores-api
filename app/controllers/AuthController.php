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
        
        if (strlen($password) < 2 or strlen($email) < 2) {
            throw new HTTPException("Bad credentials supplied.", 400, array(
                'dev' => "Supplied credentials were not valid. Email: $email",
                'internalCode' => '3446567'
            ));
        }
        
        // let's try local auth instead
        $result = $this->auth->authenticate($email, $password);
        
        if ($result == false) {
            throw new HTTPException("Bad credentials supplied.", 401, array(
                'dev' => "Supplied credentials were not valid. Email: $email",
                'internalCode' => '304958034850'
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
                throw new HTTPException("Incomplete account data submitted.", 400, array(
                    'dev' => "Not all required data fields were supplied.  Missing: $field",
                    'internalCode' => '891316819464749684'
                ));
            }
        }
        
        // attempt to create account
        // use transaction here?
        $account = new \PhalconRest\Models\Accounts();
        if ($account->create() == false) {
            throw new ValidationException("Internal error adding new account", array(
                'internalCode' => '78498119519',
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
            if ($user->create() == false) {
                throw new ValidationException("Internal error adding new user", array(
                    'internalCode' => '7891351889184',
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
                if ($owner->create() == false) {
                    throw new ValidationException("Internal error adding new owner", array(
                        'internalCode' => '98616381',
                        'dev' => 'Error while attempting to create a brand new owner'
                    ), $owner->getMessages());
                    // roll back account
                    $account->delete();
                    // roll back user
                    $user->delete();
                } else {
                    // proceed to last step
                    $number = new \PhalconRest\Models\OwnerNumbers();
                    $number->user_id = $user->id;
                    $number->phone_type = $post['phone_type'];
                    $number->number = $post['number'];
                    $number->primary = 1;
                    if ($number->create() == false) {
                        throw new ValidationException("Internal error adding new phone number", array(
                            'internalCode' => '8941351968151313546494',
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
                'internalCode' => '30945680384502037'
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
        
        // $userName = $this->request->get("userName", array(
        // "string",
        // "alphanum"
        // ));
        // $code = $this->request->get('code');
        
        if (strlen($code) < 25 or strlen($email) < 6) {
            throw new HTTPException("Bad activation data supplied.", 400, array(
                'dev' => "Supplied activation email and code were not valid. Email: $email",
                'internalCode' => '98411916891891'
            ));
        }
        
        $search = array(
            'email' => $email,
            'code' => $code
        );
        
        $users = \PhalconRest\Models\Users::query()->where("email = :email:")
            ->andWhere("active = 'Inactive'")
            ->andWhere("code = :code:")
            ->bind($search)
            ->execute();
        
        $user = $users->getFirst();
        
        if ($user) {
            $user->active = 'Active';
            $user->code = NULL;
            $result = $user->save();
            
            // update account as well
            if ($user->user_type == 'Owner') {
                $owner = $user->Owners;
                $account = $owner->Accounts;
                $account->active = 'Active';
                $result = $account->save();
                
                if ($result) {
                    return array(
                        'status' => 'Active',
                        'result' => $result
                    );
                } else {
                    throw new ValidationException("Internal error activating user.", array(
                        'internalCode' => '6456513131',
                        'dev' => 'Error while attempting to activate account.'
                    ), $account->getMessages());
                }
            }
            
            return array(
                'status' => 'Active',
                'result' => $result
            );
        } else {
            throw new HTTPException("Bad activation data supplied.", 400, array(
                'dev' => "Supplied activation email and code were not valid. Email: $email",
                'internalCode' => '2168546681'
            ));
        }
    }

    /**
     * check for a code and password
     * attempt to reset the accounts password so long as the code is valid
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
        
        $search = array(
            'code' => $code
        );
        
        $accounts = \PhalconRest\Models\Users::query()->where("active = 'Reset'")
            ->andWhere("code = :code:")
            ->bind($search)
            ->execute();
        
        $user = $accounts->getFirst();
        
        if ($user) {
            // $account = $accounts->getFirst();
            $user->active = 'Active';
            $user->password = $password;
            $user->code = NULL;
            $result = $user->save();
            return array(
                'status' => 'Active',
                'result' => $result
            );
        } else {
            throw new HTTPException("Bad credentials supplied.", 400, array(
                'dev' => "Could not save new password to account. Code: $code",
                'internalCode' => '891819816131634'
            ));
        }
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
        
        // SELECT u.email, o.account_id
        // FROM owners AS o
        // JOIN accounts AS a ON o.account_id = a.id
        // JOIN users AS u ON o.user_id = u.id
        // WHERE a.active <> 'Inactive'
        // AND u.email = 'aaaa@aaa.com';
        
        $query = \PhalconRest\Models\Users::query()->where("email = :email: AND (active = 'Active' OR active = 'Reset') ");
        $search = array(
            'email' => $email
        );
        
        $users = $query->bind($search)->execute();
        $user = $users->getFirst();
        
        if ($user) {
            
            if ($user->user_type == 'Owner') {
                $owner = $user->Owners;
                $account = $owner->Accounts;
                
                // check that account is valid
                if ($account and ($account->active == 'Inactive' or $account->active == 'Archived')) {
                    // modify the user and return the code
                    throw new HTTPException("Bad activation data supplied.", 400, array(
                        'dev' => "Supplied activation email is not valid. Email: $email",
                        'internalCode' => '2168546681'
                    ));
                }
                
                // should work for either Owner or Employee
                $user->active = 'Reset';
                $user->code = substr(md5(rand()), 0, 45);
                
                // send email somewhere around here
                
                $result = $user->save();
                return array(
                    'status' => 'Reset',
                    'result' => $result,
                    'code' => $user->code
                );
            }
        } else {
            // somehow test for false results
            throw new HTTPException("The identifier you supplied is invalid.", 400, array(
                'dev' => "Supplied identifier was not valid. Email: $email",
                'internalCode' => '89841911385131'
            ));
        }
    }
}