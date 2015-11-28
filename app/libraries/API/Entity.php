<?php
namespace PhalconRest\Libraries\Core;

use \PhalconRest\Util\HTTPException;

/**
 * This class extends the PhalconRest\API\Entity class and adds app specific logic *
 *
 * @author jking
 *        
 */
class Entity extends \PhalconRest\API\Entity
{

    /**
     * process injected model
     *
     * @param \PhalconRest\API\BaseModel $model            
     */
    public function __construct(\PhalconRest\API\BaseModel $model, \PhalconRest\API\SearchHelper $searchHelper)
    {
        $di = \Phalcon\DI::getDefault();
        $this->di = $di;
        
        // get the security service
        $this->security_service = $this->getDI()->get('securityService');
        
        parent::__construct($model, $searchHelper);
    }

    /**
     * This is a method that hooks into the PhalconRest\API\Entity::queryBuilder method right before it returns the query
     * object.
     * This gives us an oportunity to alter the object however we choose before it is returned to be processed.
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::afterQueryBuilderHook()
     */
    public function afterQueryBuilderHook($query)
    {
        // check to see whether the controller has set the security_service to enforce matter level security for this resource
        if ($this->security_service->getEnforceMatterLevelPermissions()) {
            $this->applyMatterSecurityFilter($query);
        }
        
        return $query;
    }

    /**
     * This is a method which is called form the afterQueryBuilderHook.
     * This method gives us a specific place to put all matter level security
     * logic in child Entity classes. It will simply return the $query object if it is not defined in Entity class which extend \PhalconRest\Libraries\Core\Entity
     *
     * @param
     *            phalcon query object
     * @return phalcon query object
     */
    public function applyMatterSecurityFilter($query)
    {
        return $query;
    }
}