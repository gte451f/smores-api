<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;
use Phalcon\Validation\Validator\InclusionIn as InclusionInValidator;

use Phalcon\Mvc\Model\Message as Message;

class Payments extends BaseModel
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
        $this->belongsTo("account_id", Accounts::class, "id", ['alias' => 'Accounts']);
        $this->belongsTo("check_id", Checks::class, "id", ['alias' => 'Checks']);
        $this->belongsTo("card_id", Cards::class, "id", ['alias' => 'Cards']);
        $this->belongsTo('payment_batch_id', PaymentBatches::class, 'batch_id', ['alias' => 'PaymentBatches']);
    }

    /**
     * attempt to always populate these values if they exist
     */
    public function beforeValidationOnCreate()
    {
        // assign a few default values if they aren't provided
        if (!isset($this->created_on)) {
            $this->created_on = date('Y-m-d H:i:s');
        }

        // set a default status if not defined
        if (!isset($this->status)) {
            $this->status = 'Paid';
        }
    }

    /**
     * perform various checks on when insert/edit a payment record
     *
     * TODO maybe a security check here? verify that the card submitted is owned by the authenticated user
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'mode',
            new InclusionInValidator([
                'message' => 'Payment Mode must be a specific value from the list.',
                'domain' => [
                    "Credit",
                    "Check",
                    "Cash",
                    'Discount',
                    'Refund'
                ]
            ])
        );

        $validator->add(
            'status',
            new InclusionInValidator([
                'message' => 'Payment Status must be a specific value from the list.',
                'domain' => [
                    "Paid",
                    "Failed",
                    "Refunded"
                ]
            ])
        );


        if ($this->amount < 1 or $this->amount > 5000) {
            $message = new Message("Payment amount must be between 1 and 5,000", "amount", "InvalidValue");
            $this->appendMessage($message);
            return false;
        }

        if ($this->mode == 'check' and $this->check_id <= 0) {
            $message = new Message("A check payment must be accompanied by a valid check number.", "check_id",
                "InvalidValue");
            $this->appendMessage($message);
            return false;
        }

        return $this->validate($validator);
    }

    /**
     * dynamic toggle fields based on who is asking
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns($withParents = true)
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
