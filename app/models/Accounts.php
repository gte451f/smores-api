<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Behavior\Timestampable as Timestampable;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;

class Accounts extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $notes;

    /**
     *
     * @var string
     */
    public $created_on;

    /**
     *
     * @var string
     */
    public $updated_on;

    /**
     * the payment processors PKID
     * 
     * @var string
     */
    public $external_id;

    /**
     *
     * 0=Inactive | 1=Active
     *
     * @var int
     */
    public $active;

    /**
     */
    public function initialize()
    {
        $this->hasMany("id", "PhalconRest\Models\Owners", "account_id", array(
            'alias' => 'Owners'
        ));
        $this->hasMany("id", "PhalconRest\Models\Attendees", "account_id", array(
            'alias' => 'Attendees'
        ));
        $this->hasMany("id", "PhalconRest\Models\AccountAddrs", "account_id", array(
            'alias' => 'AccountAddrs'
        ));
        $this->hasMany("id", "PhalconRest\Models\Checks", "account_id", array(
            'alias' => 'Checks'
        ));
        $this->hasMany("id", "PhalconRest\Models\Cards", "account_id", array(
            'alias' => 'Cards'
        ));
        $this->hasMany("id", "PhalconRest\Models\Payments", "account_id", array(
            'alias' => 'Payments'
        ));
        $this->hasMany("id", "PhalconRest\Models\Charges", "account_id", array(
            'alias' => 'Charges'
        ));
    }

    /**
     * set created_on when inserting
     */
    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d');
        
        // all accounts start as "Inactive" and require activation
        $this->active = 0;
    }

    /**
     * set updated on before updating
     */
    public function beforeValidationOnUpdate()
    {
        $this->updated_on = date('Y-m-d');
    }

    /**
     * validate that notes isn't too long
     */
    public function validation()
    {
        $this->validate(new StringLengthValidator(array(
            "field" => 'notes',
            'max' => 500,
            'min' => 0,
            'messageMaximum' => 'Notes field is too long, please enter a value less than 500 characters in length',
            'messageMinimum' => 'This should never display'
        )));
        
        return $this->validationHasFailed() != true;
    }
}
