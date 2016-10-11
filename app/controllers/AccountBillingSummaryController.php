<?php
namespace PhalconRest\Controllers;

use PhalconRest\Libraries\API\SecureAccountController;

/**
 * Class AccountBillingSummaryController
 * @package PhalconRest\Controllers
 *
 * extend from account specific controller
 */
class AccountBillingSummaryController extends SecureAccountController
{

    /**
     * set names since plural is slightly different
     */
    public function onConstruct()
    {
        $this->pluralName = 'AccountBillingSummaries';
        $this->singularName = 'AccountBillingSummary';
        parent::onConstruct();
    }
}