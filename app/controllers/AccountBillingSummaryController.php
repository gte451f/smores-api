<?php
namespace PhalconRest\Controllers;

/**
 * extend from account specific controller
 *
 * @author jjenkins
 *        
 */
class AccountBillingSummaryController extends \PhalconRest\Libraries\API\SecureAccountController
{

    /**
     * set names since plural is slightly different
     *
     * @param string $parseQueryString            
     */
    public function __construct($parseQueryString = true)
    {
        $this->pluralName = 'AccountBillingSummaries';
        $this->singularName = 'AccountBillingSummary';
        parent::__construct($parseQueryString);
    }
}