<?php
namespace PhalconRest\Entities;

class CardEntity extends \PhalconRest\API\Entity
{

    /**
     * before a new card is saved, store it in the remote card processor
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
        
        // put this in until we better populate the credit card form
        $object->cvc = '123';
        
        $cardExternalId = $processor->createCard($accountExternalId, $object);
        $object->external_id = $cardExternalId;
        
        return $object;
    }
}