<?php
namespace PhalconRest\Libraries\API;

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
        // check to see whether the controller has set the security_service to enforce account level security for this resource
        $securityService = $this->getDI()->get('securityService');
        
        if ($securityService->getEnforceAccountFilter()) {
            $this->applyAccountFilter($query);
        }
        
        return $query;
    }

    /**
     * This is a method which is called form the afterQueryBuilderHook.
     * This method gives us a specific place to put all account related filters logic in child Entity classes.
     * By default it assumes the primary model contains an account id and filters for the current user's
     *
     * @param
     *            phalcon query object
     *            
     * @return phalcon query object
     */
    public function applyAccountFilter($query)
    {
        // load current account
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();
        
        // figure out best way to filter by default
        $model = $this->model->getModelNamespace();
        switch ($model) {
            case 'PhalconRest\Models\Accounts':
                $id = 'id';
                break;
            
            default:
                $id = 'account_id';
                break;
        }
        
        // apply generic account filter assuming column is present in query
        $query->where("$model.$id = $currentUser->accountId");
        return $query;
    }
}