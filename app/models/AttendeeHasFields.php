<?php
namespace PhalconRest\Models;

class AttendeeHasFields extends \PhalconRest\API\BaseModel
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
        $this->belongsTo("user_id", "PhalconRest\\Models\\Users", "id", array(
            'alias' => 'Users'
        ));

        $this->belongsTo('attendee_id', 'PhalconRest\Models\Attendees', 'id', array(
            'alias' => 'Attendees'
        ));

        $this->belongsTo('field_id', 'PhalconRest\Models\Fields', 'id', array(
            'alias' => 'Fields'
        ));
    }
}
