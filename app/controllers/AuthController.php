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
        // the default auth library
        // named to work with underlying api
        $this->auth = $this->getDI()->get('auth');
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
        
        // $userName = 'fred';
        // $password = 'password';
        
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
     * if a match is found, switch the account from inactive to active
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
     * custom function to mark an account for password reset
     * for active accounts, move their status to Rest and create a new CODE
     * otherwise thrown an error
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