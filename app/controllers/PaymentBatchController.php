<?php
namespace PhalconRest\Controllers;

class PaymentBatchController extends \PhalconRest\Libraries\API\SecureController
{

    /**
     * set names since plural is slightly different
     *
     * @param string $parseQueryString            
     */
    public function __construct($parseQueryString = true)
    {
        $this->pluralName = 'PaymentBatches';
        $this->singularName = 'PaymentBatch';
        parent::__construct($parseQueryString);
    }
}