<?php
namespace PhalconRest\Controllers;

use PhalconRest\Libraries\API\SecureController;

/**
 * Class BatchController
 * @package PhalconRest\Controllers
 */
class BatchController extends SecureController
{
    /**
     * set names since plural is slightly different
     */
    public function onConstruct()
    {
        $this->pluralName = 'Batches';
        $this->singularName = 'Batch';
        parent::onConstruct();
    }
}