<?php
namespace PhalconRest\Controllers;

use PhalconRest\Libraries\API\SecureController;

/**
 * Class StatementBatchController
 * @package PhalconRest\Controllers
 */
class StatementBatchController extends SecureController
{

    /**
     * set names since plural is slightly different
     */
    public function onConstruct()
    {
        $this->pluralName = 'StatementBatches';
        $this->singularName = 'StatementBatch';
        parent::onConstruct();
    }
}