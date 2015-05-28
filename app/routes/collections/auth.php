<?php

/**
 * collection of routes to support for user controller
 */
return call_user_func(function ()
{
    $routes = new \Phalcon\Mvc\Micro\Collection();
    
    // VERSION NUMBER SHOULD BE FIRST URL PARAMETER, ALWAYS
    // setHandler MUST be a string in order to support lazy loading
    $routes->setPrefix('/v1/auth')
        ->setHandler('\PhalconRest\Controllers\AuthController')
        ->setLazy(true);
    
    // Set Access-Control-Allow headers.
    $routes->options('/', 'optionsBase');
    $routes->options('/{id}', 'optionsOne');
    
    $routes->get('/session_check', 'session_check');
    
    $routes->get('/login', 'login');
    // First paramter is the route, which with the collection prefix here would be GET /user/
    // Second paramter is the function name of the Controller.
    $routes->post('/login', 'login');
    // This is exactly the same execution as GET, but the Response has no body.
    $routes->get('/logout', 'logout');
    
    return $routes;
});