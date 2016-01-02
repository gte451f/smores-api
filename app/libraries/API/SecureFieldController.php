<?php
namespace PhalconRest\Libraries\API;

use \PhalconRest\Util\HTTPException;
use PhalconRest\API\SecureController;

/**
 * A specific secure controller for "account" oriented endpoints
 * includes a single function to enable account level security if the current user is of type "owner"
 */
class SecureFieldController extends \PhalconRest\API\SecureController
{

    public function __construct($parseQueryString = true)
    {
        // allow through basic fields request, secure the rest
        if ($this->request->isGet()) {
            $config = $this->getDI()->get('config');
            
            $uri = $this->request->getURI();
            
            // TODO Hard coded?
            if ('/v1/fields' == $this->request->getURI()) {
                // replace grand parent class
                $di = \Phalcon\DI::getDefault();
                $this->setDI($di);
                // initialize entity and set to class property
                $this->getEntity();
                return;
            }
        }
        
        return parent::__construct($parseQueryString);
    }
}