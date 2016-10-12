<?php
namespace PhalconRest\Entities;

use \PhalconRest\Libraries\API\Entity;
use \PhalconRest\Exception\ValidationException;

// required for trait
use Inacho\CreditCard;

class PaymentEntity extends \PhalconRest\Libraries\API\Entity
{
    // include payment validation logic
    use \PhalconRest\Libraries\Payments\Validate;

    /**
     * before a new payment, process credit card payments with 3rd party processor
     * if adding a credit card payment is detected and fails, do not save the payment
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::beforeSave()
     */
    function beforeSave($object, $id = null)
    {
        // filter out stray characters from amount
        $object->amount = preg_replace("/[^0-9,.]/", "", $object->amount);

        if ($object->mode == 'Credit' and $this->saveMode == 'insert') {
            $processor = $this->getDI()->get('paymentProcessor');
            if (isset($object->card_id) and $object->card_id > 0) {
                $card = \PhalconRest\Models\Cards::findFirst($object->card_id);
                $account = \PhalconRest\Models\Accounts::findFirst($object->account_id);
                if (!$card->external_id or $card->external_id == null) {
                    // error, need a valid external_id in order to process the credit card
                    // consider adding the card on the fly?
                    throw new \Exception('Selected card does not have enough information to process.');
                }

                $object->external_id = $processor->chargeCard([
                    'card_id' => $card->external_id,
                    'amount' => $object->amount,
                    'account_id' => $account->external_id
                ]);
            } else {
                // must be a new card, so charge this card for a one time payment
                // perform some basic validation
                $this->validateCardData($object);
                try {
                    $cardData = (array)$object;
                    $object->external_id = $processor->chargeCard($cardData);
                } catch (Exception $e) {
                    // what happens here?
                }
            }
            return $object;
        }

        if ($object->mode == 'Refund' and isset($id)) {
            // see if the save is going FROM card to refund and apply refund logic
            $payment = \PhalconRest\Models\Payments::findFirst($id);
            $account = \PhalconRest\Models\Accounts::findFirst($object->account_id);

            if ($payment->mode == 'Credit') {
                $processor = $this->getDI()->get('paymentProcessor');
                // include additional details here since processors require different data
                $refund_id = $processor->refundCharge([
                    'charge_id' => $object->external_id,
                    'account_id' => $account->external_id,
                    'amount' => $object->amount
                ]);
                $object->refund_id = $refund_id;

                // this is an auth.net special rule that detects between a Refund and Voided transaction
                // we'll see how much mileage we get out of this
                if ($object->external_id === $refund_id) {
                    $object->status = 'Voided';
                } else {
                    $object->status = 'Refunded';
                }
                $object->refunded_on = date('Y-m-d');
            }
        }

        return $object;
    }

    /**
     * prevent delete action from completing when a credit card has been charged
     * should refund them instead
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::beforeDelete()
     * @param \PhalconRest\API\BaseModel $model
     * @throws \Exception
     */
    public function beforeDelete(\PhalconRest\API\BaseModel $model)
    {
        // blocking refunds for the time being
        if ($model->mode == 'Refund') {
            throw new \Exception('Blocked attempt to delete credit card charge that was refunded');
        }

        // prevent delete of non-refunded credit card payment
        if ($model->mode == 'Credit' and $model->external_id != null) {
            throw new \Exception('Blocked attempt to delete payment with valid charge');
        }
    }

    /**
     * remove the check record if one was connected to it
     * no need to remove card_id since the card might be used on multiple payments
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::afterDelete()
     */
    public function afterDelete($model)
    {
        // extend me in child class
        if ($model->check_id > 0) {
            $check = \PhalconRest\Models\Checks::findFirst($model->check_id);
            if (!$check->delete()) {
                throw new ValidationException("Internal error removing check record", array(
                    'code' => '29629674',
                    'dev' => 'Error while attempting to delete a check after the related payment was removed.'
                ), $check->getMessages());
            }
        }
    }
}