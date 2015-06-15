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
    public $position;



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
    

}
