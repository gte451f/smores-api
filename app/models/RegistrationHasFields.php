<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class RegistrationHasFields extends BaseModel
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
        $this->hasOne("registration_id", Registration::class, "id", ['alias' => 'Registrations']);
        $this->belongsTo('field_id', Fields::class, 'id', ['alias' => 'Fields']);
    }
}
