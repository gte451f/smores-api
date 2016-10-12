<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Validation\Validator\Numericality as NumericalityValidator;

class Fees extends BaseModel
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
    public $amount;

    /**
     *
     * @var string
     */
    public $basis;

    /**
     *
     * @var string
     */
    public $payment_schedule;

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'amount',
            new NumericalityValidator([])
        );

        $validator->add(
            'name',
            new UniquenessValidator([
                "message" => "The fee name must be unique"
            ])
        );

        return $this->validate($validator);
    }
}
