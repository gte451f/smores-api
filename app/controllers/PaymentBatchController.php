<?php
namespace PhalconRest\Controllers;

use PhalconRest\Libraries\API\SecureController;

class PaymentBatchController extends SecureController
{

    /**
     * set names since plural is slightly different
     *
     * @param string $parseQueryString
     */
    public function onConstruct()
    {
        $this->pluralName = 'PaymentBatches';
        $this->singularName = 'PaymentBatch';
        parent::onConstruct();
    }
}