<?php
namespace PhalconRest\Models;

class Charges extends \PhalconRest\API\BaseModel
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
     * @var integer
     */
    public $request_id;

    /**
     *
     * @var integer
     */
    public $registration_id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $fee_id;

    /**
     *
     * @var double
     */
    public $amount;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $created_on;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo("account_id", "PhalconRest\\Models\\Accounts", "id", array(
            'alias' => 'Accounts'
        ));

        $this->belongsTo("fee_id", "PhalconRest\\Models\\Fees", "id", array(
            'alias' => 'Fees'
        ));
    }

    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d H:i:s');
    }
}
