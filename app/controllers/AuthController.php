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
        $userName = $this->request->getPost("username", array(
            "string",
            "alphanum"
        ));
        $password = $this->request->getPost('password');
        
        $userName = 'foo';
        $password = '123456789';
        
        if (strlen($password) < 2 or strlen($userName) < 2) {
            throw new HTTPException("Bad credentials supplied.", 400, array(
                'dev' => "Supplied credentials were not valid. UserName: $userName",
                'internalCode' => '3446567'
            ));
        }
        
        // let's try local auth instead
        $result = $this->auth->authenticate($userName, $password);
        
        if ($result == false) {
            throw new HTTPException("Bad credentials supplied.", 401, array(
                'dev' => "Supplied credentials were not valid. UserName: $userName",
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
        
        $foo = 1;
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
     * custom function to take in a username and activation code
     * if a match is found on three criteria
     * 1)active
     * 2)code
     * 3)user_name
     * ....switch the account from inactive to active
     *
     * @throws HTTPException
     * @return array
     */
    public function activate()
    {
        $userName = $this->request->getPost("userName", array(
            "string",
            "alphanum"
        ));
        $code = $this->request->getPost('code');
        
        // $userName = $this->request->get("userName", array(
        // "string",
        // "alphanum"
        // ));
        // $code = $this->request->get('code');
        
        if (strlen($code) < 25 or strlen($userName) < 2) {
            throw new HTTPException("Bad activation data supplied.", 400, array(
                'dev' => "Supplied activation username and code were not valid. UserName: $userName",
                'internalCode' => '98411916891891'
            ));
        }
        
        $search = array(
            'user_name' => $userName,
            'code' => $code
        );
        
        $accounts = \PhalconRest\Models\Accounts::query()->where("user_name = :user_name:")
            ->andWhere("active = 'Inactive'")
            ->andWhere("code = :code:")
            ->bind($search)
            ->execute();
        
        $account = $accounts->getFirst();
        
        if ($account) {
            // $account = $accounts->getFirst();
            $account->active = 'Active';
            $result = $account->save();
            return array(
                'status' => 'Active',
                'result' => $result
            );
        } else {
            throw new HTTPException("Bad activation data supplied.", 400, array(
                'dev' => "Supplied activation username and code were not valid. UserName: $userName",
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
        $code = $this->request->getPost('code');
        
        $search = array(
            'code' => $code
        );
        
        $accounts = \PhalconRest\Models\Accounts::query()->where("active = 'Reset'")
            ->andWhere("code = :code:")
            ->bind($search)
            ->execute();
        
        $account = $accounts->getFirst();
        
        if ($account) {
            // $account = $accounts->getFirst();
            $account->active = 'Active';
            $account->password = $password;
            $account->code = NULL;
            $result = $account->save();
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
        $userName = $this->request->getPost("userName", array(
            "string",
            "alphanum"
        ));
        $email = $this->request->getPost('email');
        
        $userName = $this->request->get("userName", array(
            "string",
            "alphanum"
        ));
        $email = $this->request->get('email');
        
        $query = \PhalconRest\Models\Accounts::query()->where("active = 'Active'");
        
        // create a valid search query based on the first valid supplied value
        if (strlen($userName) > 0) {
            $query = \PhalconRest\Models\Accounts::query()->where("active = 'Active'")->andWhere("user_name = :user_name:");
            $search = array(
                'user_name' => $userName
            );
            
            $accounts = $query->bind($search)->execute();
            $account = $accounts->getFirst();
        } elseif (strlen($email) > 0) {
            
            // SELECT u.email, o.account_id
            // FROM owners AS o
            // JOIN accounts AS a ON o.account_id = a.id
            // JOIN users AS u ON o.user_id = u.id
            // WHERE a.active <> 'Inactive'
            // AND u.email = 'aaaa@aaa.com';
            
            $query = \PhalconRest\Models\Users::query()->
            // ->join('\PhalconRest\Models\Owners')
            // ->join('\PhalconRest\Models\Accounts')
            // ->where("active <> 'Inactive'")
            where("email = :email:");
            
            $search = array(
                'email' => $email
            );
            
            $users = $query->bind($search)->execute();
            $user = $users->getFirst();
            
            if ($user->user_type == 'Owner') {
                $owner = $user->Owners;
                $account = $owner->Accounts;
            } else {
                throw new HTTPException("The identifier you supplied is invalid.", 400, array(
                    'dev' => "Supplied identifier was not valid. Email: $email",
                    'internalCode' => '89841911385131'
                ));
            }
        } else {
            // uh oh
            throw new HTTPException("The identifier you supplied is invalid.", 400, array(
                'dev' => "Supplied identifier was not valid.",
                'internalCode' => '9841961353138664'
            ));
        }
        
        if ($account and $account->active != 'Inactive') {
            
            // modify the account and return the code
            $account->active = 'Reset';
            $account->code = substr(md5(rand()), 0, 45);
            
            $result = $account->save();
            return array(
                'status' => 'Reset',
                'result' => $result,
                'code' => $account->code
            );
        } else {
            throw new HTTPException("Bad activation data supplied.", 400, array(
                'dev' => "Supplied activation username and code were not valid. UserName: $userName",
                'internalCode' => '2168546681'
            ));
        }
    }
}