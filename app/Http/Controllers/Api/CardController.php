<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\StripeGateway;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\Card;
use App\Models\Country;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    public function get(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;

            $response['data'] = Card::where('user_id', $userId)->where('deleted_at', null)->get();
            $response['message'] = 'Card fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            DB::rollback();

            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        if ($response['success']) {
            return response()->json($response, STATUS_OK);
        }
        return response()->json($response, STATUS_BAD_REQUEST);
    }

    public function save(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $rules = [
                //'name_on_card' => 'required',
                'card_number' => 'required|digits_between:13,17|numeric',
                'card_expiry_month' => 'required|numeric',
                'card_expiry_year' => 'required|numeric',
                'card_cvv' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;
            $cardObj = Card::where(['user_id' => $userId, 'card_number' => $requestData['card_number']])->first();
            if ($cardObj) {
                $response['message'] = 'Card already saved';
                return response()->json($response, STATUS_BAD_REQUEST);
            }

            $cardObj = new Card;
            $cardObj->user_id = $userId;
            $cardObj->name_on_card = $requestData['name_on_card'] ?? "";
            $cardObj->card_number = (int)$requestData['card_number'];
            $cardObj->card_last_four = (int)substr($requestData['card_number'], -4);
            $cardObj->card_expiry_month = (int)$requestData['card_expiry_month'];
            $cardObj->card_expiry_year = (int)$requestData['card_expiry_year'];
            $cardObj->card_cvv = (int)$requestData['card_cvv'];
            $cardObj->country = $requestData['country'] ?? "";

            if ($cardObj->save()) {
                $stripeCustomerId = $request->user()->stripe_customer_id;
                if (empty($stripeCustomerId) || is_null($stripeCustomerId)) {
                    $cardObj = Card::find($cardObj->id);

                    $card = [
                        'card_number' => $cardObj->card_number,
                        'card_expiry_month' => $cardObj->card_expiry_month,
                        'card_expiry_year' => $cardObj->card_expiry_year,
                        'card_cvv' => $cardObj->card_cvv,
                    ];

                    $stripeTokenResponse = StripeGateway::createToken($card);
                    if (!$stripeTokenResponse['success']) {
                        Card::where('id', $cardObj->id)->forceDelete();
                        $response['message'] = $stripeTokenResponse['message'];
                        $response['code'] = $stripeTokenResponse['code'] ?? "";

                        return $response;
                    }

                    $stripeToken = $stripeTokenResponse['token'];

                    $customerData = [
                        'email' => $request->user()->email,
                        'token' => $stripeToken,
                    ];

                    $stripeCustomerResponse = StripeGateway::createCustomer($customerData);

                    if ($stripeCustomerResponse['success']) {
                        $stripeCustomer = $stripeCustomerResponse['data'];
                        $userObj = User::find($userId);
                        $userObj->stripe_customer_id = $stripeCustomer->id;
                        $userObj->save();
                    } else {
                        Card::where('id', $cardObj->id)->forceDelete();

                        $response['message'] = $stripeCustomerResponse['message'];
                        $response['code'] = $stripeCustomerResponse['code'] ?? "";

                        return $response;
                    }
                }

                $card = [
                    'card_number' => $cardObj->card_number,
                    'card_expiry_month' => $cardObj->card_expiry_month,
                    'card_expiry_year' => $cardObj->card_expiry_year,
                    'card_cvv' => $cardObj->card_cvv,
                ];
                $stripeTokenResponse = StripeGateway::createToken($card);
                if (!$stripeTokenResponse['success']) {
                    Card::where('id', $cardObj->id)->forceDelete();
                    $response['message'] = $stripeTokenResponse['message'];
                    $response['code'] = $stripeTokenResponse['code'] ?? "";

                    return $response;
                }

                $stripeToken = $stripeTokenResponse['token'];

                $data = [
                    'token' => $stripeToken,
                    'stripe_customer_id' => $request->user()->stripe_customer_id ?? $stripeCustomer->id,
                ];

                $stripeCardData = StripeGateway::createCard($data);
                // dd();
                if ($stripeCardData['success']) {
                    $stripeCardObj = $stripeCardData['card'];
                    $cardObj = Card::find($cardObj->id);
                    $cardObj->stripe_card_id = $stripeCardObj->id;
                    $cardObj->card_number = null;
                    $cardObj->card_last_four = substr($requestData['card_number'], -4);
                    $cardObj->card_expiry_month = $requestData['card_expiry_month'];
                    $cardObj->card_expiry_year = $requestData['card_expiry_year'];
                    $cardObj->card_cvv = NULL;
                    $cardObj->save();
                } else {
                    Card::where('id', $cardObj->id)->forceDelete();

                    $response['message'] = $stripeCardData['message'] ?? "The CVC number is incorrect.";
                    $response['code'] = $stripeCardData['code'] ?? "incorrect_cvc";
                    $response['success'] = FALSE;
                    return response()->json($response, STATUS_UNAUTHORIZED);
                    // return $response;
                }
            }

            $response['data'] = $cardObj;
            $response['message'] = 'Card saved successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        if ($response['success']) {
            return response()->json($response, STATUS_OK);
        }
        return response()->json($response, STATUS_BAD_REQUEST);
    }

    public function delete(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $rules = [
                //'name_on_card' => 'required',
                'card_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;
            $cardObj = Card::where(['user_id' => $userId, 'id' => $requestData['card_id']])->first();
            if (!$cardObj) {
                $data['message'] = "Card Does Not Found";
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, 200);
            }
            //$cardObj = new Card;
            $cardObj->deleted_at = Carbon::now();
            $cardObj->save();

            $response['message'] = 'Card deleted successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            DB::rollback();

            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        if ($response['success']) {
            return response()->json($response, STATUS_OK);
        }
        return response()->json($response, STATUS_BAD_REQUEST);
    }

    public function validateCard(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $rules = [
                'card_id' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $requestData = $request->all();

            $cardObj = Card::where('id', $requestData['card_id'])->first();

            if (!$cardObj) {
                $response['message'] = 'Invalid card id';
                return response()->json($response, STATUS_BAD_REQUEST);
            }

            $card = [
                'card_number' => $cardObj->card_number,
                'card_expiry_month' => $cardObj->card_expiry_month,
                'card_expiry_year' => $cardObj->card_expiry_year,
                'card_cvv' => $cardObj->card_cvv,
            ];
            $stripeTokenResponse = StripeGateway::createToken($card);
            if (!$stripeTokenResponse['success']) {
                Card::where('id', $cardObj->id)->forceDelete();
                $response['message'] = $stripeTokenResponse['message'];
                $response['code'] = $stripeTokenResponse['code'] ?? "";

                return $response;
            }

            $stripeToken = $stripeTokenResponse['token'];

            $data = [
                'token' => $stripeToken,
                'stripe_customer_id' => $request->user()->stripe_customer_id,
            ];

            $stripeCardData = StripeGateway::createCard($data);

            if ($stripeCardData['success']) {
                $response['message'] = 'Valid card';

                // DELETE DUPLICATE CARD
                $stripeCardObj = $stripeCardData['card'];
                $stripeCardId = $stripeCardObj->id;

                $cardData = [
                    'stripe_customer_id' => $request->user()->stripe_customer_id,
                    'stripe_card_id' => $stripeCardId
                ];

                StripeGateway::deleteCard($cardData);
            } else {
                $response['message'] = $stripeCardData['message'] ?? "";
                $response['code'] = $stripeCardData['code'] ?? "";

                return $response;
            }

            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        if ($response['success']) {
            return response()->json($response, STATUS_OK);
        }
        return response()->json($response, STATUS_BAD_REQUEST);
    }

    // Add Bank Details  

    public function saveBank(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                //'bank_name' => 'required',
                'account_holder_name' => 'required',
                'account_number' => 'required',
                'routing_number' => 'required',
                //'ifsc' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $resourceObj = BankAccount::where('user_id', $request->user()->id)->where('account_number', (int)substr($requestData['account_number'], -4))->first();

            if ($resourceObj) {
                $response['message'] = "Bank account already added";
                return response()->json($response, STATUS_BAD_REQUEST);
            }

            $userObj = User::find($request->user()->id);

            $accountId = $request->user()->stripe_connected_account_id;
            if (!$accountId) {
                $stripeData = [
                    'email' => $userObj->email,
                    'name' => $userObj->first_name,
                    'last_name' => $userObj->last_name,
                    'country' => $requestData['country'] ?? "",
                ];

                $accountData = StripeGateway::createAccount($stripeData);

                if ($accountData['success']) {
                    $userObj->stripe_connected_account_id = $accountData['data']['id'];
                    $userObj->bank_verified = "true";
                    $userObj->save();
                } else {
                    $response['message'] = "Oops! something went wrong";
                    return response()->json($response, STATUS_BAD_REQUEST);
                }
            }

            // Save
            $bankData = [
                'stripe_connected_account_id' => $userObj->stripe_connected_account_id,
                'bank_name' => $requestData['bank_name'] ?? '',
                'account_holder_name' => $requestData['account_holder_name'] ?? '',
                'account_number' => (string)$requestData['account_number'] ?? '',
                'routing_number' => (string)$requestData['routing_number'] ?? '',
                'ifsc' => $requestData['ifsc'] ?? '',
                'country' => $requestData['country'] ?? $userObj->country,
            ];
            $bankStripeData = StripeGateway::createBank($bankData);

            if (isset($bankStripeData) && $bankStripeData['success']) {
                $resourceObj = new BankAccount;
                $resourceObj->user_id = $request->user()->id;
                $resourceObj->bank_name = $requestData['bank_name'];
                $resourceObj->account_holder_name = $requestData['account_holder_name'];
                $resourceObj->account_number = (int)substr($requestData['account_number'], -4);
                //$resourceObj->ifsc = $requestData['ifsc'];
                $resourceObj->stripe_bank_id = $bankStripeData['data']->id;
                if ($resourceObj->save()) {
                    $userObj->bank_verified = "true";
                    $userObj->country = $requestData['country'];
                    $userObj->save();
                }
            } else {
                $response['message'] = $bankStripeData['message'];
                return response()->json($response, STATUS_BAD_REQUEST);
            }

            $response['data'] = $resourceObj;
            $response['message'] = 'Bank account added successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getStripeCustomer(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $data['stripe_connected_account_id'] = $requestData['stripe_connected_account_id'];
            $res = StripeGateway::getStripeCustomer($data);

            $response['data'] = $res;
            $response['message'] = 'Customer fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }


    public function updateStripeAccount(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        try {
            $requestData = $request->all();

            $rules = [
                'dob' => 'required',
                'address_line1' => 'required',
                'postal_code' => 'required',
                'city' => 'required',
                'state' => 'required',
                // 'ssn_last_4' => 'required',
                // 'mobile' => 'required',
                'country' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $userObj = User::find($request->user()->id);

            $userObj->print_name = $requestData['print_name'] ?? "";
            $userObj->background_check_date = $requestData['background_check_date'] ?? date('Y-m-d');
            $userObj->background_check_status = $requestData['background_check_status'] ?? false;
            $userObj->background_check_details = $requestData['background_check_details'] ?? "";
            $userObj->accepted_terms_conditions = $requestData['accepted_terms_conditions'] ?? $userObj->accepted_terms_conditions;

            $accountId = $userObj->stripe_connected_account_id;

            if (!$accountId) {
                $stripeData = [
                    'email' => $userObj->email,
                    'name' => $userObj->first_name,
                    'last_name' => $userObj->last_name,
                    'country' => $requestData['country'],
                ];

                $accountData = StripeGateway::createAccount($stripeData);
                // print_r($accountData); dd('jhgf');
                if ($accountData['success']) {
                    $userObj->stripe_connected_account_id = $accountData['data']['id'];
                    // $userObj->country = $accountData['data']['country'];
                    $userObj->save();
                    $accountId = $accountData['data']['id'];
                }
            }

            if (!$accountId) {
                $response['message'] = "Oops! something went wrong";
                return $response;
            }

            $data = [
                'name' => $request->user()->first_name,
                'last_name' => $request->user()->last_name,
                'email' => $request->user()->email,
                'mobile' => $request->user()->mobile ?? '+17188795110',
                'dob' => [
                    'day' => date("d", strtotime($requestData['dob'] ?? "1990-07-10")),
                    'month' => date("m", strtotime($requestData['dob'] ?? "1990-07-10")),
                    'year' => date("Y", strtotime($requestData['dob'] ?? "1990-07-10")),
                ],
                'address' => [
                    'line1' => $requestData['address_line1'] ?? '2049 Frederick Douglass Blvd, New York, NY 10026, United States',
                    'postal_code' => $requestData['postal_code'] ?? '10026',
                    'city' => $requestData['city'] ?? 'New York',
                    'state' => $requestData['state'] ?? 'NY',
                ],
                'ssn_last_4' => '',
                'stripe_account_id' => $accountId,
                'country' => $requestData['country'],
            ];

            if ($request->country == 'US') {
                $data['ssn_last_4'] = $requestData['ssn_last_4'] ?? '0000';
            }

            $res = StripeGateway::updateAccount($data);

            if ($res['success']) {
                $accountUpdateData = $res['data'];
                $userObj->country = $res['data']['country'];
                $userObj->card_payments = 'active';
                $userObj->transfers = 'active';
                $userObj->address_verified = 'true';
            } else {
                $response['message'] = $res['message'];

                return response()->json($response, $response['status']);
            }

            $userObj->save();

            if ($userObj->transfers != 'active') {
                $res = StripeGateway::updateAccount($data);

                if ($res['success']) {
                    $accountUpdateData = $res['data'];

                    $userObj->card_payments = 'active';
                    $userObj->transfers = 'active';
                    $userObj->address_verified = 'true';
                } else {
                    $response['message'] = $res['message'];

                    return response()->json($response, $response['status']);
                }
            }

            $response['data'] = $userObj;
            $response['stripe_data'] = $res ?? NULL;
            $response['message'] = 'Account updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function transfer(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $res = StripeGateway::transfer($requestData);

            $response['data'] = $res;
            $response['message'] = 'Transferred successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateBank(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'bank_id' => 'required',
                'account_holder_name' => 'required',
                'account_number' => 'required',
                // 'ifsc' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $resourceObj = BankAccount::find($requestData['bank_id']);

            if (!$resourceObj) {
                $resourceObj = new BankAccount;
            }

            $resourceObj->bank_name = $requestData['bank_name'];
            $resourceObj->account_holder_name = $requestData['account_holder_name'];
            $resourceObj->account_number = $requestData['account_number'];
            $resourceObj->ifsc = $requestData['ifsc'];
            $resourceObj->save();

            $response['message'] = 'Bank added successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getBank(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $response['data'] = BankAccount::where('user_id', $request->user()->id)->get();

            $response['message'] = 'Bank account fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteBank(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'bank_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $resourceObj = BankAccount::where('id', $requestData['bank_id'])->delete();

            $response['message'] = 'Bank deleted successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getState(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();
            $response['data'] = Country::where('country_code', $requestData['country_code'])->get();
            $response['message'] = 'State fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            DB::rollback();

            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        if ($response['success']) {
            return response()->json($response, STATUS_OK);
        }
        return response()->json($response, STATUS_BAD_REQUEST);
    }
}
