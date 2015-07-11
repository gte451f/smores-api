<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator;
use Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;

class OwnerNumbers extends \PhalconRest\API\BaseModel
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
     * validatoni owern data
     */
    public function validation()
    {
        $this->validate(new InclusionInValidator(array(
            'field' => 'phone_type',
            'domain' => array(
                'Mobile',
                'Office',
                'Home',
                'Other'
            )
        )));
        
        $this->validate(new StringLengthValidator(array(
            "field" => 'number',
            'max' => 15,
            'min' => 10,
            'messageMaximum' => 'A phone number must be less than 15',
            'messageMinimum' => 'A phone number must be greater than 9'
        )));
        
        return $this->validationHasFailed() != true;
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
        )
        ;
    }
}
