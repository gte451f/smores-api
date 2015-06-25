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
        $this->hasOne("user_id", "PhalconRest\Models\Users", "id", array(
            'alias' => 'Users'
        ));
        
        $this->belongsTo('account_id', 'PhalconRest\Models\Accounts', 'id', array(
            'alias' => 'Accounts'
        ));
        
        $this->hasMany("user_id", "PhalconRest\Models\Registrations", "user_id", array(
            'alias' => 'Registrations'
        ));
    }
}
