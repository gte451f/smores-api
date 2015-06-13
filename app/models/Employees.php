<?php
namespace PhalconRest\Models;

class Employees extends \PhalconRest\API\BaseModel
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
    public $active;

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
     * define custom model relationships
     *
     * (non-PHPdoc)
     *
     * @see extends \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo("user_id", "PhalconRest\Models\Users", "id", array(
            'alias' => 'Users'
        ));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::getParentModel()
     */
    // public function getParentModel()
    // {
    // return 'Users';
    // }
    
    /**
     * set some default values before we create a new employee record
     */
    public function beforeValidationOnCreate()
    {
        $this->active = 1;
        
        // assign a random string
        $this->salt = substr(md5(rand()), 0, 45);
        
        // encrypt password
        $security = $this->getDI()->get('security');
        $this->password = $security->hash($this->password);
    }

    /**
     * set some default values before we create a new employee record
     */
    public function beforeValidationOnUpdate()
    {
        // only update the password if a new one is provided
        if (strlen($this->password) >= 8 and strlen($this->password) !== 60) {
            // encrypt password
            $security = $this->getDI()->get('security');
            $this->password = $security->hash($this->password);
        }
    }
}
