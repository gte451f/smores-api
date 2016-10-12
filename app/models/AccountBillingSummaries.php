<?php
namespace PhalconRest\Models;

class AccountBillingSummaries extends \PhalconRest\API\BaseModel
{

    /**
     * the account id
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var double
     */
    public $charge_total;

    /**
     *
     * @var string
     */
    public $charge_created_on;

    /**
     *
     * @var int
     */
    public $charge_count;

    /**
     *
     * @var int
     */
    public $charge_days;

    /**
     *
     * @var double
     */
    public $payment_total;

    /**
     *
     * @var string
     */
    public $payment_created_on;

    /**
     *
     * @var int
     */
    public $payment_count;

    /**
     *
     * @var int
     */
    public $payment_days;

    /**
     *
     * @var double
     */
    public $total_balance;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        // set the PKID since this is a view
        $this->primaryKeyName = 'id';

        // set these since plural is slightly non-standard
        $this->pluralName = 'AccountBillingSummaries';
        $this->singularName = 'AccountBillingSummary';

        $this->pluralTableName = 'account_billing_summaries';
        $this->singularTableName = 'account_billing_summary';

        parent::initialize();
        $this->belongsTo("id", Accounts::class, "id", ['alias' => 'Accounts']);
    }
}
