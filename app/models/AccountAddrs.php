<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;
use Phalcon\Validation\Validator\InclusionIn as InclusionInValidator;

class AccountAddrs extends BaseModel
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
    public $billing;

    /**
     *
     * @var integer
     */
    public $mailing;

    /**
     *
     * @var string
     */
    public $addr_1;

    /**
     *
     * @var string
     */
    public $addr_2;

    /**
     *
     * @var string
     */
    public $city;

    /**
     *
     * @var string
     */
    public $state;

    /**
     *
     * @var string
     */
    public $country;

    /**
     *
     * @var string
     */
    public $zip;

    /**
     * define custom model relationships
     *
     * (non-PHPdoc)
     *
     * @see extends \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();

        $this->belongsTo('account_id', Accounts::class, 'id', ['alias' => 'Accounts']);
    }

    /**
     * validate various fields
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator([
                'model' => $this,
                'message' => 'Please enter a valid email address',
                'allowEmpty' => true
            ])
        );

        return $this->validate($validator);
    }

}
