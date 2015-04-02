<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Behavior\Timestampable as Timestampable;

class Accounts extends \PhalconRest\API\BaseModel
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
    public $user_name;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $salt;


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
     */
    public function initialize()
    {
        
        $this->hasMany("id", "PhalconRest\Models\Owners", "account_id", array(
            'alias' => 'Owners'
        ));
        
        $this->hasMany("id", "PhalconRest\Models\Attendees", "account_id", array(
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
