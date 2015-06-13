<?php
// define if it isn't already in palce
defined('APPLICATION_PATH') || define('APPLICATION_PATH', __DIR__ . '/../');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'staging');

/**
 * load low level helper here so it also works when used in conjunction with phalcon devtools
 */
require_once APPLICATION_PATH . 'helpers/base.php';

// your main aplication config file
// app/config/config.php
$config = [
    'application' => [
        'appDir' => APPLICATION_PATH,
        "controllersDir" => APPLICATION_PATH . 'controllers/',
        "modelsDir" => APPLICATION_PATH . 'models/',
        "entitiesDir" => APPLICATION_PATH . 'entities/',
        "responsesDir" => APPLICATION_PATH . 'responses/',
        "exceptionsDir" => APPLICATION_PATH . 'exceptions/',
        "librariesDir" => APPLICATION_PATH . 'libraries/',
        'baseUri' => '/',
        'basePath' => '/'
    ],
    'namespaces' => [
        'models' => "PhalconRest\\Models\\",
        'controllers' => "PhalconRest\\Controllers\\",
        'libraries' => "PhalconRest\\Libraries\\",
        'entities' => "PhalconRest\\Entities\\"
    ],
    // used as a system wide prefix to all file storage paths
    'fileStorage' => [
        'basePath' => '/tmp/'
    ]
];

// incorporate the correct environmental config file
// TODO throw error if no file is found?
$overridePath = APPLICATION_PATH . 'config/' . APPLICATION_ENV . '.php';
if (file_exists($overridePath)) {
    $config = array_merge_recursive_replace($config, require ($overridePath));
} else {
    throw new HTTPException("Fatal Exception Caught.", 500, array(
        'dev' => "Invalid Envronmental Config!  Could not load the specific config file.",
        'internalCode' => '23897293759275'
    ));
}

return new \Phalcon\Config($config);