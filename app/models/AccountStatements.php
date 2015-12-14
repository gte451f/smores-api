<?php
namespace PhalconRest\Models;

class AccountStatements extends \PhalconRest\API\BaseModel
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
        
        $this->belongsTo('account_id', 'PhalconRest\Models\Accounts', 'id', array(
            'alias' => 'Accounts'
        ));
        
        $this->belongsTo('statement_batch_id', 'PhalconRest\Models\StatementBatches', 'id', array(
            'alias' => 'StatementBatches'
        ));
    }
}
