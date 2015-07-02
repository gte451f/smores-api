<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;

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
     * Employee|Attendee|Owner
     *
     * @var string
     */
    public $user_type;

    /**
     * Male|Female
     *
     * @var string
     */
    public $gender;

    /**
     * encrypted
     *
     * @var string
     */
    public $password;

    /**
     *
     * used to reset passwords, activate records
     *
     * @var string
     */
    public $code;

    /**
     * status type field
     * Inactive | Active | Archived (soft delete)
     *
     * @var string
     */
    public $active;

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
        
        $this->blockColumns = array(
            'password'
        );
        
        $this->hasOne('id', 'PhalconRest\Models\Employees', 'user_id', array(
            'alias' => 'Employees'
        ));
        
        $this->hasOne('id', 'PhalconRest\Models\Owners', 'user_id', array(
            'alias' => 'Owners'
        ));
        
        $this->hasOne('id', 'PhalconRest\Models\Attendees', 'user_id', array(
            'alias' => 'Attendees'
        ));
    }

    /**
     * set some default values before we create a new user record
     */
    public function beforeValidationOnCreate()
    {
        
        // all user accounts start as invactive and must be activated
        $this->active = 'Inactive';
        
        // all user accounts have this type
        // this is not true!
        // $this->user_type = 'Employee';
        
        // encrypt password if one is provided
        // from the existance of a password we infer that it is either an owner or an employee
        // of course we could just watch the user_type field right?
        if (strlen($this->password) >= 8) {
            $security = $this->getDI()->get('security');
            $this->password = $security->hash($this->password);
            // also set a code when we infer a password
            $this->code = substr(md5(rand()), 0, 45);
            $this->active = 'Inactive';
        }
    }

    /**
     * set some default values before we create a new employee record
     */
    public function beforeValidationOnUpdate()
    {
        // only update the password if a new one is provided that fits some basic criteria
        // TODO shouldn't this be afterValidation?
        if (strlen($this->password) >= 8 and strlen($this->password) !== 60) {
            // encrypt password
            $security = $this->getDI()->get('security');
            $this->password = $security->hash($this->password);
        }
    }

    /**
     * validate various fields
     */
    public function validation()
    {
        // check for valid email
        $this->validate(new Email(array(
            'field' => 'email',
            'required' => true
        )));
        
        // check length for first/last namespace
        $this->validate(new StringLengthValidator(array(
            "field" => 'last_name',
            'max' => 45,
            'min' => 4,
            'messageMaximum' => 'Last Name should be less than 45 characters in length',
            'messageMinimum' => 'Last Name should be greater than 3 characters in length'
        )));
        
        // check length for first/last namespace
        $this->validate(new StringLengthValidator(array(
            "field" => 'first_name',
            'max' => 45,
            'min' => 3,
            'messageMaximum' => 'First Name should be less than 45 characters in length',
            'messageMinimum' => 'First Name should be greater than 2 characters in length'
        )));
        
        $this->validate(new InclusionInValidator(array(
            'field' => 'gender',
            'domain' => array(
                'Male',
                'Female'
            )
        )));
        
        return $this->validationHasFailed() != true;
    }
}
