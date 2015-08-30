<?php
namespace PhalconRest\Models;

use Phalcon\Validation\Validator\Between;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;
use Phalcon\Mvc\Model\Validator\InclusionIn;

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
     * @var integer
     */
    public $card_id;

    /**
     *
     * @var integer
     */
    public $check_id;

    /**
     *
     * @var string
     */
    public $external_id;

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

    /**
     *
     * @var string
     */
    public $mode;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo("account_id", "PhalconRest\Models\Accounts", "id", array(
            'alias' => 'Accounts'
        ));
        
        $this->belongsTo("check_id", "PhalconRest\Models\Checks", "id", array(
            'alias' => 'Checks'
        ));
        
        $this->belongsTo("card_id", "PhalconRest\Models\Cards", "id", array(
            'alias' => 'Cards'
        ));
    }

    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d H:i:s');
    }

    public function validation()
    {
        $this->validation(new Between(array(
            'field' => 'amount',
            'minimum' => 0,
            'maximum' => 10000,
            'message' => 'The payment must be between 0 and 10000'
        )));
        
        $this->validate(new InclusionIn(array(
            "field" => 'mode',
            'message' => 'Payment must be a specific value from the list.',
            'domain' => [
                "card",
                "check",
                "cash",
                'discount'
            ]
        )));
        
        return $this->validationHasFailed() != true;
    }
}
