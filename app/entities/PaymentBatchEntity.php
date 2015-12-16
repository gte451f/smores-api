<?php
namespace PhalconRest\Entities;

class PaymentBatchEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * store a simple list of noteworth activity as the batch is processed
     *
     * @var array
     */
    private $batch_log = [];

    /**
     * create all payment records and charge the payment processor
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\Entity::afterSave()
     */
    public function afterSave($object, $id)
    {
        switch ($object->min_type) {
            case 'Total':
                $percent = $object->min_amount / 100;
                $property = 'total_balance';
                break;
            
            case 'Flat':
                $amount = $percent = $object->min_amount;
                break;
            case 'Outstanding':
                $percent = $object->min_amount / 100;
                $property = 'charge_total';
                break;
            default:
                // uh oh, unknown type found!
                $this->delete($id);
                break;
        }
        
        // construct PaymentEntity
        $paymentModel = new \PhalconRest\Models\Payments();
        $searchHelper = new \PhalconRest\API\SearchHelper();
        $paymentEntity = new \PhalconRest\Entities\PaymentEntity($paymentModel, $searchHelper);
        
        $accounts = $object->selected_accounts;
        $failedRunningTotal = 0;
        $processedRunningTotal = 0;
        $failedCount = 0;
        $processCount = 0;
        foreach ($accounts as $accountId) {
            $billingSummary = \PhalconRest\Models\AccountBillingSummaries::findFirst("id = $accountId");
            if (! $billingSummary) {
                // uh oh, no billing summary loaded!
                $this->batch_log[] = "Error!  Could not load the billing summary for account #$accountId ";
                $failedCount ++;
                continue;
            }
            $chargeCard = \PhalconRest\Models\Cards::findFirst("account_id = $accountId AND allow_reoccuring = 1");
            if (! $chargeCard) {
                // uh oh no charge card found!
                $this->batch_log[] = "Error!  Could not load a valid credit card to charge for account #$accountId.";
                $failedCount ++;
                continue;
            }
            
            // construct object for saving payment
            $inputs = new \stdClass();
            $inputs->account_id = $accountId;
            $inputs->payment_batch_id = $id;
            $inputs->mode = 'Credit';
            $inputs->card_id = $chargeCard->id;
            $inputs->status = 'Paid';
            
            // calc the default amount based on percent
            if ($object->min_type != 'Flat') {
                $amount = $billingSummary->$property * $percent;
            }
            // take the lessor of two amounts...
            $inputs->amount = ($billingSummary->total_balance >= $amount) ? $amount : $billingSummary->total_balance;
            
            // filter out zero charges
            if ($inputs->amount == 0) {
                $this->batch_log[] = "Skipping payment for account #$accountId since outstanding balance is zero.";
                $failedCount ++;
                continue;
            }
            
            try {
                $result = $paymentEntity->save($inputs);
                $processedRunningTotal = $processedRunningTotal + $inputs->amount;
                $processCount ++;
            } catch (\PhalconRest\Util\ValidationException $e) {
                $this->batch_log[] = "Attempt to save payment for account #$accountId failed:";
                foreach ($e->errorStore->validationList as $message) {
                    $this->batch_log[] = '--' . $message->getMessage();
                }
                $failedRunningTotal = $failedRunningTotal + $inputs->amount;
                $failedCount ++;
                continue;
            }
        }
        
        $paymentBatch = \PhalconRest\Models\PaymentBatches::findFirst($id);
        $paymentBatch->amount_failed = $failedRunningTotal;
        $paymentBatch->amount_processed = $processedRunningTotal;
        $paymentBatch->fail_count = $failedCount;
        $paymentBatch->success_count = $processCount;
        $paymentBatch->Batches->log = implode(PHP_EOL, $this->batch_log);
        
        if ($paymentBatch->save() == false) {
            foreach ($paymentBatch->getMessages() as $message) {
                echo $message, "\n";
            }
        }
    }
}