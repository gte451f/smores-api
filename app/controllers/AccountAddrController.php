<?php
namespace PhalconRest\Controllers;

use PhalconRest\Libraries\API\SecureController;

/**
 * Class AccountAddrController
 * @package PhalconRest\Controllers
 */
class AccountAddrController extends SecureController
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