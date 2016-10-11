<?php
/**
 * load custom services your particular API requires here
 * careful not to duplicate services already loaded by the core api
 */

// used for logging sql commands
use Phalcon\Logger;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Db\Profiler as DbProfiler;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Db\Adapter\Pdo\Mysql as Connection;

// for password and credit card encryption
use Phalcon\Crypt;
use Phalcon\Security;


// set default api behavior regarding general save actions
\PhalconRest\API\BaseModel::$throwOnSave = true;


// load a security service applied to select controllers
$di->setShared('securityService', function () use ($config) {
    return new \PhalconRest\Libraries\Security\SecurityService();
});


// hold messages that should be returned to the client
$di->setShared('messageBag', function () {
    return new \PhalconRest\Libraries\MessageBag\MessageBag();
});

/**
 * load an authenticator w/ local adapter
 * called "auth" since the API expects a service of this name for subsequent token checks
 */
$di->setShared('auth', function ($type = 'Employee') use ($config) {

    $adapter = new \PhalconRest\Libraries\Authentication\Local();
    $profile = new \PhalconRest\Libraries\Authentication\UserProfile();
    $auth = new \PhalconRest\Authentication\Authenticator($adapter, $profile);
    $auth->userNameFieldName = 'email';
    return $auth;
});

// hold messages that should be returned to the client
$di->setShared('registry', function () {
    return new \Phalcon\Registry();
});

// one way to do reversable encryption
$di->setShared('crypt', function () {
    $crypt = new Crypt();

    // Set a global encryption key
    $crypt->setKey('%31.1e$i86e$f!8jz');
    return $crypt;
});

// one way to do reversable encryption
$di->setShared('security', function () {
    $security = new Security();

    // Set a global encryption key
    $security->setWorkFactor(12);
    return $security;
});

// one way to do reversable encryption
$di->setShared('paymentProcessor', function () {
    // TODO swap out a dummy adapter if no valid key is found
    $setting = \PhalconRest\Models\Settings::findFirst("name = 'Stripe API Key'");
    return new \PhalconRest\Libraries\Payments\StripeAdapter($setting->value);
});

/**
 * Database setup.
 */
$di->set('db', function () use ($config, $di) {

    // Listen all the database events if debugging is enabled
    if ($config['application']['debugApp']) {
        $registry = $di->get('registry');
        $registry->dbCount = 0;
        $registry->dbTimer = 0;

        // config the event and log services
        $eventsManager = new EventsManager();
        $profiler = new DbProfiler();

        $fileName = date("d_m_y");
        $logger = new FileLogger($config['application']['loggingDir'] . "$fileName-db-query.log");

        $eventsManager->attach('db', function ($event, $connection) use ($logger, $registry, $profiler) {
            if ($event->getType() == 'beforeQuery') {
                $count = $registry->dbCount;
                $count++;
                $registry->dbCount = $count;

                $logger->log($connection->getSQLStatement(), Logger::INFO);

                // Start a profile with the active connection
                $profiler->startProfile($connection->getSQLStatement());

                $vars = $connection->getSQLVariables();
                if (count($vars) > 0) {
                    $variableList = 'Variables: ';
                    foreach ($vars as $key => $val) {
                        $variableList .= " ( [$key]-[$val] ) ";
                    }
                    $logger->log($variableList, Logger::INFO);
                }
            }

            if ($event->getType() == 'afterQuery') {
                // Stop the active profile
                $profiler->stopProfile();


                $profile = $profiler->getLastProfile();
                $registry->dbTimer = $registry->dbTimer + round($profile->getTotalElapsedSeconds() * 1000, 2);
            }
        });
    }
    // init db connection
    $connection = new Connection($config['database']);

    // Assign the eventsManager to the db adapter instance
    if ($config['application']['debugApp']) {
        $connection->setEventsManager($eventsManager);
    }

    return $connection;
});