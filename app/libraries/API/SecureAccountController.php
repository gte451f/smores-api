<?php
namespace PhalconRest\Libraries\API;

use \PhalconRest\Exception\HTTPException;

/**
 * A specific secure controller for "account" oriented endpoints
 * includes a single function to enable account level security if the current user is of type "owner"
 */
class SecureAccountController extends \PhalconRest\API\SecureController
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

        // pull from \PahclonRest\Libraries\API\SecureController
        // run the security service's checkUserPermissions method and thow an error if it returns false
        if (!$securityService->checkUserPermissions()) {
            // This is bad. Throw a 500. Responses should always be objects.
            throw new HTTPException("Resource not available.", 404, array(
                'dev' => 'You do not have access to the requested resource.',
                'code' => '7427655276527529'
            ));
        }
    }
}