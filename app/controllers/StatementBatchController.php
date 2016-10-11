<?php
namespace PhalconRest\Controllers;

class StatementBatchController extends \PhalconRest\Libraries\API\SecureController
{

    /**
     * set names since plural is slightly different
     *
     * @param bool $parseQueryString
     */
    public function __construct($parseQueryString = true)
    {
        $this->pluralName = 'StatementBatches';
        $this->singularName = 'StatementBatch';
        parent::__construct($parseQueryString);
    }
}