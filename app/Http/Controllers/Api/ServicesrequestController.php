<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\PushNotification;
use App\Library\StripeGateway;
use App\Models\AdditionalCost;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\RejectPayment;
use App\Models\ServicesRequest;
use App\Models\UserReceipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServicesrequestController extends Controller
{
    public function create(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        try {
            DB::beginTransaction();

            $rules = [
                'customer_id' => 'required',
                'services_provider_id' => 'required',

            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }
            $requestData = $request->all();
            $resourceObj                        = new ServicesRequest;
            $resourceObj->customer_id           = $requestData['customer_id'] ?? NULL;
            $resourceObj->services_provider_id  = $requestData['services_provider_id'] ?? "";
            $resourceObj->service_id            = $requestData['service_id'] ?? "";
            $resourceObj->amount                = $requestData['amount'] ?? "";
            $resourceObj->note                  = $requestData['note'] ?? "";
            $resourceObj->address               = $requestData['address'] ?? "";
            $resourceObj->lati                  = $requestData['lati'] ?? "";
            $resourceObj->long                  = $requestData['long'] ?? "";
            $resourceObj->request_time          = Carbon::now();
            $resourceObj->accept_time           = Carbon::now();
            $resourceObj->payment_status        = $requestData['payment_status'] ?? "pending";
            $resourceObj->request_status        = $requestData['request_status'] ?? "new";

            if ($resourceObj->save()) {
                DB::commit();

                $userId             = $requestData['customer_id'];
                $notificationMsg    = 'Service request success';
                $notificationType   = 'IOS';
                $this->addNotification($userId, $notificationMsg, $notificationType);

                $response['data'] = $resourceObj;
                $response['message'] = 'Service request successfully';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            }
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getRequestServiceProvider(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();

            $response['data'] = ServicesRequest::with('customerDetails')
                ->where('services_provider_id', $requestData['services_provider_id'])
                ->where('request_status', 'new')
                ->orderBy('id', 'desc')
                ->get();

            $response['message'] = 'fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            //DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function customerCheckRequest(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();

            $response['data'] = ServicesRequest::with('servicesProviderDetails')
                ->where('id', $requestData['    '])
                ->where('request_status', 'new')
                ->orderBy('id', 'desc')
                ->get();

            $response['message'] = 'fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            //DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function changeRequestStatus(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $resourceObj = ServicesRequest::with('servicesProviderDetails')
                ->where('id', $requestData['service_request_id'])
                ->first();
            $resourceObj->request_status = $requestData['request_status'];
            $resourceObj->accept_time           = Carbon::now();
            $resourceObj->save();

            $userId             = $resourceObj['customer_id'];
            $notificationMsg    = 'Request status ' . $requestData['request_status'] . ' successfully';
            $notificationType   = 'IOS';
            $this->addNotification($userId, $notificationMsg, $notificationType);


            $response['message'] = 'Request status successfully updated';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            //DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function sendPaymentRequest(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $rules = [
                // 'provider_id' => 'required',
                'user_id' => 'required',
                'booking_id' => 'required',
                'service_cost' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;
            $paymentObj  = new PaymentRequest;
            $paymentObj->provider_id            = $request->user()->id;
            $paymentObj->user_id                = $requestData['user_id'];
            $paymentObj->booking_id             = $requestData['booking_id'];
            $paymentObj->service_cost           = $requestData['service_cost'];
            $paymentObj->total_cost             = $requestData['total_cost'] ?? null;
            $paymentObj->reason                 = $requestData['reason'] ?? null;
            $paymentObj->request_time           = Carbon::now();
            if ($paymentObj->save()) {
                $additionalCost = $request->additional_cost;
                foreach ($additionalCost as $id) {
                    $additionalObj = new AdditionalCost;
                    $additionalObj->payment_req_id = $paymentObj->id;
                    $additionalObj->add_service_cost = $id;
                    $additionalObj->save();
                }
            }
            if ($paymentObj->save()) {
                DB::commit();

                $notificationData = [];

                $notificationType = PAYMENT_REQUEST;
                $notificationMsg = PAYMENT_REQUEST_MSG;


                $paymentObj = PaymentRequest::where('id', $paymentObj['id'])->with('AdditionalCost')->first();
                $paymentObj->send_by = $request->user()->user_type;

                $employerObj = User::find($paymentObj->user_id);
                $device_token = $employerObj->device_token;

                $extraData['payment_detail'] = [
                    'id' => $paymentObj->id
                ];

                $extraData['user_detail'] = [
                    'id' => $request->user()->id,
                    'first_name' => $request->user()->first_name,
                    'last_name' => $request->user()->last_name,
                    'email' => $request->user()->email,
                    'image' => $request->user()->image,
                ];

                $notificationObj = new Notification;
                $notificationObj->user_id = $employerObj->id;
                $notificationObj->message = $notificationMsg;
                $notificationObj->type = $notificationType;
                $notificationObj->data = json_encode($extraData);

                if ($notificationObj->save()) {
                    $dataObj = [];
                    $dataObj['user_type'] = $request->user()->user_type;
                    $dataObj['booking_id'] = $paymentObj->booking_id;

                    $notificationData = [
                        'title' => "Dear " . $employerObj->first_name . ' ' . $employerObj->last_name,
                        'message' => $request->user()->first_name . " " . $request->user()->lasts_name . " - " . $notificationMsg,
                        'device_token' =>  $device_token,
                        'send_by' => APP_NAME,
                        'id' => $paymentObj->id,
                        'type' => $notificationType,
                        'badge' => "1",
                        'data' => $dataObj
                    ];
                    if ($employerObj->notification_status == true) {
                        PushNotification::send($notificationData);
                    }
                }
            }
            $response['data'] = $paymentObj;
            $response['message'] = 'payment request sent successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        return response()->json($response, $response['status']);
    }

    public function customerCheckPaymentRequest(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();

            $response['data'] = Payment::where('payment_by', $requestData['customer_id'])
                ->where('payment_status', 'requested')
                ->orderBy('id', 'desc')
                ->get();

            $response['message'] = 'fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            //DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addNotification($userId, $notificationMsg, $notificationType)
    {
        $notificationObj = new Notification;
        $notificationObj->user_id   = $userId;
        $notificationObj->message   = $notificationMsg;
        $notificationObj->type      = 'null';
        $notificationObj->data      = 'null';
        $notificationObj->save();
    }

    public function rejectPaymentRequest(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $rules = [
                // 'provider_id' => 'required',
                'paymentReq_id' => 'required',
                'message' => 'required',
                'status' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;
            $paymentObj  = new RejectPayment;
            $paymentObj->paymentReq_id          = $requestData['paymentReq_id'];
            $paymentObj->message                = $requestData['message'];
            if ($paymentObj->save()) {
                $rejectObj = PaymentRequest::where(['id' => $requestData['paymentReq_id']])->first();
                if (!$rejectObj) {
                    $data['message'] = "Payment Request Does Not Found";
                    $data['status'] = STATUS_BAD_REQUEST;
                    return response()->json($data, 200);
                }
                if ($request->status == 1) {
                    $rejectObj->payment_status = 2;
                    $notifyType = PAYMENT_REJECT_NOTCOMPLETED;
                    $employerObj = User::find($rejectObj->provider_id);
                    $notifyMsg = $employerObj->first_name . " " . $employerObj->lasts_name . " - " . PAYMENT_REJECT_NOTCOMPLETED_MSG;
                }
                if ($request->status == 2) {
                    $rejectObj->payment_status = 3;
                    $notifyType = PAYMENT_REJECT_INCORRECT;
                    $employerObj = User::find($rejectObj->provider_id);
                    $notifyMsg = $employerObj->first_name . " " . $employerObj->lasts_name . " - " . PAYMENT_REJECT_INCORRECT_MSG;
                }
                if ($request->status == 3) {
                    $rejectObj->payment_status = 4;
                    $notifyType = PAYMENT_REJECT_ADMIN;
                    $employerObj = User::find($rejectObj->provider_id);
                    $notifyMsg = $employerObj->first_name . " " . $employerObj->lasts_name . " - " . PAYMENT_REJECT_ADMIN_MSG;
                }

                if ($request->status == 4) {
                    $rejectObj->payment_status = 1;
                    $notifyType = PAYMENT_SUCCESS;
                    $employerObj = User::find($rejectObj->provider_id);
                    $notifyMsg = $employerObj->first_name . " " . $employerObj->lasts_name . " - " . STRIPE_PAYMENT_SUCCESS;
                }

                if ($rejectObj->save()) {
                    DB::commit();

                    $notificationData = [];
                    $extraData = [];

                    $notificationType = $notifyType;
                    $notificationMsg = $notifyMsg;

                    if ($request->user()->user_type == 'user') {
                        $employerObj = User::find($rejectObj->provider_id);
                        $device_token = $employerObj->device_token;
                    } else {
                        $employerObj = User::find($rejectObj->user_id);
                        $device_token = $employerObj->device_token;
                    }

                    $extraData['payment_detail'] = [
                        'id' => $rejectObj->booking_id,
                    ];

                    $extraData['user_detail'] = [
                        'id' => $request->user()->id,
                        'first_name' => $request->user()->first_name,
                        'last_name' => $request->user()->last_name,
                        'email' => $request->user()->email,
                        'image' => $request->user()->image,
                    ];

                    $notificationObj = new Notification;
                    $notificationObj->user_id = $employerObj->id;
                    $notificationObj->message = $notificationMsg;
                    $notificationObj->type = $notificationType;
                    $notificationObj->data = json_encode($extraData);

                    if ($notificationObj->save()) {
                        $dataObj = [];
                        $dataObj['user_type'] = $request->user()->user_type;
                        $dataObj['booking_id'] = $rejectObj->booking_id;

                        $notificationData = [
                            'title' => "Dear " . $employerObj->first_name . ' ' . $employerObj->last_name,
                            'message' => $notifyMsg,
                            'device_token' =>  $device_token,
                            'send_by' => APP_NAME,
                            'id' => $paymentObj->id,
                            'type' => $notificationType,
                            'badge' => "1",
                            'data' => $dataObj,
                        ];
                        if ($employerObj->notification_status == true) {
                            PushNotification::send($notificationData);
                        }
                    }
                }
            }
            if ($request->status == 4) {
                $responsemsg = STRIPE_PAYMENT_SUCCESS;
            } else {
                $responsemsg = PAYMENT_REJECT;
            }
            $data['user'] = $paymentObj;
            $data['message'] = $responsemsg;
            $data['status'] = STATUS_OK;
            $data['success'] = TRUE;
            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        return response()->json($response, $response['status']);
    }

    public function makePayment(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $rules = [
                'booking_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, $response['status']);
            }
            $requestData = $request->all();
            error_log(print_r($request->all(), true));

            $userId = $request->user()->id;
            $loggedInUserName = $request->user()->first_name . " " . $request->user()->last_name;
            $payObj = PaymentRequest::where(['user_id' => $request->user()->id, 'booking_id' => $requestData['booking_id'], 'payment_status' => 0])->first();

            if (!$payObj) {
                $response['message'] = 'Invalid request';
                return response()->json($response, $response['status']);
            }

            // $payObj->payment_status = $requestData['status'];

            /* error_log(print_r($payObj, true));

            if ($payObj->save()) {
            Make payment if status is 0 */
            if ($payObj->payment_status == 0) {
                $cardObj = Card::find($requestData['card_id']);

                $orderData = [
                    'stripe_customer_id' => $request->user()->stripe_customer_id,
                    'amount' => $payObj->total_cost,
                    'source' => $cardObj->stripe_card_id,
                ];
                $chargeResponse = StripeGateway::createCharge($orderData);
                $chargeData = [];
                if ($chargeResponse['success']) {
                    $chargeObj = $chargeResponse['data'];
                    $chargeData = $chargeObj->jsonSerialize();
                } else {
                    $response['message'] = $chargeResponse['message']; //"Your card information is incorrect";
                    return response()->json($response, $response['status']);
                }

                if (isset($chargeData['status']) && $chargeData['status'] == 'succeeded') {
                    $paymentMessage = STRIPE_PAYMENT_SUCCESS;
                    $paymentMessageType = PAYMENT_SUCCESS;
                } elseif (isset($chargeData['status']) && $chargeData['status'] == 'pending') {
                    $paymentMessage = STRIPE_PAYMENT_PENDING;
                    $paymentMessageType = PAYMENT_PENDING;
                } elseif (isset($chargeData['status']) && $chargeData['status'] == 'failed') {
                    $paymentMessage = STRIPE_PAYMENT_FAILED;
                    $paymentMessageType = PAYMENT_FAILED;
                } else {
                    $paymentMessage = STRIPE_PAYMENT_FAILED;
                    $paymentMessageType = PAYMENT_FAILED;
                }
                $amount = $payObj->total_cost;
                if (isset($chargeData['amount'])) {
                    $amount = ($chargeData['amount'] / 100);
                }
                $paymentObj = new Payment;
                $paymentObj->user_id = $requestData['user_id'];
                $paymentObj->booking_id = $requestData['booking_id'];
                $paymentObj->card_id = $requestData['card_id'];
                /* $paymentObj->payment_by = $request->user()->id; */
                $paymentObj->payment_by = $request->user()->id;
                $paymentObj->amount = $amount;
                /* $paymentObj->amount = $payObj->total_cost; */
                $paymentObj->charge_id = $chargeData['id'] ?? NULL;
                $paymentObj->transaction_id = $chargeData['balance_transaction'] ?? NULL;
                $paymentObj->currency = $chargeData['currency'] ?? NULL;
                $paymentObj->payment_message = $paymentMessage ?? NULL;
                $paymentObj->payment_status = $chargeData['status'] ?? 1;
                $paymentObj->save();

                if ($chargeData['status'] != 'succeeded') {
                    /* $payObj->payment_status = 0;
                    $payObj->save(); */
                    $response['message'] = $chargeResponse['message'];

                    return response()->json($response, $response['status']);
                }
            }

            $payObj->payment_status = 1;
            $payObj->save();

            $notificationMsg = $paymentMessage;
            $notificationType = $paymentMessageType;
            $notificationData = [];
            $extraData = [];

            $notificationUserId = $request->user()->id;
            $notificationId = $requestData['user_id'] ?? $payObj->user_id;

            /* if ($requestData['status'] == '2') {
                    $notificationType = JOB_ACCEPTED;
                    $notificationMsg = ACCEPTED_NOTIFICATION_MESSAGE;
                    $pushNotificationMsg = "Your offer has been accepted for " . $jobObj->title;

                    $response['message'] = "Job has been accepted.";
                } elseif ($requestData['status'] == '3') {
                    $notificationType = JOB_HIRED;
                    $notificationMsg = REJECTED_NOTIFICATION_MESSAGE;
                    $pushNotificationMsg = "Your offer has been rejected for " . $jobObj->title;

                    $response['message'] = "Job has been rejected.";
                } elseif ($requestData['status'] == '4') {
                    $notificationType = ON_THE_WAY;
                    $notificationMsg = $loggedInUserName . " " . ON_THE_WAY_NOTIFICATION_MESSAGE . $jobObj->title;
                    //$pushNotificationMsg = $request->user()->first_name . " " . $request->user()->last_name . " on the way for " . $jobObj->title;
                    $pushNotificationMsg = $notificationMsg;

                    $notificationUserId = $requestData['user_id'];
                    $notificationId = $jobObj->user_id;

                    $response['message'] = $loggedInUserName . " " . "on the way for " . $jobObj->title;
                } elseif ($requestData['status'] == '5') {
                    $notificationType = WORKING_ON;
                    $notificationMsg = $loggedInUserName . " " . WORKING_ON_NOTIFICATION_MESSAGE . $jobObj->title;
                    //$pushNotificationMsg = $request->user()->first_name . " " . $request->user()->last_name . " started working on " . $jobObj->title;
                    $pushNotificationMsg = $notificationMsg;

                    $notificationUserId = $requestData['user_id'];
                    $notificationId = $jobObj->user_id;

                    $response['message'] = $loggedInUserName . " " . "started working on the job" . $jobObj->title;
                } elseif ($requestData['status'] == '6') {
                    $notificationType = JOB_COMPLETED;
                    $notificationMsg = "Your job " . $jobObj->title . " has been completed";
                    $pushNotificationMsg = "Your job " . $jobObj->title . " has been completed";

                    $notificationUserId = $request->user()->id;
                    $notificationId = $requestData['user_id'];

                    $response['message'] = "Job has been completed.";
                } elseif ($requestData['status'] == '7') {
                    $notificationType = JOB_SUBMITTED;
                    $notificationMsg = $jobObj->title . " has been submitted by " . $request->user()->first_name . " " . $request->user()->last_name;
                    $pushNotificationMsg = $notificationMsg;

                    $notificationUserId = $request->user()->id;
                    $notificationId = $jobObj->user_id;

                    $response['message'] = "Job has been submitted.";
                } elseif ($requestData['status'] == '8') {
                    $notificationType = JOB_RATED;
                    $notificationMsg = "Your job " . $jobObj->title . " has been completed";
                    $pushNotificationMsg = "Your job " . $jobObj->title . " has been completed";

                    $notificationUserId = $request->user()->id;
                    $notificationId = $requestData['user_id'];

                    $response['message'] = "You have been rated for job " . $jobObj->title;
                } */
            // IF STATUS IS 6 THEN PAY TO APPLIED USER
            $appliedUserData = User::find($requestData['user_id']);

            if (isset($appliedUserData->stripe_connected_account_id) && !empty($appliedUserData->stripe_connected_account_id)) {

                if (is_null($appliedUserData->card_payments) || is_null($appliedUserData->transfers)) {
                    $data = [
                        'first_name' => $appliedUserData->first_name,
                        'last_name' => $appliedUserData->last_name,
                        'email' => $appliedUserData->email,
                        //'mobile' => $appliedUserData->phone_number_full ?? '+17188795110',
                        'mobile' => '+17188795110',
                        'dob' => [
                            'day' => date("d", strtotime($appliedUserData->dob ?? "1990-07-10")),
                            'month' => date("m", strtotime($appliedUserData->dob ?? "1990-07-10")),
                            'year' => date("Y", strtotime($appliedUserData->dob ?? "1990-07-10")),
                        ],
                        'address' => [
                            'line1' => isset($appliedUserData->address) && !empty($appliedUserData->address) ? $appliedUserData->address : '2049 Frederick Douglass Blvd, New York, NY 10026, United States',
                            'postal_code' => isset($appliedUserData->pincode) && !empty($appliedUserData->pincode) ? $appliedUserData->pincode : '10026',
                            'city' => isset($appliedUserData->city) && !empty($appliedUserData->city) ? $appliedUserData->city : 'New York',
                            'state' => isset($appliedUserData->state) && !empty($appliedUserData->state) ? $appliedUserData->state : 'NY',
                        ],
                        'ssn_last_4' => isset($appliedUserData->ssn_last_4) && !empty($appliedUserData->ssn_last_4) ? $appliedUserData->ssn_last_4 : '0000',
                        'stripe_account_id' => $appliedUserData->stripe_connected_account_id,
                    ];

                    $accountUpdateResponse = StripeGateway::updateAccount($data);
                    if ($accountUpdateResponse['success']) {
                        $accountUpdateData = $accountUpdateResponse['data'];

                        if ($accountUpdateData['capabilities']->card_payments == 'active') {
                            $appliedUserData->card_payments = 'active';
                        }

                        if ($accountUpdateData['capabilities']->transfers == 'active') {
                            $appliedUserData->transfers = 'active';
                        }
                    }
                    $appliedUserData->save();
                }

                if ($appliedUserData->card_payments == 'active' && $appliedUserData->transfers == 'active') {
                    $stripeData = [
                        'stripe_connected_account_id' => $appliedUserData->stripe_connected_account_id,
                        'amount' => $payObj->total_cost,
                        'chargeId' => $paymentObj->charge_id
                    ];
                    $transferResult = StripeGateway::transfer($stripeData);

                    $userReceiptObj = new UserReceipt;
                    $userReceiptObj->user_id = $requestData['user_id'];
                    $userReceiptObj->booking_id = $requestData['booking_id'];
                    $userReceiptObj->amount = $payObj->total_cost;

                    if ($transferResult['success'] == 'succeeded') {
                        $userReceiptObj->stripe_transfer_id = $transferResult['data']->id;
                        $userReceiptObj->payment_status = 'succeeded';
                    } else {
                        $userReceiptObj->payment_status = 'failed';
                    }
                    $userReceiptObj->save();
                    /* if ($userReceiptObj->save()) {
                                if ($userReceiptObj->payment_status == 'succeeded') {
                                    return     $payObj->payment_status = "1";
                                    $payObj->save();
                                }
                            } */
                }
            }



            $notificationUserData = User::find($notificationUserId);

            $extraData['job_detail'] = [
                'id' => $payObj->id,
            ];

            $extraData['user_detail'] = [
                'id' => $notificationUserData->id,
                'first_name' => $notificationUserData->first_name,
                'last_name' => $notificationUserData->last_name,
                'email' => $notificationUserData->email,
                'image' => $notificationUserData->image,
            ];

            $notificationObj = new Notification;
            $notificationObj->user_id = $notificationId;
            $notificationObj->message = $notificationMsg;
            $notificationObj->type = $notificationType;
            $notificationObj->data = json_encode($extraData);

            if ($notificationObj->save()) {
                $dataObj = [];
                $dataObj['user_type'] = $request->user()->user_type;
                $dataObj['booking_id'] = $payObj->booking_id;
                $userObj = User::find($notificationId);

                $notificationData = [
                    'title' => "Dear " . $userObj->first_name . ' ' . $userObj->last_name,
                    'message' => PAYMENT_SEND_MSG . " - " . $notificationUserData->first_name . ' ' . $notificationUserData->last_name,
                    'device_token' => $userObj->device_token,
                    'send_by' => APP_NAME,
                    'id' => $payObj->id,
                    'type' => $notificationType,
                    'badge' => "1",
                    'data' => $dataObj,
                ];

                PushNotification::send($notificationData);
            }
            // }
            $response['message'] = STRIPE_PAYMENT_SUCCESS;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
            $response['data'] = $payObj;
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
}
