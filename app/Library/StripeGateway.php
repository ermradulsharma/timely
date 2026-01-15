<?php

namespace App\Library;

use App\Models\Setting;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Exception;

class StripeGateway
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    public static function setStripeApiKey()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    protected static function getClient(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret_key'));
    }

    protected static function handleException(Exception $e, array &$response)
    {
        if ($e instanceof ApiErrorException) {
            $response['message'] = $e->getError()->message;
            $response['code'] = $e->getError()->code;
        } else {
            $response['message'] = $e->getMessage();
            $response['code'] = "";
        }
    }

    public static function createToken($card = null)
    {
        $response = ['success' => false, 'message' => ""];
        try {
            $stripe = self::getClient();
            $tokenObj = $stripe->tokens->create([
                'card' => [
                    'number' => $card['card_number'],
                    'exp_month' => $card['card_expiry_month'],
                    'exp_year' => $card['card_expiry_year'],
                    'cvc' => $card['card_cvv'],
                ],
            ]);

            if ($tokenObj) {
                $response['token'] = $tokenObj->id;
                $response['success'] = true;
            }
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function createCustomer($data = [])
    {
        $response = ['success' => false];
        try {
            self::setStripeApiKey();

            $customerObj = \Stripe\Customer::create([
                'email' => $data['email'],
                'source'  => $data['token']
            ]);
            $response['success'] = true;
            $response['data'] = $customerObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function createChargeOld($orderData = [])
    {
        $response = ['success' => false];

        $orderAmount = $orderData['amount'] * 100;
        $adminPercentage = config('services.admin.commission_percentage', 10);
        $adminAmount = round($orderAmount * ($adminPercentage / 100));
        $restaurantAmount = $orderAmount - $adminAmount;

        if (isset($orderData['stripe_connected_account_id']) && !empty($orderData['stripe_connected_account_id'])) {
            $stripeData = [
                'customer' => $orderData['stripe_customer_id'],
                'amount' => $adminAmount,
                'currency' => 'usd',
                'source' => $orderData['source'],
                'transfer_data' => [
                    "amount" => $restaurantAmount,
                    "destination" => $orderData['stripe_connected_account_id'],
                ]
            ];
        } else {
            $stripeData = [
                'customer' => $orderData['stripe_customer_id'],
                'amount' => $orderAmount,
                'currency' => 'usd',
                'source' => $orderData['source'],
            ];
        }

        try {
            self::setStripeApiKey();
            $chargeObj = \Stripe\Charge::create($stripeData);
            $response['data'] = $chargeObj;
            $response['success'] = true;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function createCharge($paymentData = [])
    {
        $response = ['success' => false];

        try {
            $stripe = self::getClient();
            $amount = $paymentData['amount'] * 100;

            $charge = $stripe->charges->create([
                'amount' => $amount,
                'currency' => 'usd',
                'customer' => $paymentData['stripe_customer_id'],
                'card' => $paymentData['source'],
            ]);

            $response['data'] = $charge;
            $response['success'] = true;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function createCard($card = null)
    {
        $response = ['success' => false];

        try {
            self::setStripeApiKey();

            $token = $card['token'];
            $customer = \Stripe\Customer::retrieve($card['stripe_customer_id']);
            
            if ($customer->sources) {
                $stripeCard = $customer->sources->create(["source" => $token]);
            } else {
                $stripe = self::getClient();
                $stripeCard = $stripe->customers->createSource(
                    $card['stripe_customer_id'],
                    ['source' => $token]
                );
            }

            $response['success'] = true;
            $response['card'] = $stripeCard;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function deleteCard($card = null)
    {
        $response = ['success' => false];

        try {
            $stripe = self::getClient();
            $result = $stripe->customers->deleteSource(
                $card['stripe_customer_id'],
                $card['stripe_card_id'],
                []
            );

            if ($result->deleted) {
                $response['success'] = true;
            }
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function createAccount($data = [])
    {
        $response = ['success' => false];
        try {
            $stripe = self::getClient();

            $capabilities = [
                'transfers' => ['requested' => true],
            ];
            $tos_acceptance = [
                'ip' => request()->ip(),
                'date' => time(),
            ];

            if ($data['country'] == 'US') {
                $capabilities['card_payments'] = ['requested' => true];
            } else {
                $tos_acceptance['service_agreement'] = 'recipient';
            }

            $accountObj = $stripe->accounts->create([
                'type' => 'custom',
                'country' => $data['country'],
                'business_type' => 'individual',
                'individual' => [
                    'first_name' => $data['name'],
                    'last_name' => $data['last_name'],
                ],
                'email' => $data['email'],
                'capabilities' => $capabilities,
                'tos_acceptance' => $tos_acceptance,
            ]);

            $response['success'] = true;
            $response['data'] = $accountObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function createExternalAccount($data = [])
    {
        $response = ['success' => false];
        try {
            $stripe = self::getClient();

            $capabilities = [
                'transfers' => ['requested' => true],
            ];
            $tos_acceptance = [
                'ip' => request()->ip(),
                'date' => time(),
            ];

            if ($data['country'] == 'US') {
                $capabilities['card_payments'] = ['requested' => true];
            } else {
                $tos_acceptance['service_agreement'] = 'recipient';
            }

            $accountObj = $stripe->accounts->create([
                'type' => 'custom',
                'country' => $data['country'],
                'email' => $data['email'],
                'capabilities' => $capabilities,
                'tos_acceptance' => $tos_acceptance,
            ]);

            $response['success'] = true;
            $response['data'] = $accountObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function getStripeCustomer($data = [])
    {
        $response = ['success' => false];
        try {
            $stripe = self::getClient();

            $accountObj = $stripe->accounts->retrieve(
                $data['stripe_connected_account_id'],
                []
            );

            $response['success'] = true;
            $response['data'] = $accountObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function updateAccount($data = [])
    {
        $response = ['success' => false];
        try {
            $stripe = self::getClient();

            $individual = [
                'first_name' => $data['name'],
                'last_name' => $data['last_name'],
                'dob' => [
                    'day' => $data['dob']['day'],
                    'month' => $data['dob']['month'],
                    'year' => $data['dob']['year'],
                ],
                'address' => [
                    'line1' => $data['address']['line1'],
                    'postal_code' => $data['address']['postal_code'],
                    'city' => $data['address']['city'],
                    'state' => $data['address']['state'],
                ],
                'email' => $data['email'],
                'phone' => $data['mobile']
            ];

            if (!empty($data['ssn_last_4'])) {
                $individual['ssn_last_4'] = $data['ssn_last_4'];
            }

            $tos_acceptance = [
                'ip' => request()->ip(),
                'date' => time(),
            ];

            if ($data['country'] != 'US') {
                $tos_acceptance['service_agreement'] = 'recipient';
            }

            $accountObj = $stripe->accounts->update(
                $data['stripe_account_id'],
                [
                    'business_type' => 'individual',
                    'business_profile' => [
                        'mcc' => 1520,
                        'product_description' => 'Property owner.'
                    ],
                    'individual' => $individual,
                    'tos_acceptance' => $tos_acceptance,
                ]
            );

            $response['success'] = true;
            $response['data'] = $accountObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function getAccountById($accountId)
    {
        $response = ['success' => false];
        try {
            $stripe = self::getClient();
            $accountObj = $stripe->accounts->retrieve($accountId, []);
            $response['success'] = true;
            $response['data'] = $accountObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function createBank($data = [])
    {
        $response = ['success' => false];
        try {
            $stripe = self::getClient();
            $currency = ($data['country'] == 'US') ? 'USD' : 'CAD';

            $bankObj = $stripe->accounts->createExternalAccount(
                $data['stripe_connected_account_id'],
                [
                    'external_account' => [
                        "country" => $data['country'],
                        'currency' => $currency,
                        'account_number' => $data['account_number'],
                        'account_holder_name' => $data['account_holder_name'],
                        "object" => "bank_account",
                        "account_holder_type" => 'individual',
                        "routing_number" => $data['routing_number'] ?? "110000000",
                    ],
                ]
            );

            $response['success'] = true;
            $response['data'] = $bankObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function deleteBank($data = [])
    {
        $response = ['success' => false];
        try {
            $stripe = self::getClient();
            $bankObj = $stripe->accounts->deleteExternalAccount(
                $data['stripe_connected_account_id'],
                $data['stripe_bank_id'],
                []
            );

            $response['success'] = true;
            $response['data'] = $bankObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }

    public static function transfer($data = [])
    {
        $response = ['success' => false];
        try {
            $stripe = self::getClient();

            $adminPercentage = config('services.admin.commission_percentage', 10);
            $adminCommission = $data['amount'] * ($adminPercentage / 100);
            $finalAmount = $data['amount'] - $adminCommission;

            $transferObj = $stripe->transfers->create([
                'amount' => $finalAmount * 100,
                'currency' => 'usd',
                'destination' => $data['stripe_connected_account_id'],
                "source_transaction" => $data['chargeId'],
            ]);

            $response['success'] = true;
            $response['data'] = $transferObj;
        } catch (Exception $e) {
            self::handleException($e, $response);
        }
        return $response;
    }
}
