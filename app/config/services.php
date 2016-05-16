<?php
// Factory Default loads all services by default....
use Phalcon\DI\FactoryDefault as DefaultDI;
use Phalcon\Loader;

// used for logging sql commands
use Phalcon\Logger;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Db\Adapter\Pdo\Mysql as Connection;
use PhalconRest\Libraries\MessageBag as MessageBag;

// PhalconRest libraries
use PhalconRest\API\Request as Request;
use PhalconRest\API\Inflector;

// for keeping time
use PHPBenchTime\Timer;

// for password and credit card encryption
use Phalcon\Crypt;
use Phalcon\Security;

$T = new \PHPBenchTime\Timer();
$T->start();

/**
 * The DI is our direct injector.
 * It will store pointers to all of our services
 * and we will insert it into all of our controllers.
 *
 * @var DefaultDI
 */
$di = new DefaultDI();

$di->setShared('request', function () {
    // $request = new \PhalconRest\Libraries\Request\Request();
    $request = new Request();
    // we expect inputs to be camel, so we convert to snake for server side
    $request->defaultCaseFormat = 'snake';
    return $request;
});

// load a security service applied to select controllers
$di->setShared('securityService', function () use ($config) {
    return new \PhalconRest\Libraries\Security\SecurityService();
});

// stopwatch service to track
$di->setShared('stopwatch', function () use ($T) {
    // start the stopwatch
    return $T;
});

// hold messages that should be returned to the client
$di->setShared('messageBag', function () {
    return new \PhalconRest\Libraries\MessageBag\MessageBag();
});

/**
 * Return array of the Collections, which define a group of routes, from
 * routes/collections.
 * These will be mounted into the app itself later.
 */
$di->set('collections', function () use ($config) {
    $collections = include('../app/routes/routeLoader.php');
    return $collections;
});

/**
 * $di's setShared method provides a singleton instance.
 * If the second parameter is a function, then the service is lazy-loaded
 * on its first instantiation.
 */
$di->setShared('config', function () use ($config) {
    return $config;
});

// As soon as we request the session service, it will be started.
$di->setShared('session', function () {
    $session = new \Phalcon\Session\Adapter\Files();
    $session->start();
    return $session;
});

// general purpose cache used to store complex or database heavy structures
$di->setShared('cache', function () use ($config) {
    // Cache data for one hour by default
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
        'lifetime' => 60
    ));

    // Create the component that will cache "Data" to a "File" backend
    // Set the cache file directory - important to keep the "/" at the end of
    // of the value for the folder
    $cache = new \Phalcon\Cache\Backend\File($frontCache, array(
        'cacheDir' => $config['application']['cacheDir']
    ));
    return $cache;
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

$di->setShared('modelsManager', function () {
    return new \Phalcon\Mvc\Model\Manager();
});

$di->set('modelsMetadata', function () use ($config) {
    $metaData = new \Phalcon\Mvc\Model\Metadata\Files(array(
        'metaDataDir' => $config['application']['tempDir']
    ));
    return $metaData;
});

// used in model?
$di->setShared('memory', function () {
    return new \Phalcon\Mvc\Model\MetaData\Memory();
});

// hold messages that should be returned to the client
$di->setShared('registry', function () {
    return new \Phalcon\Registry();
});

// querybuild class, entity depends on this
//$di->set('queryBuilder', function () {
//    return new \PhalconRest\API\QueryBuilder();
//});


$di->set('queryBuilder', [
    'className' => '\\PhalconRest\\API\\QueryBuilder',
    'arguments' => [
        ['type' => 'parameter'],
        ['type' => 'parameter'],
        ['type' => 'parameter']
    ]
]);


// phalcon inflector?
$di->setShared('inflector', function () {
    return new Inflector();
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

        // config the event and log services
        $eventsManager = new EventsManager();
        $fileName = date("d_m_y");
        $logger = new FileLogger($config['application']['loggingDir'] . "$fileName-db-query.log");

        $eventsManager->attach('db', function ($event, $connection) use ($logger, $registry) {
            if ($event->getType() == 'beforeQuery') {
                $count = $registry->dbCount;
                $count++;
                $registry->dbCount = $count;

                $logger->log($connection->getSQLStatement(), Logger::INFO);
                $vars = $connection->getSQLVariables();
                if (count($vars) > 0) {
                    $variableList = 'Variables: ';
                    foreach ($vars as $key => $val) {
                        $variableList .= " ( [$key]-[$val] ) ";
                    }
                    $logger->log($variableList, Logger::INFO);
                }
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

/**
 * If our request contains a body, it has to be valid JSON.
 * This parses the body into a standard Object and makes that available from the DI.
 * If this service is called from a function, and the request body is not valid JSON or is empty,
 * the program will throw an Exception.
 */
$di->setShared('requestBody', function () {
    $in = file_get_contents('php://input');
    $in = json_decode($in, FALSE);

    // JSON body could not be parsed, throw exception
    if ($in === null) {
        throw new HTTPException('There was a problem understanding the data sent to the server by the application.', 409, array(
            'dev' => 'The JSON body sent to the server was unable to be parsed.',
            'code' => '5',
            'more' => ''
        ));
    }

    return $in;
});