<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator\Email as Email;

class Users extends \PhalconRest\API\BaseModel
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
    public $first_name;

    /**
     *
     * @var string
     */
    public $last_name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $dob;

    /**
     * Validations and business logic
     */
    public function validation()
    {
        $this->validate(new Email(array(
            'field' => 'email',
            'required' => true
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
