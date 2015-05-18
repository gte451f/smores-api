<?php
namespace PhalconRest\Models;

class Owners extends \PhalconRest\API\BaseModel
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
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::getParentModel()
     */
    public function getParentModel()
    {
        return 'Users';
    }
}
