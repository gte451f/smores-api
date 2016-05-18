<?php
namespace PhalconRest\Libraries\API;

use \PhalconRest\Util\HTTPException;

/**
 * This class extends the PhalconRest\API\SecureController and adds application specific security logic
 */
class SecureController extends \PhalconRest\API\SecureController
{

    /**
     * Determine whether the user can access the requested resource
     *
     * @return bool
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