<?php
namespace PhalconRest\Libraries\Payments;

/**
 * a generic api for all payment processing implementations
 * acts as the glue connecting a smores account with the processor's data on a card holder
 *
 * @author jjenkins
 *
 */
interface Processor
{

    /**
     * create a stripe "customer" record for an account
     * update the account to store stripe's external PKID
     * might be bad to couple external record with internal update, but this is normally done
     * in a transparent manner and opt to keep both actions as close as possible
     *
     * include logic to search for this account in stripe before attempting to make a brand new one
     *
     * @param \PhalconRest\Models\Accounts $account
     * @return mixed external_id otherwise false
     */
    public function createCustomer(\PhalconRest\Models\Accounts $account);

    /**
     * for an external_id
     * search for a customer record and return it's full details
     *
     *
     * @param string $external_id
     *            from an account
     * @param
     *            boolean ignore cache and force pull from api
     * @return object an object representing the cutomer record otherwise false
     */
    public function findCustomer($external_id, $force_api_call);

    /**
     * for a given credit card create a charge request
     *
     *
     * @param array $data
     *            an array hold various combinations of card data to be processed
     * @return object an object representing the charge record otherwise false
     */
    public function chargeCard($data);

    /**
     * for a given charge, refund the transaction
     *
     * @param array $data
     *            an array hold various combinations of charge data to be processed
     * @return object an object representing the refund record otherwise false
     */
    public function refundCharge($data);

    /**
     * for a given external account number and credit card data
     * create a processor card record
     *
     * written so an internal need not already be created (unlike new accounts)
     * returns the newly created card record
     * leaving it up to the entity to update the internal record
     *
     * @param object $card
     *            a stdObject that holds common card data
     * @return string external card id
     *
     */
    public function createCard($accountExternalId, $card);

    /**
     * for a given external_id, delete the card associated with it
     *
     * @param string $external_id
     *            from an card record
     */
    public function deleteCard($externalId);

    /**
     * for a given external_id, delete the customer and all cards associated with it
     *
     * @param string $external_id
     *            from an account record
     */
    public function deleteCustomer($externalId);
}