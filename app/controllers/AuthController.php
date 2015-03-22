<?php
namespace PhalconRest\Controllers;

/**
 */
class AuthController extends \Phalcon\DI\Injectable
{

    public function login()
    {
        return array(
            'username' => 'jking',
            'password' => '********'
        );
    }

    public function logout()
    {
        echo 'logged out';
    }
}