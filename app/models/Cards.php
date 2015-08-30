<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class Cards extends \PhalconRest\API\BaseModel
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
     * @var string
     */
    public $created_on;

    /**
     *
     * @var integer
     */
    public $allow_reoccuring;

    /**
     *
     * @var string
     */
    public $expiration_month;

    /**
     *
     * @var string
     */
    public $expiration_year;

    /**
     *
     * @var string
     */
    public $name_on_card;

    /**
     *
     * @var string
     */
    public $number;

    /**
     *
     * @var string
     */
    public $vendor;

    /**
     *
     * @var integer
     */
    public $is_debit;

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
        
        $this->hasMany("id", "PhalconRest\Models\Payments", "card_id", array(
            'alias' => 'Payments'
        ));
    }

    /**
     * set any default values before we create a new record
     */
    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d H:i:s');
    }

    public function validation()
    {
        // make sure this credit card number isn't already in the table
        $this->validate(new Uniqueness(array(
            "field" => 'number'
        )));
        
        $this->validate(new StringLengthValidator(array(
            'field' => 'name_on_card',
            'min' => 2,
            'max' => 45,
            'messageMinimum' => 'Name on card is to short, it must be at least 2 characters long.',
            'messageMaximum' => 'Name on card is to long, it must be shorter than 45 characters long'
        )));
        
        $this->validate(new InclusionIn(array(
            "field" => 'vendor',
            'message' => 'Card vendor must be one of the following: American Express, Visa or Master Card',
            'domain' => [
                "American Express",
                "Visa",
                "Master Card"
            ]
        )));
        
        return $this->validationHasFailed() != true;
    }
}
