<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;
use Phalcon\Mvc\Model\Validator\InclusionIn;
use Phalcon\Mvc\Model\Message as Message;

class Payments extends \PhalconRest\API\BaseModel
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
    public $payment_batch_id;

    /**
     *
     * @var integer
     */
    public $card_id;

    /**
     *
     * @var integer
     */
    public $check_id;

    /**
     *
     * @var string
     */
    public $external_id;

    /**
     *
     * @var string
     */
    public $created_on;

    /**
     *
     * @var string
     */
    public $settled_on;

    /**
     *
     * @var double
     */
    public $amount;

    /**
     *
     * @var string
     */
    public $mode;

    /**
     *
     * @var string
     */
    public $refund_id;

    /**
     *
     * @var string
     */
    public $refunded_on;

    /**
     * Paid|Failed|Refunded
     * 
     * @var string
     */
    public $status;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo("account_id", "PhalconRest\Models\Accounts", "id", array(
            'alias' => 'Accounts'
        ));
        
        $this->belongsTo("check_id", "PhalconRest\Models\Checks", "id", array(
            'alias' => 'Checks'
        ));
        
        $this->belongsTo("card_id", "PhalconRest\Models\Cards", "id", array(
            'alias' => 'Cards'
        ));
        
        $this->belongsTo('payment_batch_id', 'PhalconRest\Models\PaymentBatches', 'id', array(
            'alias' => 'PaymenttBatches'
        ));
    }

    /**
     * set a standard create date
     */
    public function beforeValidationOnCreate()
    {
        $this->created_on = date('Y-m-d H:i:s');
    }

    /**
     * perform various checks on when insert/edit a payment record
     *
     * TODO maybe a security check here? verify that the card submitted is owned by the authenticated user
     */
    public function validation()
    {
        $this->validate(new InclusionIn(array(
            "field" => 'mode',
            'message' => 'Payment Mode must be a specific value from the list.',
            'domain' => [
                "Credit",
                "Check",
                "Cash",
                'Discount',
                'Refund'
            ]
        )));
        
        $this->validate(new InclusionIn(array(
            "field" => 'status',
            'message' => 'Payment Status must be a specific value from the list.',
            'domain' => [
                "Paid",
                "Failed",
                "Refunded"
            ]
        )));
        
        if ($this->amount < 10 or $this->amount > 10000) {
            $message = new Message("Payment amount must be between 10 and 10,000", "amount", "InvalidValue");
            $this->appendMessage($message);
            return false;
        }
        
        if ($this->mode == 'check' and $this->check_id <= 0) {
            $message = new Message("A check payment must be accompanied by a valid check number.", "check_id", "InvalidValue");
            $this->appendMessage($message);
            return false;
        }
        
        return $this->validationHasFailed() != true;
    }

    /**
     * dynamic toggle fields based on who is asking
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns()
    {
        $blockColumns = [];
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();
        
        if ($currentUser->userType != 'Employee') {
            $blockColumns[] = 'external_id';
        }
        $this->setBlockColumns($blockColumns, true);
    }
}
