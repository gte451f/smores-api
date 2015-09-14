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
     * update the account to store stripes external PKID
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
     * for a given card number and the smores card record
     * create a processor card record
     *
     * cardNumber and cvv included since they are never stored in persistant storage
     *
     * @param object $card
     *            a stdObject that holds common card data
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