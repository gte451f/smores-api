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
        $this->hasMany("id", "PhalconRest\Models\AccountAddrs", "account_id", array(
            'alias' => 'AccountAddrs'
        ));
        $this->hasMany("id", "PhalconRest\Models\Checks", "account_id", array(
            'alias' => 'Checks'
        ));
        $this->hasMany("id", "PhalconRest\Models\Cards", "account_id", array(
            'alias' => 'Cards'
        ));
        $this->hasMany("id", "PhalconRest\Models\Payments", "account_id", array(
            'alias' => 'Payments'
        ));
        $this->hasMany("id", "PhalconRest\Models\Charges", "account_id", array(
            'alias' => 'Charges'
        ));
    }

    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d');
    }

    public function beforeValidationOnUpdate()
    {
        $this->updated_on = date('Y-m-d');
    }
}
