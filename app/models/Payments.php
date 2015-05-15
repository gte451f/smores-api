<?php
namespace PhalconRest\Models;

class Payments extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $account_id;

    /**
     *
     * @var string
     */
    public $external_id;

    /**
     *
     * @var integer
     */
    public $charge_id;

    /**
     *
     * @var integer
     */
    public $fund_id;

    /**
     *
     * @var string
     */
    public $created_on;

    /**
     *
     * @var string
     */
    public $settled_on;

    /**
     *
     * @var double
     */
    public $amount;
}
