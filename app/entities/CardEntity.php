<?php
namespace PhalconRest\Entities;

class CardEntity extends \PhalconRest\API\Entity
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
        
        // put this in until we better populate the credit card form
        $object->cvc = '123';
        
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