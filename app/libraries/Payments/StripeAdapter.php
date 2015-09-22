<?php
namespace PhalconRest\Libraries\Payments;

use Phalcon\DI\Injectable;
use \PhalconRest\Util\HTTPException;
use PhalconRest\Util\ValidationException;
use \PhalconRest\Models\PhalconRest\Models;

/**
 *
 * @author jjenkins
 *        
 */
final class StripeAdapter extends Injectable implements Processor
{

    /**
     * store a cached version of the current customer to cut down on frequent calls to the api
     * store customer records as ['TOKEN' => CUSTOMER OBJECT]
     * this makes for ez reference for multiple records
     */
    private $cachedCustomers = [];

    /**
     * store a cached version of the current card to cut down on frequent calls to the api
     * store card records as ['TOKEN' => CARD OBJECT]
     * this makes for ez reference for multiple records
     */
    private $cachedCards = [];

    function __construct($key)
    {
        $di = \Phalcon\DI::getDefault();
        $this->di = $di;
        
        // init stripe access
        \Stripe\Stripe::setApiKey($key);
    }

    /**
     * create a card holder account on the stripe system
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::createCustomer()
     */
    public function createCustomer(\PhalconRest\Models\Accounts $account)
    {
        
        // check that this customer doesn't already exist
        // skip cache to be sure the latest record is pulled
        if ($account->external_id) {
            $customer = $this->findCustomer($account->external_id);
            
            // match found, no need to create a new customer record
            if ($customer) {
                $this->cachedCustomers[$account->external_id] = $customer;
                return $account->external_id;
            }
        }
        
        try {
            $result = \Stripe\Customer::create(array(
                "description" => $account->id
            ));
        } catch (\Stripe\Error\Base $e) {
            $this->handleStripeError($e);
        }
        
        $account->external_id = $result->id;
        if (! $account->save()) {
            throw new HTTPException("Could not save Payment Information for account", 404, array(
                'code' => 1872391762862,
                'dev' => 'StripeAdapter->createCustomer failed to save external_id: ' . $result->id
            ), $account->getMessages());
            return false;
        }
        return $result->id;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::createCard()
     */
    public function createCard($accountExternalId, $card)
    {
        
        // validate some credit card data
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
        if ($card->external_id) {
            throw new HTTPException("Could not save card information", 404, array(
                'code' => 216894194189464684,
                'dev' => 'This card record already has an external_id'
            ));
        }
        
        $customer = $this->findCustomer($accountExternalId);
        
        if ($customer) {
            // pull card data from storage and temp data
            $cardData = [
                'number' => $card->number,
                'cvc' => $card->cvc,
                'name' => $card->name_on_card,
                'exp_month' => $card->expiration_month,
                'exp_year' => $card->expiration_year,
                'object' => 'card'
            ];
            
            try {
                $result = $customer->sources->create(array(
                    "source" => $cardData
                ));
                return $result->id;
            } catch (\Stripe\Error\Base $e) {
                $this->handleStripeError($e);
            }
        } else {
            // hmm.. provided accountExternalId found no customers
            // throw an error instead of attempting to create a customer on the fly
            throw new HTTPException("Could not save card details", 404, array(
                'code' => 654686168484646,
                'dev' => 'Could not locate an existing customer record to attach card to.'
            ), []);
        }
    }

    /**
     * find an existing customer in the stripe system
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::findCustomer()
     */
    public function findCustomer($external_id, $force_api_call = false)
    {
        // simple validation
        if (strlen($external_id) < 5) {
            throw new \Exception('external_id is not long enough');
        }
        if (! strstr($external_id, 'cus_')) {
            throw new \Exception('invalid stripe external_id');
        }
        
        // consult w/ cache first
        if (! $force_api_call) {
            if (isset($this->cachedCustomers[$external_id])) {
                return $this->cachedCustomers[$external_id];
            }
        }
        
        // either force is true or the cache missed, pull from api
        try {
            $customer = \Stripe\Customer::retrieve($external_id);
            
            if ($customer and $customer->delete != true) {
                $this->cachedCustomers[$customer->id] = $customer;
                return $customer;
            }
        } catch (\Stripe\Error\Base $e) {
            $this->handleStripeError($e);
        }
        
        // if a customer record is not found, return false
        return false;
    }

    /**
     * find an existing card in the stripe system
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::findCustomer()
     */
    public function findCard($external_id, $force_api_call = false)
    {
        // simple validation
        if (strlen($external_id) < 5) {
            throw new \Exception('external_id is not long enough');
        }
        if (! strstr($external_id, 'card_')) {
            throw new \Exception('invalid stripe external_id');
        }
        
        // consult w/ cache first
        if (! $force_api_call) {
            if (isset($this->cachedCustomers[$external_id])) {
                return $this->cachedCustomers[$external_id];
            }
        }
        
        // either force is true or the cache missed, pull from api
        try {
            // load customer record in order to request related card record
            $card = \PhalconRest\Models\Cards::findFirst("external_id = '$external_id'");
            $account = $card->Accounts;
            
            $customer = $this->findCustomer($account->external_id);
            
            $card = $customer->sources->retrieve($external_id);
            
            if ($card and $card->delete != true) {
                $this->cachedCards[$card->id] = $card;
                return $card;
            }
        } catch (\Stripe\Error\Base $e) {
            $this->handleStripeError($e);
        }
        
        // if a customer record is not found, return false
        return false;
    }

    /**
     * attempt to keep api as simple as possible
     * provide the external id of the card to delete and the
     * adapter does the rest
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::deleteCard()
     */
    public function deleteCard($externalId)
    {
        $mm = $this->di->get('modelsManager');
        
        $cardList = $mm->createBuilder()
            ->from('PhalconRest\\Models\\Cards')
            ->join('PhalconRest\\Models\\Accounts')
            ->where("PhalconRest\\Models\\Cards.external_id >= '$externalId'")
            ->getQuery()
            ->execute();
        
        // remove all cards..in case there are 0 or N
        foreach ($cardList as $card) {
            // load customer from model
            $customer = \Stripe\Customer::retrieve($card->Accounts->external_id);
            // issue delete command
            $customer->sources->retrieve($externalId)->delete();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::deleteCustomer()
     */
    public function deleteCustomer($externalId)
    {}

    /**
     * deal with incoming stripe errors in one function
     * submit all errors to HTTPException for now, don't have a good story to CLI Exception handling
     *
     * @param object $e
     *            the exception object
     */
    private function handleStripeError($e)
    {
        
        // Since it's a decline, \Stripe\Error\Card will be caught
        $body = $e->getJsonBody();
        $err = $body['error'];
        $devMessage = '';
        $devMessage .= 'Status: ' . $e->getHttpStatus() . "\n";
        $devMessage .= 'Type: ' . $err['type'] . "\n";
        $devMessage .= 'Param: ' . $err['param'] . "\n";
        $devMessage .= 'Message: ' . $err['message'] . "\n";
        
        // treat as validation error
        if ($err['type'] == 'card_error') {
            throw new ValidationException("Error: " . $err['message'], [
                'dev' => $devMessage,
                'code' => '8494469468464'
            ], [
                'field' => $err['message']
            ]);
        } else {
            
            throw new HTTPException("Could not save Payment Information for account", 404, array(
                'code' => 123123923847,
                'dev' => $devMessage
            ), []);
        }
    }
}

