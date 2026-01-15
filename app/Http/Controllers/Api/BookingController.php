<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\PushNotification;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\ServicesBooking;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function getBookingList(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;

            $booking = Booking::with('provider', 'paymentRequest', 'paymentRequest.rejectRequest', 'user', 'serviceBooking.service')->orderBy('id', 'desc');
            if ($request->user()->user_type == "provider") {
                $booking = $booking->where('provider_send_to', $userId);
            }

            if ($request->user()->user_type == "user") {
                $booking = $booking->where('user_id_send_by', $userId);
            }

            $response['data'] = $booking->get();
            $response['message'] = 'Fetch Service Succcefully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        return response()->json($response, $response['status']);
    }

    public function getBookingDetails(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;
            $validation = Validator::make($request->all(), [
                'booking_id' => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors()->first();
                $data['message'] = $errors;
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, $data['status']);
            }
            $booking = Booking::with('provider', 'paymentRequest', 'paymentRequest.rejectRequest', 'user', 'serviceBooking.service')->orderBy('id', 'desc');
            if ($request->user()->user_type == "provider") {
                $booking = $booking->where('id', $requestData['booking_id']);
            }

            if ($request->user()->user_type == "user") {
                $booking = $booking->where('id', $requestData['booking_id']);
            }

            $response['data'] = $booking->first();
            $response['message'] = 'Fetch Service Succcefully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        return response()->json($response, $response['status']);
    }

    public function bookingAccept(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;
            $validation = Validator::make($request->all(), [
                // 'provider_id' => 'required',
                // 'service_id' => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors()->first();
                $data['message'] = $errors;
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, $data['status']);
            }

            if ($request->user()->user_type == "provider") {
                $booking = Booking::where('provider_send_to', $userId)->find($requestData['booking_id']);
            }

            if ($request->user()->user_type == "user") {
                $booking = Booking::where('user_id_send_by', $userId)->find($requestData['booking_id']);
            }
            if (!$booking) {
                $data['message'] = "Booking Not Found";
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, 200);
            }
            $booking->status = $requestData['status'];
            $booking->save();

            if ($booking->status == 0) {
                $data['message'] = DECLINE;
                $msg = BOOKING_DECLINE_MESSAGE;
                $empObj = User::find($booking->user_id_send_by);
                $notify = $empObj->first_name . " " . $empObj->lasts_name . " - " . BOOKING_DECLINE_MSG;
                $device_token = $empObj->device_token;
            } elseif ($booking->status == 1) {
                $data['message'] = ACCEPT;
                $msg = BOOKING_ACCEPTED_MESSAGE;
                $empObj = User::find($booking->user_id_send_by);
                $notify = $empObj->first_name . " " . $empObj->lasts_name . " - " . BOOKING_ACCEPTED_MSG;
                $device_token = $empObj->device_token;
            } elseif ($booking->status == 2) {
                $data['message'] = NEW_BOOKING;
                $msg = NEW_BOOKING_MESSAGE;
                $empObj = User::find($booking->provider_send_to);
                $notify = $empObj->first_name . " " . $empObj->lasts_name . " - " . NEW_BOOKING_MSG;
                $device_token = $empObj->device_token;
            } elseif ($booking->status == 3) {
                $data['message'] = COMPLETED;
                $msg = BOOKING_COMPLETED_MESSAGE;
                $empObj = User::find($booking->user_id_send_by);
                $notify = $empObj->first_name . " " . $empObj->lasts_name . " - " . BOOKING_COMPLETED_MSG;
                $device_token = $empObj->device_token;
            } elseif ($booking->status == 4) {
                $data['message'] = CANCLE;
                $msg = BOOKING_CANCLE_MESSAGE;
                if ($request->user()->user_type == "provider") {
                    $empObj = User::find($booking->user_id_send_by);
                }
                if ($request->user()->user_type == "user") {
                    $empObj = User::find($booking->provider_send_to);
                }
                $notify = $empObj->first_name . " " . $empObj->lasts_name . " - " . BOOKING_CANCLE_MSG;                
                $device_token = $empObj->device_token;
            }

            if ($booking->save()) {
                DB::commit();

                $notificationMsg = "";
                $notificationType = "";
                $notificationData = [];
                $extraData = [];


                $notificationType = $data['message'];
                $notificationMsg = $notify;


                $jobObj = Booking::where('id', $requestData['booking_id'])->with('provider', 'user')->first();
                $jobObj->send_by = $request->user()->user_type;

                $extraData['booking_detail'] = [
                    'id' => $jobObj->id
                ]; 

                $extraData['user_detail'] = [
                    'id' => $request->user()->id,
                    'first_name' => $request->user()->first_name,
                    'last_name' => $request->user()->last_name,
                    'email' => $request->user()->email,
                    'image' => $request->user()->image,
                ];

                $notificationObj = new Notification;
                $notificationObj->user_id = $empObj->id;
                $notificationObj->message = $notificationMsg;
                $notificationObj->type = $notificationType;
                $notificationObj->data = json_encode($extraData);

                if ($notificationObj->save()) {
                    $dataObj = [];
                    $dataObj['user_type'] = $request->user()->user_type;
                    $dataObj['booking_id'] = $jobObj->id;


                   $notificationData = [
                        'title' => "Dear " . $empObj->first_name . ' ' . $empObj->last_name,
                        'message' => $notify,
                        'device_token' =>  $device_token,
                        'send_by' => APP_NAME,
                        'id' => $jobObj->id,
                        'type' => $notificationType,
                        'badge' => "1",
                        'data' => $dataObj,
                    ];
                    if ($empObj->notification_status == true) {
                        PushNotification::send($notificationData);
                    }
                }
            }

            $data['user'] = $booking;
            $data['status'] = STATUS_OK;
            $data['success'] = TRUE;
            return response()->json($data, 200);
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        return response()->json($response, $response['status']);
    }

    public function bookingService(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $validation = Validator::make($request->all(), [
                'provider_id' => 'required',
                'service_id' => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors()->first();
                $data['message'] = $errors;
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, $data['status']);
            }

            $requestData = $request->all();
            $bookingObj = new Booking;
            $bookingObj->provider_send_to = $requestData['provider_id'];
            $bookingObj->user_id_send_by = $request->user()->id;
            // $bookingObj->service_id = $requestData['service_id'];
            // $bookingObj->price = $requestData['price'] ?? null;
            $bookingObj->lat = $requestData['lat'] ?? null;
            $bookingObj->lng = $requestData['lng'] ?? null;
            $bookingObj->pickup_address = $requestData['pickup_address'];
            $bookingObj->destionation_address = $requestData['destionation_address'];

            if ($bookingObj->save()) {
                $servicesIds = $requestData['service_id'];
                if (!is_array($servicesIds)) {
                    $servicesIds = str_replace(" ", "", $servicesIds);
                    $servicesIds = explode(",", $servicesIds);
                    $servicesIds = array_filter($servicesIds);
                }
                if (count($servicesIds) > 0) {
                    foreach ($servicesIds as $service_id) {
                        $bookservice = new ServicesBooking;
                        $bookservice->booking_id = $bookingObj->id;
                        $bookservice->service_id = $service_id;
                        $bookservice->save();
                    }
                }

                DB::commit();

                $notificationMsg = "";
                $notificationType = "";
                $notificationData = [];
                $extraData = [];

                $notificationType = NEW_BOOKING;
                $notificationMsg = NEW_BOOKING_MESSAGE;

                $jobObj = Booking::find($bookingObj->id);
                $jobObj->send_by = $request->user()->user_type;

                $employerObj = User::find($jobObj->provider_send_to);
                $device_token = $employerObj->device_token;
                
                $extraData['booking_detail'] = [
                    'id' => $jobObj->id,
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
                    $dataObj['booking_id'] = $jobObj->id;

                    // $userObj = User::find($employerId);
                    // $providerObj = User::find($requestData['provider_id']);

                    $notificationData = [
                        'title' => "Dear " . $employerObj->first_name . ' ' . $employerObj->last_name,
                        'message' => $employerObj->first_name . ' ' . $employerObj->last_name . " " . $notificationMsg,
                        'device_token' => $device_token,
                        'send_by' => APP_NAME,
                        'id' => $jobObj->id,
                        'type' => $notificationType,
                        'badge' => "1",
                        'data' => $dataObj
                    ];
                    if ($employerObj->notification_status == true) {
                        PushNotification::send($notificationData);
                    }
                }
            }
            // $bookingObj = Booking::where('id', $bookingObj->id)->first();
            $response['data'] = $bookingObj;
            $response['message'] = 'Your service request has been sent successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }
}
