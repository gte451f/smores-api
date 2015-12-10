<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator\InclusionIn;

class PaymentBatches extends \PhalconRest\API\BaseModel
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
    public $processed_on;

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
     *
     * @var string
     */
    public $payment_method;

    /**
     *
     * @var string
     */
    public $created_on;

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
        $this->hasMany("id", "PhalconRest\Models\Payments", "payment_batch_id", array(
            'alias' => 'Payments'
        ));
    }

    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d H:i:s');
        
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();
        $this->created_by_id = $currentUser->id;
    }

    /**
     * perform various checks on a batch when creating a new one
     */
    public function validation()
    {
        $this->validate(new InclusionIn(array(
            "field" => 'payment_method',
            'message' => 'Payment Method must be one of the following:',
            'domain' => [
                "Credit",
                "Check",
                "Cash"
            ]
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
