<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class AccountHasFields extends BaseModel
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
    public $account_id;

    /**
     *
     * @var integer
     */
    public $field_id;

    /**
     *
     * @var string
     */
    public $value;

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
        $this->hasOne("account_id", "PhalconRest\\Models\\Accounts", "id", array(
            'alias' => 'Accounts'
        ));

        $this->belongsTo('field_id', 'PhalconRest\Models\Fields', 'id', array(
            'alias' => 'Fields'
        ));
    }
}
