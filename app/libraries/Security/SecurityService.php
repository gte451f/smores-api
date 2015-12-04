<?php
namespace PhalconRest\Libraries\Security;

use Phalcon\DI\Injectable;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Role;
use Phalcon\Acl as Acl;
use PhalconRest\Libraries\Security\SecureUser;

/**
 * This class serves as a wrapper for the Phalcon\Acl class.
 * It builds the acl specific
 * logic and also stores some relevant information that we will use in validating that a
 * user has access to particular resources.
 *
 * @author jking
 *        
 */
final class SecurityService extends Injectable
{
    // Phalcon\Acl object
    private $acl;
    
    // array of all routes in the system
    private $routes;

    /**
     * holds the user object
     *
     * @var object PhalconRest\Libraries\Security\SecureUser
     */
    private $user;
    
    // read, write, or delete - these will be derived from the rest verbs listed in the route
    private $requestType;
    
    // the resource that was requested i.e. /v1/appl_lists -- $requestedResource = 'appl_lists'
    private $requestedResource;

    /**
     * used by controller
     * should SecurityService apply account filter
     * @boolean
     */
    public $enforceAccountFilter = false;
    
    // array of security rules to apply. defined in a config file.
    private $security_rules;

    /**
     *
     * @return \PhalconRest\Libraries\Security\SecurityService
     */
    public function __construct()
    {
        $config = $this->getDI()->get('config');
        $this->security_rules = $config['security_rules'];
        
        $this->acl = new AclList();
        $this->acl->setDefaultAction(Acl::DENY);
        
        $this->setRoleList();
        $this->setResourceList();
        $this->setTypeAndResource();
        $this->buildAccessRules();
        $this->user = new SecureUser();
        return $this;
    }

    /**
     * set all of the roles that exist in the system
     * these should match up with those defined in security_rules.php
     */
    private function setRoleList()
    {
        $role = new Role(ADMIN_USER);
        $this->acl->addRole($role);
        
        $role = new Role(PORTAL_USER);
        $this->acl->addRole($role);
    }

    /**
     * determine all of the resources that exist in the system
     * by reading from collections
     */
    private function setResourceList()
    {
        foreach ($this->di->get('collections') as $collection) {
            $prefix = $collection->getPrefix();
            $route = $this->routes[] = str_replace('/v1/', '', $prefix);
            
            $operations = array(
                'read',
                'write',
                'delete'
            );
            
            $this->acl->addResource($route, $operations);
        }
    }

    /**
     * based on the requested route, determine and set the 'requestType' and 'requestedResource' properties
     */
    private function setTypeAndResource()
    {
        $router = $this->getDI()->get('router');
        $matchedRoute = $router->getMatchedRoute();
        $pattern = $matchedRoute->getPattern();
        $method = $matchedRoute->getHttpMethods();
        
        $this->requestType = $this->translateRestVerbs($method);
        
        $pattern = preg_replace('/\/v1\//', '', $pattern);
        $this->requestedResource = preg_replace('/\/.*/', '', $pattern);
    }

    /**
     * traslate the actual Action names in the controller to the pared down list of actions we
     * support for security - read, write, delete
     *
     * @param string $method            
     * @return string
     */
    private function translateRestVerbs($method)
    {
        switch (strtolower($method)) {
            case "get":
            case "getOne":
            case "optionsBase":
            case "optionsOne":
                return 'read';
                break;
            
            case "post":
            case "put":
            case "patch":
                return 'write';
                break;
            
            case "delete":
                return 'delete';
                break;
        }
    }

    /**
     * build the access rules baed on config define in 'config/security_rules'.
     */
    private function buildAccessRules()
    {
        if ($this->security_rules) {
            if (isset($this->security_rules['read'])) {
                $this->setAccessRules('read');
            }
            
            if (isset($this->security_rules['write'])) {
                $this->setAccessRules('write');
            }
            
            if (isset($this->security_rules['delete'])) {
                $this->setAccessRules('delete');
            }
        }
    }

    /**
     * determine if any of the user's roles give them system level access to this resource
     *
     * @return boolean
     */
    public function checkUserPermissions()
    {
        $users_groups = $this->user->getUserGroups();
        foreach ($users_groups as $group) {
            if ($this->acl->isAllowed($group, $this->requestedResource, $this->requestType)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * based on a given requestType, use security roles defined in config and set rules for that given request type
     *
     * @param unknown $requestType            
     */
    private function setAccessRules($requestType)
    {
        // set remaining permissions based on the setting in config/security_rules
        foreach ($this->security_rules[$requestType] as $role => $resourceArr) {
            foreach ($resourceArr as $resource) {
                $this->acl->allow($role, $resource, $requestType);
            }
        }
    }

    /**
     * setter method boolean determining whether matter level security will be enforced on the requested resource
     *
     * @param unknown $bool            
     */
    public function setEnforceAccountFilter($bool)
    {
        $this->enforceAccountFilter = $bool;
    }

    /**
     * getter methods
     */
    public function getAcl()
    {
        return $this->acl;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getRequestType()
    {
        return $this->requestType;
    }

    public function getRequestedResource()
    {
        return $this->requestedResource;
    }

    public function getEnforceAccountFilter()
    {
        return $this->enforceAccountFilter;
    }
}