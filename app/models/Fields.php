<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator;
use Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Fields extends \PhalconRest\API\BaseModel
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
    public $display;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $input;

    /**
     *
     * @var string
     */
    public $table;

    /**
     *
     * @var string
     */
    public $allowed_data;

    /**
     *
     * @var string
     */
    public $possible_value;

    /**
     *
     * @var int (boolean)
     */
    public $private;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();

        $this->belongsTo("registration_id", "PhalconRest\\Models\\Registrations", "id", array(
            'alias' => 'Registrations'
        ));
    }

    /**
     * auto detect a value
     */
    public function beforeValidationOnCreate()
    {
        $this->detectName();
    }

    /**
     * auto detect a value
     */
    public function beforeValidationOnUpdate()
    {
        $this->detectName();
    }

    private function detectName()
    {
        // change space to underscore
        $value = str_replace(' ', '_', $this->display);
        // change hyphen to underscore
        $value = str_replace('-', '_', $value);

        // Remove all special characters a
        $value = preg_replace("/[^A-Za-z0-9\_]/", "", $value);
        $value = trim($value);

        // change multiple underscores to single underscore
        $value = str_replace('__', '_', $value);
        // just in case there were three
        $value = str_replace('__', '_', $value);

        // all accounts start as "Inactive" and require activation
        $this->name = strtolower($value);
    }

    /**
     * validation owener data
     */
    public function validation()
    {
        $this->validate(new InclusionInValidator(array(
            'field' => 'table',
            'domain' => array(
                'registrations',
                'accounts',
                'attendees',
                'owners'
            )
        )));

        $this->validate(new InclusionInValidator(array(
            'field' => 'allowed_data',
            'domain' => array(
                'string',
                'number',
                'utcdate',
                'boolean'
            )
        )));

        $this->validate(new InclusionInValidator(array(
            'field' => 'input',
            'domain' => array(
                'text',
                'textarea',
                'select',
                'radio',
                'single-check',
                'multi-check',
                'date'
            )
        )));

        $this->validate(new InclusionInValidator(array(
            'field' => 'private',
            'domain' => array(
                1,
                0
            )
        )));

        $this->validate(new Uniqueness(array(
            "field" => "name",
            "message" => "Display Name must be unique from all other custom fields"
        )));

        $this->validate(new StringLengthValidator(array(
            "field" => 'display',
            'max' => 35,
            'min' => 4,
            'messageMaximum' => 'Display name is too long, please enter a value less than 35 characters in length',
            'messageMinimum' => 'Display name is too short, please enter a value 4 characters or greater in length'
        )));

        return $this->validationHasFailed() != true;
    }
}
