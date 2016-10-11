<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;


class Accounts extends BaseModel
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
    public $name;

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
        $this->hasMany("id", Owners::class, "account_id", ['alias' => 'Owners']);
        $this->hasMany("id", Attendees::class, "account_id", ['alias' => 'Attendees']);
        $this->hasMany("id", AccountAddrs::class, "account_id", ['alias' => 'AccountAddrs']);
        $this->hasMany("id", Checks::class, "account_id", ['alias' => 'Checks']);
        $this->hasMany("id", Cards::class, "account_id", ['alias' => 'Cards']);
        $this->hasMany("id", Payments::class, "account_id", ['alias' => 'Payments']);
        $this->hasMany("id", Charges::class, "account_id", ['alias' => 'Charges']);
        $this->hasOne('id', CustomAccountFields::class, 'account_id', ['alias' => 'CustomAccountFields']);
    }

    /**
     * set created_on when inserting
     */
    public function beforeValidationOnCreate()
    {
        if (!isset($this->created_on)) {
            $this->created_on = date('Y-m-d');
        }

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
     * validate various fields
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add('notes', new StringLengthValidator([
            'max' => 500,
            'min' => 0,
            'messageMaximum' => 'Notes field is too long, please enter a value less than 500 characters in length',
            'messageMinimum' => 'This should never display',
            'allowEmpty' => true
        ]));

        $validator->add('name', new StringLengthValidator([
            'max' => 45,
            'min' => 3,
            'messageMaximum' => 'Name field is too long, please enter a value less than 45 characters in length',
            'messageMinimum' => 'Name field is too short, please enter a value more than three characters'
        ]));

        $validator->add(
            'name',
            new UniquenessValidator([
                "message" => "The Account name must be unique"
            ])
        );

        return $this->validate($validator);
    }

    /**
     * dynamic toggle fields based on who is asking
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns($withParents = true)
    {
        $blockColumns = [];
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();

        if ($currentUser->userType != 'Employee') {
            $blockColumns[] = 'external_id';
        }
        $this->setBlockColumns($blockColumns, true);
    }
}
