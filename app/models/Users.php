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
     * employee|attendee|owner
     * @var string
     */
    public $user_type;
    

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

    }
}
