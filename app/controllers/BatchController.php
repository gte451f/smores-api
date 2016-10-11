<?php
namespace PhalconRest\Controllers;

class BatchController extends \PhalconRest\Libraries\API\SecureController
{
    /**
     * set names since plural is slightly different
     *
     * @param boolean $parseQueryString
     */
    public function __construct($parseQueryString = true)
    {
        $this->pluralName = 'Batches';
        $this->singularName = 'Batch';
        parent::__construct($parseQueryString);
    }
}