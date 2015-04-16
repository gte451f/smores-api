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
        
        $this->belongsTo("user_id", "PhalconRest\Models\Attendees", "user_id", array(
            'alias' => 'Attendees'
        ));
        
        $this->addBehavior(new Timestampable(array(
            'beforeCreate' => array(
                'field' => 'created_on',
                'format' => 'Y-m-d'
            )
        )));
        
        $this->addBehavior(new Timestampable(array(
            'beforeUpdate' => array(
                'field' => 'updated_on',
                'format' => 'Y-m-d'
            )
        )));
    }
}
