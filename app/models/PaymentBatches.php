<?php
namespace PhalconRest\Models;

class PaymentBatches extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $batch_id;

    /**
     *
     * @var integer
     */
    public $fail_count;

    /**
     *
     * @var integer
     */
    public $success_count;

    /**
     *
     * @var double
     */
    public $amount_failed;

    /**
     *
     * @var double
     */
    public $amount_processed;

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
        $this->pluralName = 'PaymentBatches';
        $this->singularName = 'PaymentBatch';
        
        $this->pluralTableName = 'payment_batches';
        $this->singularTableName = 'payment_batch';
        
        parent::initialize();
        $this->hasMany("batch_id", "PhalconRest\\Models\\Payments", "payment_batch_id", array(
            'alias' => 'Payments'
        ));
        
        $this->hasOne("batch_id", "PhalconRest\\Models\\Batches", "id", array(
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

    /**
     * point to parent id
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::getPrimaryKeyName()
     */
    public function getPrimaryKeyName()
    {
        return "id";
    }
}
