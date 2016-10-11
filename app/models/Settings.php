<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;

class Settings extends BaseModel
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $value;

    /**
     *
     * @var string
     */
    public $help;

    /**
     * validate various fields
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add('value', new StringLengthValidator([
            'max' => 255,
            'min' => 4,
            'messageMaximum' => 'Value field is too long, please enter a value less than 255 characters in length',
            'messageMinimum' => 'A Value must be at least one character long',
            'allowEmpty' => true
        ]));

        $validator->add('name', new StringLengthValidator([
            'max' => 45,
            'min' => 4,
            'messageMaximum' => 'Name field is too long, please enter a value less than 45 characters in length',
            'messageMinimum' => 'A Name must be at least one character long',
            'allowEmpty' => true
        ]));


        $validator->add(
            'name',
            new UniquenessValidator([
                "message" => "The Name field must be unique"
            ])
        );

        return $this->validate($validator);
    }


}


