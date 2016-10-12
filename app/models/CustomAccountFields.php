<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

/**
 * backed by an ever changing view
 * see Fields controller for more
 *
 * @author jjenkins
 *
 */
class CustomAccountFields extends BaseModel
{

    /**
     *
     * @var integer
     */
    public $account_id;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo("account_id", Accounts::class, "id", ['alias' => 'Accounts']);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::getPrimaryKeyName()
     */
    public function getPrimaryKeyName()
    {
        return 'account_id';
    }

    /**
     * hide user_id in favor of parent id
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns($withParents = true)
    {
        $this->setBlockColumns([
            'account_id'
        ]);
    }
}
