<?php
namespace PhalconRest\Entities;

use \PhalconRest\Util\ValidationException;
use Inacho\CreditCard;

class CardEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * before a new card is saved, store it in the remote card processor
     * if adding the card fails, then do not save to server
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::afterSave()
     */
    function beforeSave($object, $id)
    {
        $processor = $this->getDI()->get('paymentProcessor');
        $account = \PhalconRest\Models\Accounts::findFirst($object->account_id);
        if (! $account->external_id or $account->external_id == null) {
            $accountExternalId = $processor->createCustomer($account);
        } else {
            $accountExternalId = $account->external_id;
        }
        
        // run credit card through a series of validation tests
        $result = CreditCard::validCreditCard($object->number, $object->vendor);
        if ($result['valid'] == false) {
            throw new ValidationException("Bad Credit Card Supplied", [
                'dev' => "Bad card number supplied:  $object->number",
                'code' => '5846846848644984'
            ], [
                'number' => 'The supplied credit card number is invalid.'
            ]);
        } else {
            $object->number = $result['number'];
        }
        
        $result = CreditCard::validDate($object->expiration_year, $object->expiration_month);
        if ($result == false) {
            throw new ValidationException("Bad Expiration Date Supplied", [
                'dev' => "Bad expiration month or year:  $object->expiration_month | $object->expiration_year",
                'code' => '81618161684684'
            ], [
                'expiration_month' => 'The supplied expiration month is invalid.',
                'expiration_year' => 'The supplied expiration year is invalid.'
            ]);
        }
        
        // put this in until we better populate the credit card form
        // TODO fix CVC in app
        // $object->cvc = '123';
        
        $object->external_id = $processor->createCard($accountExternalId, $object);
        
        // clear out data we do NOT want to store
        $object->number = substr($object->number, strlen($object->number) - 4, 4);
        unset($object->cvc);
        
        return $object;
    }

    /**
     * attempt to delete card from remote processor before removing the internal record
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::beforeDelete()
     */
    public function beforeDelete($model)
    {
        $processor = $this->getDI()->get('paymentProcessor');
        $processor->deleteCard($model->external_id);
    }
}