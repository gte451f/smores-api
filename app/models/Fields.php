<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;
use Phalcon\Validation\Validator\InclusionIn as InclusionInValidator;

class Fields extends BaseModel
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
        $this->belongsTo("registration_id", Registrations::class, "id", ['alias' => 'Registrations']);
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


    /**
     *
     */
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
     * validate field data
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'table',
            new InclusionInValidator([
                'domain' => [
                    'registrations',
                    'accounts',
                    'attendees',
                    'owners'
                ]
            ])
        );

        $validator->add(
            'allowed_data',
            new InclusionInValidator([
                'domain' => [
                    'string',
                    'number',
                    'utcdate',
                    'boolean'
                ]
            ])
        );

        $validator->add(
            'input',
            new InclusionInValidator([
                'domain' => [
                    'text',
                    'textarea',
                    'select',
                    'radio',
                    'single-check',
                    'multi-check',
                    'date'
                ]
            ])
        );


        $validator->add(
            'private',
            new InclusionInValidator([
                'domain' => [
                    1,
                    0
                ]
            ])
        );


        $validator->add(
            'name',
            new UniquenessValidator([
                "message" => "Display Name must be unique from all other custom fields"
            ])
        );


        $validator->add(
            'display',
            new StringLengthValidator([
                'max' => 35,
                'min' => 4,
                'messageMaximum' => 'Display name is too long, please enter a value less than 35 characters in length',
                'messageMinimum' => 'Display name is too short, please enter a value 4 characters or greater in length'
            ])
        );

        return $this->validate($validator);
    }
}
