<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class AccountStatements extends BaseModel
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
     * @var
     */
    public $statement_batch_id;

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
        $this->belongsTo('account_id', Accounts::class, 'id', ['alias' => 'Accounts']);
        $this->belongsTo('statement_batch_id', StatementBatches::class, 'batch_id', ['alias' => 'StatementBatches']);
    }
}
