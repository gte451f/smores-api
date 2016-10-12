<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;
use Phalcon\Validation\Validator\InclusionIn as InclusionInValidator;

;

class OwnerNumbers extends BaseModel
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
    public $user_id;

    /**
     *
     * @var string
     */
    public $phone_type;

    /**
     *
     * @var integer
     */
    public $primary;

    /**
     *
     * @var string
     */
    public $number;

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
        $this->belongsTo("owner_id", Owners::class, "user_id", ['alias' => 'Owners']);
    }

    /**
     * validate number data
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'phone_type',
            new InclusionInValidator([
                'domain' => array(
                    'Mobile',
                    'Office',
                    'Home',
                    'Other'
                )
            ])
        );

        $validator->add(
            'number',
            new StringLengthValidator([
                'max' => 15,
                'min' => 10,
                'messageMaximum' => 'A phone number must be less than 15',
                'messageMinimum' => 'A phone number must be greater than 9'
            ])
        );

        return $this->validate($validator);
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'user_id' => 'owner_id',
            'phone_type' => 'phone_type',
            'primary' => 'primary',
            'number' => 'number'
        );
    }
}
