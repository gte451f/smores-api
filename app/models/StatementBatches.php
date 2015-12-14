<?php
namespace PhalconRest\Models;

class StatementBatches extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $batch_id;

    /**
     * this model's parent model
     *
     * @var string
     */
    public static $parentModel = 'Batches';

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        // set these since plural is slightly non-standard
        $this->pluralName = 'StatementBatches';
        $this->singularName = 'StatementBatch';
        
        $this->pluralTableName = 'statements_batches';
        $this->singularTableName = 'statements_batch';
        
        parent::initialize();
        
        $this->hasOne("batch_id", "PhalconRest\Models\Batches", "id", array(
            'alias' => 'Batches'
        ));
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns()
    {
        $this->setBlockColumns([
            'batch_id'
        ], true);
    }
}
