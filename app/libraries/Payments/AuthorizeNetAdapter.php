<?php
namespace PhalconRest\Libraries\Payments;

use Phalcon\DI\Injectable;
use \PhalconRest\Util\HTTPException;
use \PhalconRest\Models;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

// seems to be required by the auth.net api
// hard code auth.net log to safe directory for time being
define("AUTHORIZENET_LOG_FILE", "/tmp/auth_log.txt");

/**
 *
 * @author jjenkins
 *
 */
final class AuthorizeNetAdapter extends Injectable implements Processor
{
    use \PhalconRest\Libraries\Payments\SimpleCardValidation;

    const RESPONSE_OK = "1";

    // const ENDPOINT = \net\authorize\api\constants\ANetEnvironment::SANDBOX;
    const ENDPOINT = \net\authorize\api\constants\ANetEnvironment::PRODUCTION;

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

    function __construct($key, $id)
    {
        $di = \Phalcon\Di::getDefault();
        $this->di = $di;

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($id);
        $merchantAuthentication->setTransactionKey($key);

        $this->merchantAuthentication = $merchantAuthentication;
    }

    /**
     * create a card holder account on the AuthorizeNet system
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::createCustomer()
     */
    public function createCustomer(\PhalconRest\Models\Accounts $account)
    {
        $refId = '1234567';
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

        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setMerchantCustomerId($account->id);
        $customerProfile->setDescription($account->name);

        // populate the email based on primary or billing address
        $primaryEmail = $billingEmail = false;
        foreach ($account->Owners as $owner) {
            if ($owner->billing_contact == 1) {
                $billingEmail = $owner->Users->email;
            }
            if ($owner->primary_contact == 1) {
                $primaryEmail = $owner->Users->email;
            }
        }
        if ($billingEmail) {
            $customerProfile->setEmail($billingEmail);
        } elseif ($primaryEmail) {
            $customerProfile->setEmail($primaryEmail);
        }


        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setRefId($refId);
        $request->setProfile($customerProfile);
        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse(self::ENDPOINT);


        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            $externalId = $response->getCustomerProfileId();

            $account->external_id = $externalId;
            if (!$account->save()) {
                throw new HTTPException("Could not save Payment Information for account", 404, array(
                    'code' => 89984613689191,
                    'dev' => 'AuthorizeNetAdapter->createCustomer failed to save external_id: ' . $externalId
                ), $account->getMessages());
                return false;
            }
            return $externalId;
        } else {
            $this->handleAuthError($response);
            return false;
        }
    }

    /**
     * card_id and account_id represent they external data for each record
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\Libraries\Payments\Processor::chargeCard()
     */
    public function chargeCard($data)
    {
        // assume we use stored card for now, will support a new card soon
        if ($data['amount'] < 1) {
            throw new \Exception('Charge amount must exceed $1.');
        }

        if (isset($data['card_id'])) {
            // verify that the external_id exists in the database
            $card = $this->findCard($data['card_id']);
            if ($card) {
                $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
                $profileToCharge->setCustomerProfileId($data['account_id']);
                $paymentProfile = new AnetAPI\PaymentProfileType();
                $paymentProfile->setPaymentProfileId($data['card_id']);
                $profileToCharge->setPaymentProfile($paymentProfile);
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authCaptureTransaction");
                $transactionRequestType->setAmount($data['amount']);
                $transactionRequestType->setProfile($profileToCharge);
                $request = new AnetAPI\CreateTransactionRequest();
                $request->setMerchantAuthentication($this->merchantAuthentication);
                $request->setTransactionRequest($transactionRequestType);
                $controller = new AnetController\CreateTransactionController($request);
                $response = $controller->executeWithApiResponse(self::ENDPOINT);
                if ($response != null) {
                    $tresponse = $response->getTransactionResponse();
                    if (($tresponse != null) && ($tresponse->getResponseCode() == self::RESPONSE_OK)) {
                        return $tresponse->getTransId();
                    } else {
                        $this->handleAuthError($response);
                    }
                }
            }
            // thow error since the provided card id is not found on api
            throw new HTTPException("Could not charge card on file, the card wasn't found!", 404, array(
                'code' => 49848946486844,
                'dev' => 'Could not find card on remote processor in order to charge it.'
            ), []);
        } else {
            // maybe this is a one time card?
            $chargeData['source'] = [
                // 'brand' => $data['vendor'],
                'address_zip' => $data['zip'],
                'number' => $data['number'],
                'object' => 'card',
                'cvc' => $data['cvc'],
                'exp_year' => $data['expiration_year'],
                'exp_month' => $data['expiration_month'],
                'name' => $data['name_on_card'],
                'address_line1' => $data['address'],
                'email' => $data['email']
            ];

            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($data['number']);
            $creditCard->setExpirationDate($data['expiration_month'] . "/" . $data['expiration_year']);
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);

            $customer = new AnetAPI\CustomerDataType();
            $customer->setEmail($data['email']);

            // create a transaction
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($data['amount']);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setCustomer($customer);

            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($this->merchantAuthentication);
            $request->setTransactionRequest($transactionRequestType);
            $controller = new AnetController\CreateTransactionController($request);
            $response = $controller->executeWithApiResponse(self::ENDPOINT);

            if ($response != null) {
                $tresponse = $response->getTransactionResponse();
                if (($tresponse != null) && ($tresponse->getResponseCode() == self::RESPONSE_OK)) {
                    return $tresponse->getTransId();
                } else {
                    $this->handleAuthError($tresponse);
                }
            } else {
                $this->handleAuthError($response);
            }
        }
    }

    /**
     * either void or refund a card, try void first
     *
     * i get this sometimes...not sure why
     * (string:73) Class net\authorize\api\contract 1\TransactionResponseType does not exist
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\Libraries\Payments\Processor::chargeCard()
     */
    public function refundCharge($data)
    {
        // attempt to void first, then refund
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("voidTransaction");
        $transactionRequestType->setRefTransId($data['charge_id']);
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setTransactionRequest($transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(self::ENDPOINT);
        if ($response != null) {
            $tresponse = $response->getTransactionResponse();
            if (($tresponse != null) && ($tresponse->getResponseCode() == self::RESPONSE_OK)) {
                return $tresponse->getTransId();
            } else {
                // attempt to refund transaction instead
                $payment = \PhalconRest\Models\Payments::findFirst("external_id={$data['charge_id']}");
                if ($payment) {
                    $card = \PhalconRest\Models\Cards::findFirst("id=$payment->card_id");

                    // Create the payment data for a credit card
                    $creditCard = new AnetAPI\CreditCardType();
                    $creditCard->setCardNumber($card->number);
                    $creditCard->setExpirationDate("$card->expiration_month/$card->expiration_year");
                    $paymentOne = new AnetAPI\PaymentType();
                    $paymentOne->setCreditCard($creditCard);

                    // create a transaction
                    $transactionRequest = new AnetAPI\TransactionRequestType();
                    $transactionRequest->setTransactionType("refundTransaction");
                    $transactionRequest->setRefTransId($data['charge_id']);
                    $transactionRequest->setAmount($data['amount']);
                    $transactionRequest->setPayment($paymentOne);

                    $request = new AnetAPI\CreateTransactionRequest();
                    $request->setMerchantAuthentication($this->merchantAuthentication);
                    $request->setTransactionRequest($transactionRequest);
                    $controller = new AnetController\CreateTransactionController($request);
                    $response = $controller->executeWithApiResponse(self::ENDPOINT);
                    if ($response != null) {
                        $tresponse = $response->getTransactionResponse();
                        if (($tresponse != null)) {
                            if ($tresponse->getResponseCode() == self::RESPONSE_OK) {
                                return $tresponse->getTransId();
                            } else {
                                $this->handleTransactionError($tresponse);
                            }
                        } else {
                            // i have a null transaction response....
                            $this->handleTransactionError();
                        }
                    } else {
                        $this->handleAuthError($response);
                    }
                }
            }
        } else {
            $this->handleAuthError($response);
        }
    }


    /**
     * for a card and auth.net account number, create a new payment profile
     *
     * @param $accountExternalId
     * @param object $card
     * @return mixed
     * @throws HTTPException
     * @throws \Exception
     * @throws \PhalconRest\Libraries\Payments\HTTPException
     * @throws \PhalconRest\Util\ValidationException
     *
     * @see \PhalconRest\Libraries\Payments\Processor::createCard()
     *
     */
    public function createCard($accountExternalId, $card)
    {
        // verify some very basic card data first
        $this->verifyCard($card);

        // validate some credit card data
        $customer = $this->findCustomer($accountExternalId);

        $account = \PhalconRest\Models\Accounts::findFirst("external_id = '$accountExternalId'");

        if ($customer) {
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($card->number);
            $creditCard->setExpirationDate("$card->expiration_year-$card->expiration_month");
            $creditCard->setCardCode($card->cvc);
            $paymentCreditCard = new AnetAPI\PaymentType();
            $paymentCreditCard->setCreditCard($creditCard);

            $nameArr = explode(' ', $card->name_on_card);

            // Create the Bill To info for new payment type
            $billto = new AnetAPI\CustomerAddressType();
            // include company name if it's found in a valid account
            if ($account) {
                $billto->setCompany($account->name);
            }

            $billto->setFirstName($nameArr[0]);
            if (count($nameArr) > 1) {
                $billto->setLastName($nameArr[1]);
            }
            $billto->setAddress($card->address);
            $billto->setZip($card->zip);


            // Create a new Customer Payment Profile
            $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
            // $paymentprofile->setCustomerType('individual');
            $paymentProfile->setBillTo($billto);
            $paymentProfile->setPayment($paymentCreditCard);

            // Submit a CreateCustomerPaymentProfileRequest to create a new Customer Payment Profile
            $paymentProfileRequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
            $paymentProfileRequest->setMerchantAuthentication($this->merchantAuthentication);
            // Use an existing profile id
            $paymentProfileRequest->setCustomerProfileId($accountExternalId);
            $paymentProfileRequest->setPaymentProfile($paymentProfile);
            $paymentProfileRequest->setValidationMode("testMode");
            $controller = new AnetController\CreateCustomerPaymentProfileController($paymentProfileRequest);
            $response = $controller->executeWithApiResponse(self::ENDPOINT);
            if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
                return $response->getCustomerPaymentProfileId();
            } else {
                $this->handleAuthError($response);
            }
        } else {
            // hmm.. provided accountExternalId found no customers
            // throw an error instead of attempting to create a customer on the fly
            throw new HTTPException("Could not save card details", 404, array(
                'code' => 65468914684664,
                'dev' => 'Could not locate an existing customer record to attach card to.'
            ), []);
        }
    }

    /**
     * find an existing customer in the Auth.Net system
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::findCustomer()
     */
    public function findCustomer($external_id, $force_api_call = false)
    {
        // simple validation
        if (strlen($external_id) < 4) {
            throw new \Exception('external_id is not long enough');
        }

        // consult w/ cache first
        if (!$force_api_call) {
            if (isset($this->cachedCustomers[$external_id])) {
                return $this->cachedCustomers[$external_id];
            }
        }

        // either force is true or the cache missed, pull from api
        $request = new AnetAPI\GetCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId($external_id);
        $controller = new AnetController\GetCustomerProfileController($request);
        $response = $controller->executeWithApiResponse(self::ENDPOINT);
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            $profileSelected = $response->getProfile();
            $paymentProfilesSelected = $profileSelected->getPaymentProfiles();

            // $profileSelected = $response->getProfile();
            // $external_id = $response->getCustomerProfileId();
            $this->cachedCustomers[$external_id] = $profileSelected;
            return $profileSelected;
        } else {
            $this->handleAuthError($response);
            return false;
        }
    }

    /**
     * find an existing card in the Auth.Net system
     * return a payment profile
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

        // consult w/ cache first
        if (!$force_api_call) {
            if (isset($this->cachedCustomers[$external_id])) {
                return $this->cachedCustomers[$external_id];
            }
        }

        // either force is true or the cache missed, pull from api
        // load customer record in order to request related card record
        $card = \PhalconRest\Models\Cards::findFirst("external_id = '$external_id'");
        $account = $card->Accounts;

        // Set profile ids of profile to be updated
        $request = new AnetAPI\GetCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId($account->external_id);
        $request->setCustomerPaymentProfileId($external_id);

        $controller = new AnetController\GetCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse(self::ENDPOINT);

        if (($response != null) and $response->getMessages()->getResultCode() == "Ok") {
            $this->cachedCards[$card->id] = $response->getPaymentProfile();
            return $response->getPaymentProfile();
        } else {
            $this->handleAuthError($response);
            return false;
        }
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
        // load a card record
        $mm = $this->di->get('modelsManager');
        $cardList = $mm->createBuilder()
            ->from('PhalconRest\\Models\\Cards')
            ->join('PhalconRest\\Models\\Accounts')
            ->where("PhalconRest\\Models\\Cards.external_id = '$externalId'")
            ->getQuery()
            ->execute();

        // remove all cards..in case there are 0 or N
        foreach ($cardList as $card) {
            // issue delete command
            $request = new AnetAPI\DeleteCustomerPaymentProfileRequest();
            $request->setMerchantAuthentication($this->merchantAuthentication);
            $request->setCustomerProfileId($card->Accounts->external_id);
            $request->setCustomerPaymentProfileId($externalId);
            $controller = new AnetController\DeleteCustomerPaymentProfileController($request);
            $response = $controller->executeWithApiResponse(self::ENDPOINT);
            if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
                // meh, return something
                return true;
            } else {
                $this->handleAuthError($response);
            }
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\Libraries\Payments\Processor::deleteCustomer()
     */
    public function deleteCustomer($externalId)
    {
    }

    /**
     * deal with incoming auth errors in one function
     * submit all errors to HTTPException for now, don't have a good story to CLI Exception handling
     *
     * @param object $response
     * @throws HTTPException
     */
    private function handleAuthError($response)
    {
        $devMessage = "Response : " . $response->getMessages()->getMessage()[0]->getCode();
        $devMessage .= " : " . $response->getMessages()->getMessage()[0]->getText();

        throw new HTTPException("General Error working with payment processor.", 404, array(
            'code' => 897841364168489464,
            'more' => $devMessage
        ), []);
    }

    /**
     * deal with incoming stripe errors in one function
     * submit all errors to HTTPException for now, don't have a good story to CLI Exception handling
     *
     * @param object $e
     *            the exception object
     */
    private function handleTransactionError($tresponse = null)
    {
        if ($tresponse) {
            $devMessage = "Transaction Error: " . $tresponse->getResponseCode() . PHP_EOL;
            foreach ($tresponse->getErrors() as $error) {
                $devMessage .= $error->getErrorCode() . " : " . $error->getErrorText() . PHP_EOL;
            }
        } else {
            $devMessage = 'Transaction Error:  Transaction Response was null!';
        }
        throw new HTTPException("Transaction Error working with payment processor.", 404, array(
            'code' => 897841364161913168489464,
            'more' => $devMessage
        ), []);
    }
}
