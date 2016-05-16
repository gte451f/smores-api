<?php
// let apache tell us what environment we are in
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'docker'));

// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', str_replace('/public', '/app/', __DIR__));

use \PhalconRest\Util\HTTPException;
use \PhalconRest\Util\DatabaseException;

// use output buffer to manage what is actually sent to the client...or clean it out before it's sent
ob_start();

try {
    /**
     * read in config values
     */
    require_once APPLICATION_PATH . 'config/config.php';

    /**
     * bootstrap Phalcon Auto Loader
     */
    require_once APPLICATION_PATH . 'config/loader.php';

    /**
     * read in services
     */
    require_once APPLICATION_PATH . 'config/services.php';

    /**
     * init app object
     */
    require_once APPLICATION_PATH . 'config/bootstrap.php';

    /**
     * handle here for unit testing requirement
     */
    $app->handle();
} catch (Phalcon\Exception $e) {
    // process an uncaught exception as a generic HTTP exception
    throw new HTTPException("Phalcon Exception Caught.", 500, array(
        'dev' => $e->getTrace(),
        'more' => $e->getMessage(),
        'code' => '89798414618968161'
    ));
} catch (PDOException $e) {
    // catch any unexpected database exceptions
    throw new DatabaseException("Fatal Database Exception Caught.", 500, array(
        'dev' => $e->getTrace(),
        'more' => $e->getMessage(),
        'code' => '313519613516184'
    ));
}