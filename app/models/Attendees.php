<?php
namespace PhalconRest\Models;

class Attendees extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $account_id;

    /**
     *
     * @var string
     */
    public $school_grade;

    /**
     *
     * @var string
     */
    public $medical_notes;

    /**
     *
     * @var string
     */
    public $allergy_notes;

    /**
     *
     * @var string
     */
    public $general_notes;

    /**
     * this model's parent model
     *
     * @var string
     */
    public static $parentModel = 'Users';

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
        $this->hasOne("user_id", "PhalconRest\\Models\\Users", "id", array(
            'alias' => 'Users'
        ));

        $this->belongsTo('account_id', 'PhalconRest\Models\Accounts', 'id', array(
            'alias' => 'Accounts'
        ));

        $this->hasMany("user_id", "PhalconRest\\Models\\Registrations", "attendee_id", array(
            'alias' => 'Registrations'
        ));

        $this->hasOne('user_id', "PhalconRest\\Models\\CustomAttendeeFields", 'user_id', [
            'alias' => 'CustomAttendeeFields'
        ]);
    }

    /**
     * set to pkid of parent table
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::getPrimaryKeyName()
     */
    public function getPrimaryKeyName()
    {
        return 'user_id';
    }
}
