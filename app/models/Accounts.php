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
    public $active;

    /**
     *
     * @var number
     */
    public $code;

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
        
        // assign a random string to the code
        $this->code = substr(md5(rand()), 0, 45);
        $this->active = 'Inactive';
        
        // encrypt password
        $security = $this->getDI()->get('security');
        $this->password = $security->hash($this->password);
    }

    public function beforeValidationOnUpdate()
    {
        $this->updated_on = date('Y-m-d');
        
        // only update the password if a new one is provided
        if (strlen($this->password) >= 8 and strlen($this->password) !== 60) {
            // encrypt password
            $security = $this->getDI()->get('security');
            $this->password = $security->hash($this->password);
        }
    }
}
