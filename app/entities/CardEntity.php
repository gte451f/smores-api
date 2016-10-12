<?php
namespace PhalconRest\Entities;

use \PhalconRest\Exception\ValidationException;
use Inacho\CreditCard;

class CardEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * before a new card is saved, store it in the remote card processor
     * if adding the card fails, then do not save to server
     * this means that some validation must occur before the default model logic since
     * we run it though the remote processor first
     *
     *
     * @param object $object store common credit card data
     * @param null $id
     * @return array
     * @throws ValidationException
     */
    function beforeSave($object, $id = null)
    {
        //check that all the required data is present in the object first
        foreach (['name_on_card', 'number', 'vendor', 'expiration_year', 'expiration_month', 'cvc'] as $field) {
            if (!isset($object->$field)) {
                // required field is missing, throw a hissy fit!
                throw new ValidationException("Missing Card Data ", [
                    'dev' => "Some required card data missing: $field",
                    'code' => '461319681891891'
                ], [
                    $field => "This $field field is required."
                ]);
            }
        };

        $processor = $this->getDI()->get('paymentProcessor');
        $account = \PhalconRest\Models\Accounts::findFirst($object->account_id);
        if (!$account->external_id or $account->external_id == null) {
            $accountExternalId = $processor->createCustomer($account);
        } else {
            $accountExternalId = $account->external_id;
        }

        // run credit card through a series of validation tests
        if (strlen($object->name_on_card) <= 5) {
            throw new ValidationException("Bad Card Name Supplied", [
                'dev' => "Bad card name supplied:  $object->name_on_card",
                'code' => '564684646816189464864'
            ], [
                'number' => 'The supplied Card Name is invalid.'
            ]);
        }

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
     *
     * @see \PhalconRest\API\Entity::beforeDelete()
     * @param BaseModel $model
     */
    public function beforeDelete(\PhalconRest\API\BaseModel $model)
    {
        $processor = $this->getDI()->get('paymentProcessor');
        $processor->deleteCard($model->external_id);
    }
}