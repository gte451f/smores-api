<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Behavior\Timestampable as Timestampable;

class Registrations extends \PhalconRest\API\BaseModel
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
        $this->hasMany("id", "PhalconRest\Models\Requests", "registration_id", array(
            'alias' => 'Requests'
        ));
        
        $this->belongsTo("attendee_id", "PhalconRest\Models\Attendees", "user_id", array(
            'alias' => 'Attendees'
        ));
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
