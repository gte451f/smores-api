<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\InclusionIn as InclusionInValidator;
use Phalcon\Validation\Validator\Numericality as NumericalityValidator;


class Owners extends BaseModel
{

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $account_id;

    /**
     * 0 | 1
     *
     * @var integer
     */
    public $primary_contact;

    /**
     * Mother|Father|Grand Parent|Other|Guardian
     *
     * @var string
     */
    public $relationship;

    /**
     * this model's parent model
     *
     * @var string
     */
    public static $parentModel = 'Users';

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
        $this->hasOne("user_id", Users::class, "id", ['alias' => 'Users']);
        $this->belongsTo('account_id', Accounts::class, 'id', ['alias' => 'Accounts']);
        $this->hasMany("user_id", OwnerNumbers::class, "owner_id", ['alias' => 'OwnerNumbers']);
    }

    /**
     * set primary_created to a default value
     */
    public function beforeValidationOnCreate()
    {
        if (!isset($this->primary_contact)) {
            $this->primary_contact = 0;
        }
    }


    /**
     * validate owner data
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(['primary_contact'], new NumericalityValidator([
            'message' => [
                'message' => 'Primary Contact should be true or false.',
            ]
        ]));

        $validator->add('relationship', new InclusionInValidator([
            'domain' => [
                'Mother',
                'Father',
                'Guardian',
                'Grand Parent',
                'Other'
            ]
        ]));

        return $this->validate($validator);
    }
}
