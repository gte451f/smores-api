<?php
namespace PhalconRest\Models;


use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;
use Phalcon\Validation\Validator\InclusionIn as InclusionInValidator;


class Users extends BaseModel
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
     * 0=Inactive | 1=Active | 2=Password Reset
     *
     * @var int
     */
    public $active;

    /**
     *
     * @var string
     */
    public $token;

    /**
     *
     * @var string
     */
    public $token_created_on;

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

        // all employees or owner accounts start as invactive
        // if they wish to login to either admin or portal, they must be activated
        if ($this->user_type == 'Employee' or $this->user_type == 'Owner') {
            $this->active = 0;
        }

        // all user accounts have this type
        // this is not true!
        // $this->user_type = 'Employee';

        // encrypt password if one is provided
        // from the existence of a password we infer that it is either an owner or an employee
        // of course we could just watch the user_type field right?
        if (strlen($this->password) >= 8) {
            $security = $this->getDI()->get('security');
            $this->password = $security->hash($this->password);
            // also set a code when we infer a password
            $this->code = substr(md5(rand()) . md5(rand()), 0, 45);

            // why set the account to inactive when the password changes?
            // is this a specific password reset? THen do this in the auth controller
            // $this->active = 0;
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
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator([
                'model' => $this,
                'message' => 'Please enter a valid email address',
                'allowEmpty' => true
            ])
        );

        $validator->add(
            'email',
            new UniquenessValidator([
                'allowEmpty' => 'true'
            ])
        );

        // check length for first/last namespace
        $validator->add('last_name', new StringLengthValidator([
            'max' => 45,
            'min' => 2,
            'messageMaximum' => 'Last Name should be less than 45 characters in length',
            'messageMinimum' => 'Last Name should be greater than 2 characters in length'
        ]));

        $validator->add('first_name', new StringLengthValidator([
            'max' => 45,
            'min' => 2,
            'messageMaximum' => 'Last Name should be less than 45 characters in length',
            'messageMinimum' => 'Last Name should be greater than 2 characters in length'
        ]));

        $validator->add('gender', new InclusionInValidator([
            'domain' => [
                'Male',
                'Female'
            ]
        ]));

        return $this->validate($validator);
    }

    /**
     * dynamic toggle fields based on who is asking
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns($withParents = true)
    {
        $blockColumns = [
            'password',
            'code',
            'token',
            'token_created_on'
        ];
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();

        if ($currentUser->userType != 'Employee') {
            // $blockColumns[] = 'dob';
            // $blockColumns[] = 'email';
            $blockColumns[] = 'active';
        }
        $this->setBlockColumns($blockColumns, true);
    }
}
