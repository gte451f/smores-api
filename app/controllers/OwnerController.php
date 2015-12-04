<?php
namespace PhalconRest\Controllers;

class OwnerController extends \PhalconRest\Libraries\API\SecureController
{

    /**
     * determine if resulting list should be filtered by current user's account #
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\Libraries\API\SecureController::securityCheck()
     */
    protected function securityCheck($securityService)
    {
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();
        
        if ($currentUser->userType == 'Owner') {
            $securityService->setEnforceAccountFilter(true);
        }
        
        return parent::securityCheck($securityService);
    }
}