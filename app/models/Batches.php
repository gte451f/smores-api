<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator\InclusionIn;
use Phalcon\Mvc\Model\Validator\Numericality as NumericalityValidator;

class Batches extends \PhalconRest\API\BaseModel
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
    public $created_by_id;

    /**
     *
     * @var string
     */
    public $created_on;

    /**
     *
     * @var string
     */
    public $min_type;

    /**
     *
     * @var int
     */
    public $min_amount;
    
    /**
     * @var string
     */
    public $log;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        // set these since plural is slightly non-standard
        $this->pluralName = 'Batches';
        $this->singularName = 'Batch';
        
        $this->pluralTableName = 'batches';
        $this->singularTableName = 'batch';
        
        parent::initialize();
        
        $this->belongsTo("created_by_id", "PhalconRest\\Models\\Users", "id", array(
            'alias' => 'Users'
        ));
        
        $this->hasOne('id', 'PhalconRest\Models\PaymentBatches', 'batch_id', array(
            'alias' => 'PaymentBatches'
        ));
        
        $this->hasOne('id', 'PhalconRest\Models\StatementBatches', 'batch_id', array(
            'alias' => 'StatementBatches'
        ));
    }

    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d H:i:s');
        
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();
        $this->created_by_id = $currentUser->id;
        
        if (! isset($this->status)) {
            $this->status = 'New';
        }
    }

    /**
     * perform various checks on a batch when creating a new one
     */
    public function validation()
    {
        $this->validate(new InclusionIn(array(
            "field" => 'min_type',
            'message' => 'Batch must include a minimum payment type.',
            'domain' => [
                "Total",
                "Outstanding",
                "Flat"
            ]
        )));
        
        $this->validate(new NumericalityValidator(array(
            "field" => 'min_amount'
        )));
        
        $this->validate(new InclusionIn(array(
            "field" => 'status',
            'message' => 'Status must be one of the following:',
            'domain' => [
                "New",
                "In Progress",
                "Complete"
            ]
        )));
        
        return $this->validationHasFailed() != true;
    }
}
