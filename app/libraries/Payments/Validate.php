<?php
namespace PhalconRest\Libraries\Payments;

use \PhalconRest\Exception\ValidationException;
use Inacho;

/**
 * the trait provides a way to "side load" some validation logic in multiple classes
 *
 *
 * use of this trait requires card validation library
 * ie...
 * use Inacho\CreditCard;
 * use \PhalconRest\Util\ValidationException;
 *
 *
 * @author jjenkins
 *
 */
trait Validate
{

    /**
     * @param $object
     *            standard object holding the card data to be submitted $object
     * @return mixed
     * @throws ValidationException
     */
    public function validateCardData($object)
    {
        // a valid card payment expect the following
        $expectedFields = [
            'expiration_month',
            'expiration_year',
            'number',
            'vendor',
            'name_on_card',
            'cvc',
            'zip',
            'address'
        ];

        foreach ($expectedFields as $field) {
            if (!isset($object->$field)) {
                throw new ValidationException("Bad Credit Card Supplied: $field is required", [
                    'dev' => "Client submitted post missing critical data: $field",
                    'code' => '981313156464984'
                ], [
                    $field => "$field is required"
                ]);
            }
        }

        //remove any non numeric value from card number and cvc
        $object->number = filter_var($object->number, FILTER_SANITIZE_NUMBER_INT);
        $object->cvc = filter_var($object->cvc, FILTER_SANITIZE_NUMBER_INT);

        // run credit card through a series of more rigorous validation tests
        $result = Inacho\CreditCard::validCreditCard($object->number, $object->vendor);
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

        $result = Inacho\CreditCard::validDate($object->expiration_year, $object->expiration_month);
        if ($result == false) {
            throw new ValidationException("Bad Expiration Date Supplied", [
                'dev' => "Bad expiration month or year:  $object->expiration_month | $object->expiration_year",
                'code' => '81618161684684'
            ], [
                'expiration_month' => 'The supplied expiration month is invalid.',
                'expiration_year' => 'The supplied expiration year is invalid.'
            ]);
        }

        return $object;
    }
}