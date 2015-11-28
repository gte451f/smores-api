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
    }
}
