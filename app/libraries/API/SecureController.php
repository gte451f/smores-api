<?php
namespace PhalconRest\Libraries\API;

use \PhalconRest\API\SecureController as APISecureController;
use \PhalconRest\Exception\HTTPException;

/**
 * This class extends the PhalconRest\API\SecureController and adds application specific security logic
 */
class SecureController extends APISecureController
{

    /**
     * Determine whether the user can access the requested resource
     *
     * @return bool
     * @param object $securityService
     * @throws HTTPException
     */
    protected function securityCheck($securityService)
    {
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