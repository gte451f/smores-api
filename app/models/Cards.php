<?php
namespace PhalconRest\Models;


use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;
use Phalcon\Validation\Validator\InclusionIn as InclusionInValidator;

class Cards extends BaseModel
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
        $this->belongsTo("account_id", Accounts::class, "id", ['alias' => 'Accounts']);
        $this->hasMany("id", Payments::class, "card_id", ['alias' => 'Payments']);
    }

    /**
     * set any default values before we create a new record
     */
    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d H:i:s');
    }


    /**
     * validate fields
     *
     * @return bool
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'name_on_card',
            new StringLengthValidator([
                'min' => 2,
                'max' => 45,
                'messageMinimum' => 'Name on card is to short, it must be at least 2 characters long.',
                'messageMaximum' => 'Name on card is to long, it must be shorter than 45 characters long'
            ])
        );

        $validator->add(
            'vendor',
            new InclusionInValidator([
                'message' => 'Card vendor must be one of the following: American Express, Visa or Master Card',
                'domain' => [
                    "amex",
                    "visa",
                    "mastercard",
                    "discover",
                    'dinersclub',
                    'jcb'
                ]
            ])
        );

        return $this->validationHasFailed() != true;
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
