<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\Numericality as NumericalityValidator;

class Fees extends \PhalconRest\API\BaseModel
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
        $this->validate(new NumericalityValidator(array(
            'field' => 'amount'
        )));

        $this->validate(new Uniqueness(array(
            "field" => "name",
            "message" => "The fee name must be unique"
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
