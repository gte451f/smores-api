<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class Charges extends BaseModel
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
        $this->belongsTo("account_id", Accounts::class, "id", ['alias' => 'Accounts']);
        $this->belongsTo("fee_id", Fees::class, "id", ['alias' => 'Fees']);
    }

    public function beforeValidationOnCreate()
    {
        if (!isset($this->created_on)) {
            $this->created_on = date('Y-m-d H:i:s');
        }
        return $this->validationHasFailed() != true;
    }
}
