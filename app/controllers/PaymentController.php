<?php
namespace PhalconRest\Controllers;

/**
 * extend from account specific controller
 *
 * @author jjenkins
 *        
 */
class PaymentController extends \PhalconRest\Libraries\API\SecureAccountController
{

    /**
     * custom action to save a one time credit card payment
     * differs from a regular payment in that a full credit card is supplied and used to make a single payment
     * no credit card data is stored in our system
     * 3rd party gets data for processing only
     *
     * @return mixed return valid Apache code, could be an error, maybe not
     */
    public function post()
    {
        $request = $this->getDI()->get('request');
        $post = $request->getJson($this->getControllerName('singular'));
        
        // maybe hand to the library first?
        
        // then run the rest of a normal post action
        
        // This record only must be created
        $id = $this->entity->save($post);
        
        // now fetch the record so we can return it
        $search_result = $this->entity->findFirst($id);
        
        if ($search_result == false) {
            // This is bad. Throw a 500. Responses should always be objects.
            throw new HTTPException("There was an error retreiving the newly created record.", 500, array(
                'dev' => 'The resource you requested is not available after it was just created',
                'code' => '1238510381861'
            ));
        } else {
            return $this->respond($search_result);
        }
    }
}