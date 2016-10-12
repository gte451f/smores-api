<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class Registrations extends BaseModel
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
    public $user_id;

    /**
     *
     * @var string
     */
    public $notes;

    /**
     *
     * @var string
     */
    public $created_on;

    /**
     *
     * @var string
     */
    public $updated_on;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->hasMany("id", Requests::class, "registration_id", ['alias' => 'Requests']);
        $this->hasMany("id", Charges::class, "registration_id", ['alias' => 'Charges']);
        $this->belongsTo("attendee_id", Attendees::class, "user_id", ['alias' => 'Attendees']);
        $this->hasOne('id', CustomRegistrationFields::class, 'registration_id',
            ['alias' => 'CustomRegistrationFields']);
        $this->hasManyToMany("attendee_id", Attendees::class, "user_id", "account_id", Accounts::class, "id",
            ['alias' => 'Accounts']);
    }

    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d H:i:s');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'user_id' => 'attendee_id',
            'notes' => 'notes',
            'created_on' => 'created_on',
            'updated_on' => 'updated_on'
        );
    }
}
