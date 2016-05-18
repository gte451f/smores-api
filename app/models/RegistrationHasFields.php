<?php
namespace PhalconRest\Models;

class RegistrationHasFields extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $registration_id;

    /**
     *
     * @var integer
     */
    public $field_id;

    /**
     *
     * @var string
     */
    public $value;

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
        $this->hasOne("registration_id", "PhalconRest\\Models\\Registration", "id", array(
            'alias' => 'Registrations'
        ));

        $this->belongsTo('field_id', 'PhalconRest\Models\Fields', 'id', array(
            'alias' => 'Fields'
        ));
    }
}
