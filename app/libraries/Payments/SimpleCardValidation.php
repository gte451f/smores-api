<?php
namespace PhalconRest\Libraries\Payments;

use \PhalconRest\Util\ValidationException;

trait SimpleCardValidation
{

    function verifyCard($card)
    {
        if (strlen($card->name_on_card) < 2 or strlen($card->name_on_card) > 45) {
            throw new ValidationException("Could not save card information", array(
                'code' => 216894194189464684
            ), [
                'name_on_card' => 'The name on the card should be between 2 and 45 characters in length'
            ]);
        }

        if (strlen($card->cvc) > 4 OR strlen($card->cvc) < 2) {
            throw new ValidationException("Could not save card information", array(
                'code' => 216894194189464684
            ), [
                'expiration_year' => 'Card CVC number must between 3 & 4 characters'
            ]);
        }

        if ($card->expiration_year < date("Y")) {
            throw new ValidationException("Could not save card information", array(
                'code' => 216894194189464684
            ), [
                'expiration_year' => 'Expiration Year must be greater than or equal to current year'
            ]);
        }

        if (strlen($card->expiration_month) <= 1) {
            throw new ValidationException("Could not save card information", array(
                'code' => 216894194189464684
            ), [
                'expiration_month' => 'Expiration Month must be included'
            ]);
        }

        if ($card->expiration_year == date("Y")) {
            if ($card->expiration_month <= date("m")) {
                throw new ValidationException("Could not save card information", array(
                    'code' => 216894194189464684
                ), [
                    'expiration_month' => 'Expiration Month must be greater than current month'
                ]);
            }
        }

        if (strlen($card->number) < 7) {
            throw new ValidationException("Could not save card information", array(
                'code' => 216894194189464684
            ), [
                'number' => 'Please check your card number'
            ]);
        }

        // fail if the card record already contains an external id
        if (isset($card->external_id) AND strlen($card->external_id > 0)) {
            throw new HTTPException("Could not save card information", 404, array(
                'code' => 216894194189464684,
                'dev' => 'This card record already has an external_id'
            ));
        }
    }

}