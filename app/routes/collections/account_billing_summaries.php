<?php

/**
 * Standard routes for resource
 * Refer to routes/collections/example.php for further details
 */
return call_user_func(function () {
    $routes = new \Phalcon\Mvc\Micro\Collection();
    
    // VERSION NUMBER SHOULD BE FIRST URL PARAMETER, ALWAYS
    // setHandler MUST be a string in order to support lazy loading
    $routes->setPrefix('/v1/account_billing_summaries')
        ->setHandler('\PhalconRest\Controllers\AccountBillingSummaryController')
        ->setLazy(true);
    
    $routes->options('/', 'optionsBase');
    $routes->options('/{id}', 'optionsOne');
    $routes->get('/', 'get');
    $routes->head('/', 'get');
    $routes->get('/{id:[0-9]+}', 'getOne');
    $routes->head('/{id:[0-9]+}', 'getOne');
    
    return $routes;
});