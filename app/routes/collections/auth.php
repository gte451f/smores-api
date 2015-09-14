<?php

/**
 * collection of routes to support for user controller
 */
return call_user_func(function () {
    $routes = new \Phalcon\Mvc\Micro\Collection();
    
    // VERSION NUMBER SHOULD BE FIRST URL PARAMETER, ALWAYS
    // setHandler MUST be a string in order to support lazy loading
    $routes->setPrefix('/v1/auth')
        ->setHandler('\PhalconRest\Controllers\AuthController')
        ->setLazy(true);
    
    // Set Access-Control-Allow headers.
    $routes->options('/', 'optionsBase');
    $routes->options('/{id}', 'optionsOne');
    
    // First paramter is the route, which with the collection prefix here would be GET /user/
    // Second paramter is the function name of the Controller.
    
    // custom routes
    $routes->get('/session_check', 'session_check');
    $routes->get('/logout', 'logout');
    $routes->get('/scratch1', 'scratch1');
    
    $routes->post('/login', 'login');
    $routes->post('/create', 'create');
    $routes->post('/reminder', 'reminder');
    $routes->post('/activate', 'activate');
    $routes->post('/reset', 'reset');
    
    // copies used mostly for testing
    $routes->get('/login', 'login');
    // $routes->get('/activate', 'activate');
    // $routes->get('/reminder', 'reminder');
    // $routes->get('/reset', 'reset');
    
    return $routes;
});