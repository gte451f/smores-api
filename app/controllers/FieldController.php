<?php
namespace PhalconRest\Controllers;

use \PhalconRest\Util\HTTPException;
use \PhalconRest\Libraries\CustomFields\Util;

/**
 * extend from account specific controller
 *
 * @author jjenkins
 *        
 */
class FieldController extends \PhalconRest\Libraries\API\SecureFieldController
{

    public function rebuildView()
    {
        $request = $this->getDI()->get('request');
        $post = $request->getJson();
        
        // expecting a single value
        if (isset($post->view)) {
            // validate from list of possible values
            switch ($post->view) {
                case 'registrations':
                case 'attendees':
                case 'accounts':
                case 'owners':
                    // attempt to rebuild a view ... where to store the code?
                    Util::rebuildView($post->view);
                    return $this->respond([
                        [
                            'result' => true
                        ]
                    ]);
                    break;
                
                default:
                    // error, invalid view specified;
                    throw new HTTPException("Invalid view specified.", 404, array(
                        'dev' => 'Supplied view:  ' . $post->view . ' did not match any known values.',
                        'code' => '68416464684646464'
                    ));
                    break;
            }
        }
    }
}