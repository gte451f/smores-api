<?php
namespace PhalconRest\Controllers;

use PhalconRest\Exception\HTTPException;
use PhalconRest\Libraries\CustomFields\Util;
use PhalconRest\Libraries\API\SecureController;

/**
 * Class FieldController
 * @package PhalconRest\Controllers
 *
 * extend from account specific controller
 */
class FieldController extends SecureController
{

    /**
     * allow public requests to the fields end point
     * blocking this causes bugs when users attempt to load the app and this data seems harmless to show
     *
     */
    public function onConstruct()
    {
        // allow through basic fields request, secure the rest
        if ($this->request->isGet()) {
            $config = $this->getDI()->get('config');

            $uri = $this->request->getURI();

            // TODO Hard coded?
            if ($config['application']['baseUri'] . 'fields' == $this->request->getURI()) {
                // replace grand parent class
                $di = \Phalcon\DI::getDefault();
                $this->setDI($di);
                // initialize entity and set to class property
                $this->getEntity();
                return;
            }
        }
        parent::onConstruct();
    }

    /**
     * support custom action to rebuild various field related views
     *
     * @throws HTTPException
     */
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
                        ['result' => true]
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