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
        
        $userName = 'fred';
        $password = 'password';
        
        
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
}