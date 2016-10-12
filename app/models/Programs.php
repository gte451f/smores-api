<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Validation\Validator\Numericality as NumericalityValidator;


class Programs extends BaseModel
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
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var double
     */
    public $fee;

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

        $this->hasMany("id", Events::class, "program_id", ['alias' => 'Events']);
    }

    public function validation()
    {
        $validator = new Validation();

        $validator->add('fee', new NumericalityValidator([
            'allowEmpty' => true
        ]));

        $validator->add(
            'name',
            new UniquenessValidator([
                "message" => "The program name must be unique"
            ])
        );

        return $this->validate($validator);
    }
}
