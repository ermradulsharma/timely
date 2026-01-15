<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\PushNotification;
use App\Library\StripeGateway;
use App\Mail\InterviewSlotBookAlert;
use App\Models\AppliedJob;
use App\Models\Card;
use App\Models\ContractType;
use App\Models\EmploymentType;
use App\Models\FavouriteJob;
use App\Models\InterviewSlot;
use App\Models\InterviewVideo;
use App\Models\Job;
use App\Models\Services;
use App\Models\JobExperience;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Mail;

class JobController extends Controller
{
    public function get(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;

            $response['data'] = Job::where('employer_id', $userId)
                ->with('experience_detail')
                ->with('applied', 'applied.user_detail:id,name,first_name,last_name,email,image')
                ->get()->append([
                    'total_applied_count',
                    'interviewing_count',
                    'hired_count'
                ]);

            $response['message'] = 'Job fetched successfully';
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

    public function detail(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $rules = [
                'job_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;

            //$userType = $request->user()->user_type;

            $jobObj = Job::where('id', $requestData['job_id'])->where('employer_id', $userId)
                ->with('experience_detail')
                ->with('applied', 'applied.user_detail', 'applied.user_detail.skills', 'applied.user_detail.skills.skill_detail:id,title', 'applied.user_detail.work_experiences', 'applied.user_detail.educations');

            $response['data'] = $jobObj->first()->append([
                'total_applied_count',
                'interviewing_count',
                'hired_count'
            ]);

            $response['message'] = 'Job detail fetched successfully';
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

    public function create(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            DB::beginTransaction();

            $rules = [
                'company_name' => 'required',
                //'position' => 'required',
                'title' => 'required',
                'description' => 'required',
                'job_experience_id' => 'required',
                'city' => 'required',
                'state' => 'required',
                'address_line_1' => 'required',
                //'address_line_2' => 'required',
                //'address_line_3' => 'required',
                'skills' => 'required',
                'number_of_position' => 'required',
                'contract_type_id' => 'required',
                'employment_type_id' => 'required',
                'currency' => 'required',
                'hourly_rate' => 'required',
                'qualification' => 'required',
                'benefits' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;

            $resourceObj = new Job;
            $resourceObj->employer_id = $userId;
            $resourceObj->company_name = $requestData['company_name'] ?? NULL;
            $resourceObj->position = $requestData['position'] ?? "";
            $resourceObj->title = $requestData['title'] ?? NULL;
            $resourceObj->description = $requestData['description'] ?? NULL;
            $resourceObj->city = $requestData['city'] ?? NULL;
            $resourceObj->state = $requestData['state'] ?? NULL;
            $resourceObj->address_line_1 = $requestData['address_line_1'] ?? NULL;
            $resourceObj->address_line_2 = $requestData['address_line_2'] ?? NULL;
            //$resourceObj->address_line_3 = $requestData['address_line_3'] ?? NULL;
            $resourceObj->pincode = $requestData['pincode'] ?? NULL;
            $resourceObj->skills = $requestData['skills'] ?? NULL;
            $resourceObj->number_of_position = $requestData['number_of_position'] ?? NULL;
            $resourceObj->contract_type = $requestData['contract_type'] ?? NULL;
            $resourceObj->type_of_employment = $requestData['type_of_employment'] ?? NULL;
            $resourceObj->contract_type_id = $requestData['contract_type_id'] ?? NULL;
            $resourceObj->employment_type_id = $requestData['employment_type_id'] ?? NULL;
            $resourceObj->currency = $requestData['currency'] ?? NULL;
            $resourceObj->hourly_rate = $requestData['hourly_rate'] ?? NULL;
            $resourceObj->payment_type = $requestData['payment_type'] ?? NULL;
            $resourceObj->qualification = $requestData['qualification'] ?? NULL;
            $resourceObj->benefits = $requestData['benefits'] ?? NULL;

            if (isset($requestData['job_experience_id']) && !empty($requestData['job_experience_id'])) {
                $jobExperienceObj = JobExperience::find($requestData['job_experience_id']);

                $resourceObj->job_experience_id = $requestData['job_experience_id'];
                $resourceObj->min_experience = $jobExperienceObj->min;
                $resourceObj->max_experience = $jobExperienceObj->max;
            }

            if ($resourceObj->save()) {
                DB::commit();

                $response['data'] = $resourceObj;
                $response['message'] = 'Job created successfully';
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

    public function update(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            DB::beginTransaction();

            $rules = [
                'resource_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();

            $resourceObj = Job::find($requestData['resource_id']);
            $resourceObj->company_name = $requestData['company_name'] ?? $resourceObj->company_name;
            $resourceObj->position = $requestData['position'] ?? "";
            $resourceObj->title = $requestData['title'] ?? $resourceObj->title;
            $resourceObj->description = $requestData['description'] ?? $resourceObj->description;
            $resourceObj->city = $requestData['city'] ?? $resourceObj->city;
            $resourceObj->state = $requestData['state'] ?? $resourceObj->state;
            $resourceObj->address_line_1 = $requestData['address_line_1'] ?? $resourceObj->address_line_1;
            $resourceObj->address_line_2 = $requestData['address_line_2'] ?? $resourceObj->address_line_2;
            //$resourceObj->address_line_3 = $requestData['address_line_3'] ?? $resourceObj->address_line_3;
            $resourceObj->pincode = $requestData['pincode'] ?? $resourceObj->pincode;
            $resourceObj->skills = $requestData['skills'] ?? $resourceObj->skills;
            $resourceObj->number_of_position = $requestData['number_of_position'] ?? $resourceObj->number_of_position;
            $resourceObj->contract_type = $requestData['contract_type'] ?? $resourceObj->contract_type;
            $resourceObj->type_of_employment = $requestData['type_of_employment'] ?? $resourceObj->type_of_employment;
            $resourceObj->contract_type_id = $requestData['contract_type_id'] ?? $resourceObj->contract_type_id;
            $resourceObj->employment_type_id = $requestData['employment_type_id'] ?? $resourceObj->employment_type_id;
            $resourceObj->currency = $requestData['currency'] ?? $resourceObj->currency;
            $resourceObj->hourly_rate = $requestData['hourly_rate'] ?? $resourceObj->hourly_rate;
            $resourceObj->payment_type = $requestData['payment_type'] ?? $resourceObj->payment_type;
            $resourceObj->qualification = $requestData['qualification'] ?? $resourceObj->qualification;
            $resourceObj->benefits = $requestData['benefits'] ?? $resourceObj->benefits;
            $resourceObj->status = $requestData['status'] ?? $resourceObj->status;

            if (isset($requestData['job_experience_id']) && !empty($requestData['job_experience_id'])) {
                $jobExperienceObj = JobExperience::find($requestData['job_experience_id']);

                $resourceObj->job_experience_id = $requestData['job_experience_id'];
                $resourceObj->min_experience = $jobExperienceObj->min;
                $resourceObj->max_experience = $jobExperienceObj->max;
            }

            if ($resourceObj->save()) {
                DB::commit();

                $response['data'] = $resourceObj;
                $response['message'] = 'Job updated successfully';
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

    public function delete(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            DB::beginTransaction();

            $rules = [
                'resource_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();

            if (Job::where('id', $requestData['resource_id'])->delete()) {
                DB::commit();

                $response['message'] = 'Job deleted successfully';
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

    public function getJobExperience(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $response['data'] = JobExperience::get();

            $response['message'] = 'Job experience fetched successfully';
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

    public function search(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;

            $jobObj = Job::where('status', '0')->with('experience_detail');

            if (isset($requestData['search_term']) && !empty($requestData['search_term'])) {
                $jobObj->whereRaw("(company_name LIKE '%" . $requestData['search_term'] . "%' OR position LIKE '%" . $requestData['search_term'] . "%' OR title LIKE '%" . $requestData['search_term'] . "%')");
            }

            if (isset($requestData['location']) && !empty($requestData['location'])) {
                $jobObj->whereRaw("(city LIKE '" . $requestData['location'] . "' OR state LIKE '%" . $requestData['location'] . "%' OR address_line_1 LIKE '%" . $requestData['location'] . "%' OR address_line_2 LIKE '%" . $requestData['location'] . "%' OR address_line_3 LIKE '%" . $requestData['location'] . "%' OR pincode LIKE '%" . $requestData['location'] . "%')");
            }

            $response['data'] = $jobObj->get()->append(['job_status', 'is_favourite']);

            $response['message'] = 'Job fetched successfully';
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

    public function getAppliedJob(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;

            $jobObj = Job::with('experience_detail');

            $response['data'] = $jobObj
                ->with('applied_job_detail')
                ->whereHas('applied_job_detail')
                ->get();

            $response['message'] = 'Job fetched successfully';
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

    public function apply(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            DB::beginTransaction();

            $rules = [
                'job_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;

            $resourceObj = AppliedJob::where(['user_id' => $userId, 'job_id' => $requestData['job_id']])->first();

            if (!$resourceObj) {
                $resourceObj = new AppliedJob;
                $resourceObj->user_id = $userId;
                $resourceObj->job_id = $requestData['job_id'];
            }

            if ($resourceObj->save()) {
                DB::commit();

                $notificationMsg = "";
                $notificationType = "";
                $notificationData = [];
                $extraData = [];

                $notificationType = JOB_APPLIED;
                $notificationMsg = JOB_APPLIED_MESSAGE;

                $jobObj = Job::find($requestData['job_id']);
                $employerId = $jobObj->employer_id;

                $extraData['job_detail'] = [
                    'id' => $jobObj->id,
                    'title' => $jobObj->title,
                    'description' => $jobObj->description,
                ];

                $extraData['user_detail'] = [
                    'id' => $request->user()->id,
                    'first_name' => $request->user()->first_name,
                    'last_name' => $request->user()->last_name,
                    'email' => $request->user()->email,
                    'image' => $request->user()->image,
                ];

                $notificationObj = new Notification;
                $notificationObj->user_id = $employerId;
                $notificationObj->message = $notificationMsg;
                $notificationObj->type = $notificationType;
                $notificationObj->data = json_encode($extraData);

                if ($notificationObj->save()) {
                    $employerObj = User::find($employerId);

                    $notificationData = [
                        'title' => "Dear " . $employerObj->first_name,
                        'message' => $request->user()->first_name . " " . $notificationMsg,
                        'device_token' => $employerObj->device_token,
                        'send_by' => APP_NAME,
                        'id' => $jobObj->id,
                        'type' => $notificationType,
                    ];

                    PushNotification::send($notificationData);
                }
            }

            $response['data'] = AppliedJob::where('id', $resourceObj->id)->with('job_detail')->first();
            $response['message'] = 'Job applied successfully';
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

    public function hireOld(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            DB::beginTransaction();

            $rules = [
                'job_id' => 'required',
                'user_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();

            $userId = $requestData['user_id'];

            $jobObj = Job::find($requestData['job_id']);
            $appliedJobObj = AppliedJob::where(['user_id' => $userId, 'job_id' => $requestData['job_id']])->first();

            $numberOfPosition = $jobObj->number_of_position;
            $numberOfHired = AppliedJob::where(['status' => '3', 'job_id' => $requestData['job_id']])->count();

            if ($numberOfHired < $numberOfPosition) {
                $appliedJobObj->status = '3';

                if ($appliedJobObj->save()) {
                    $jobObj->status = '1';

                    if ($jobObj->save()) {
                        DB::commit();

                        $response['data'] = Job::where('id', $jobObj->id)
                            ->with('experience_detail')
                            ->with('applied', 'applied.user_detail:id,name,first_name,last_name,email,image')
                            ->first();
                        $response['message'] = 'Hired successfully';
                        $response['success'] = TRUE;
                        $response['status'] = STATUS_OK;
                    }
                }
            } else {
                $response['message'] = 'Job already closed';
            }
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function hire(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            DB::beginTransaction();

            $rules = [
                'job_id' => 'required',
                'user_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();

            $userId = $requestData['user_id'];

            $jobObj = Job::find($requestData['job_id']);
            $appliedJobObj = AppliedJob::where(['user_id' => $userId, 'job_id' => $requestData['job_id']])->first();

            $appliedJobObj->status = '3';

            if ($appliedJobObj->save()) {
                $jobObj->status = '1';

                if ($jobObj->save()) {
                    DB::commit();

                    // Paid to employee
                    $cardObj = Card::find($requestData['card_id']);

                    $orderData = [
                        'stripe_customer_id' => $request->user()->stripe_customer_id,
                        'amount' => JOB_PRICE,
                        'source' => $cardObj->stripe_card_id,
                    ];
                    $chargeResponse = StripeGateway::createCharge($orderData);
                    $chargeData = [];
                    if ($chargeResponse['success']) {
                        $chargeObj = $chargeResponse['data'];
                        $chargeData = $chargeObj->jsonSerialize();
                    }

                    if (isset($chargeData['status']) && $chargeData['status'] == 'succeeded') {
                        $paymentMessage = STRIPE_PAYMENT_SUCCESS;
                    } elseif (isset($chargeData['status']) && $chargeData['status'] == 'pending') {
                        $paymentMessage = STRIPE_PAYMENT_PENDING;
                    } elseif (isset($chargeData['status']) && $chargeData['status'] == 'failed') {
                        $paymentMessage = STRIPE_PAYMENT_FAILED;
                    } else {
                        $paymentMessage = STRIPE_PAYMENT_FAILED;
                    }

                    $paymentObj = new Payment;
                    $paymentObj->user_id = $requestData['user_id'];
                    $paymentObj->job_id = $requestData['job_id'];
                    $paymentObj->card_id = $requestData['card_id'];
                    $paymentObj->payment_by = $request->user()->id;
                    //$paymentObj->amount = ($chargeData['amount'] / 100);
                    $paymentObj->amount = JOB_PRICE;
                    $paymentObj->charge_id = $chargeData['id'] ?? NULL;
                    $paymentObj->transaction_id = $chargeData['balance_transaction'] ?? NULL;
                    $paymentObj->currency = $chargeData['currency'] ?? NULL;
                    $paymentObj->payment_message = $paymentMessage ?? NULL;
                    $paymentObj->payment_status = $chargeData['status'] ?? NULL;
                    $paymentObj->save();

                    $response['data'] = Job::where('id', $jobObj->id)
                        ->with('experience_detail')
                        ->with('applied', 'applied.user_detail:id,name,first_name,last_name,email,image')
                        ->first();
                    $response['message'] = 'Hired successfully';
                    $response['success'] = TRUE;
                    $response['status'] = STATUS_OK;
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addToFavourite(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $rules = [
                'job_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();

            $userId = $request->user()->id;

            $resourceObj = FavouriteJob::where(['user_id' => $userId, 'job_id' => $requestData['job_id']])->first();
            if (!$resourceObj) {
                $resourceObj = new FavouriteJob;
                $resourceObj->user_id = $userId;
                $resourceObj->job_id = $requestData['job_id'];
            }

            $resourceObj->save();

            $response['message'] = 'Added to favorite successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getFavourite(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;

            $jobObj = Job::with('experience_detail');

            $response['data'] = $jobObj
                ->with('favourite_detail')
                ->whereHas('favourite_detail')
                ->get()->append(['job_status']);

            $response['message'] = 'Job fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteFavourite(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $rules = [
                'job_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();

            FavouriteJob::where('job_id', $requestData['job_id'])->where('user_id', $request->user()->id)->delete();

            $response['message'] = 'Favorite deleted successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getInterviewVideo(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;

            $jobObj = InterviewVideo::with('questions');

            $response['data'] = $jobObj->get();

            $response['message'] = 'Interview video fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function contractEmploymentTypes(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $response['contract_type'] = ContractType::get();
            $response['employment_type'] = EmploymentType::get();

            $response['message'] = 'Contract employment type fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateAppliedJobStatus(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            //DB::beginTransaction();

            $rules = [
                'job_id' => 'required',
                'user_id' => 'required',
                'status' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();

            if ($requestData['status'] > 4) {
                $response['message'] = 'Invalid status';

                return response()->json($response, $response['status']);
            }

            $userId = $requestData['user_id'];

            $jobObj = Job::find($requestData['job_id']);

            $completeCount = AppliedJob::where(['status' => '4', 'job_id' => $requestData['job_id']])->count();
            $numberOfPosition = $jobObj->number_of_position;

            if ($requestData['status'] == '4') {
                /* if ($completeCount == $numberOfPosition) {
                    $response['message'] = "Job already closed";
                    return $response;
                } */
            }

            $appliedJobObj = AppliedJob::where(['user_id' => $userId, 'job_id' => $requestData['job_id']])->first();
            $appliedJobObj->status = $requestData['status'];

            if ($appliedJobObj->save()) {
                $completeCount = AppliedJob::where(['status' => '4', 'job_id' => $requestData['job_id']])->count();

                if ($completeCount == $numberOfPosition) {
                    //$jobObj->status = '1';
                    //$jobObj->save();
                }

                $notificationMsg = "";
                $notificationType = "";
                $notificationData = [];
                $extraData = [];

                if ($requestData['status'] == '2') {
                    $notificationType = JOB_SHORTLISTED;
                    $notificationMsg = SHORTLISTED_NOTIFICATION_MESSAGE;
                } elseif ($requestData['status'] == '3') {
                    $notificationType = JOB_HIRED;
                    $notificationMsg = HIRED_NOTIFICATION_MESSAGE;
                } elseif ($requestData['status'] == '4') {
                    $notificationType = JOB_COMPLETED;
                    $notificationMsg = COMPLETED_NOTIFICATION_MESSAGE;
                }

                $employerObj = User::find($jobObj->employer_id);

                $extraData['job_detail'] = [
                    'id' => $jobObj->id,
                    'title' => $jobObj->title,
                    'description' => $jobObj->description,
                ];

                $extraData['employer_detail'] = [
                    'id' => $employerObj->id,
                    'first_name' => $employerObj->first_name,
                    'last_name' => $employerObj->last_name,
                    'email' => $employerObj->email,
                    'image' => $employerObj->image,
                ];

                $notificationObj = new Notification;
                $notificationObj->user_id = $userId;
                $notificationObj->message = $notificationMsg;
                $notificationObj->type = $notificationType;
                $notificationObj->data = json_encode($extraData);

                if ($notificationObj->save()) {
                    $userObj = User::find($userId);

                    $notificationData = [
                        'title' => "Dear " . $userObj->first_name,
                        'message' => $notificationMsg,
                        'device_token' => $userObj->device_token,
                        'send_by' => APP_NAME,
                        'id' => $jobObj->id,
                        'type' => $notificationType,
                    ];

                    PushNotification::send($notificationData);
                }
            }

            if ($requestData['status'] == '3' && $request->user()->user_type == 'employer') {
                // Paid to employee
                $cardObj = Card::find($requestData['card_id']);

                $orderData = [
                    'stripe_customer_id' => $request->user()->stripe_customer_id,
                    'amount' => JOB_PRICE,
                    'source' => $cardObj->stripe_card_id,
                ];
                $chargeResponse = StripeGateway::createCharge($orderData);
                $chargeData = [];
                if ($chargeResponse['success']) {
                    $chargeObj = $chargeResponse['data'];
                    $chargeData = $chargeObj->jsonSerialize();
                }

                if (isset($chargeData['status']) && $chargeData['status'] == 'succeeded') {
                    $paymentMessage = STRIPE_PAYMENT_SUCCESS;
                } elseif (isset($chargeData['status']) && $chargeData['status'] == 'pending') {
                    $paymentMessage = STRIPE_PAYMENT_PENDING;
                } elseif (isset($chargeData['status']) && $chargeData['status'] == 'failed') {
                    $paymentMessage = STRIPE_PAYMENT_FAILED;
                } else {
                    $paymentMessage = STRIPE_PAYMENT_FAILED;
                }

                $paymentObj = new Payment;
                $paymentObj->user_id = $requestData['user_id'];
                $paymentObj->job_id = $requestData['job_id'];
                $paymentObj->card_id = $requestData['card_id'];
                $paymentObj->payment_by = $request->user()->id;
                //$paymentObj->amount = ($chargeData['amount'] / 100);
                $paymentObj->amount = JOB_PRICE;
                $paymentObj->charge_id = $chargeData['id'] ?? NULL;
                $paymentObj->transaction_id = $chargeData['balance_transaction'] ?? NULL;
                $paymentObj->currency = $chargeData['currency'] ?? NULL;
                $paymentObj->payment_message = $paymentMessage ?? NULL;
                $paymentObj->payment_status = $chargeData['status'] ?? NULL;
                $paymentObj->save();
            }

            $response['message'] = 'Applied job status updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
            $response['data'] = $jobObj;
        } catch (Exception $e) {
            //DB::rollback();
            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateStatus(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            //DB::beginTransaction();

            $rules = [
                'job_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();

            $jobObj = Job::find($requestData['job_id']);
            if (!$jobObj) {
                $response['message'] = 'Invalid job id';

                return response()->json($response, $response['status']);
            }

            $jobObj->status = $requestData['status'];
            $jobObj->save();

            $response['message'] = 'Job status updated successfully';
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

    public function interviewSlot(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();

            $response['data'] = InterviewSlot::where('user_id', NULL)->orderBy('slot_date', 'ASC')->orderBy('start_time', 'ASC')->get();

            $response['message'] = 'Interview slot fetched successfully';
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

    public function bookInterviewSlot(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            //DB::beginTransaction();

            $rules = [
                'slot_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;

            $slotObj = InterviewSlot::find($requestData['slot_id']);

            if (!$slotObj) {
                $response['message'] = 'Invalid job id';

                return response()->json($response, $response['status']);
            }

            if (!empty($slotObj->user_id)) {
                $response['message'] = 'Slot already booked';

                return response()->json($response, $response['status']);
            }

            $slotObj->user_id = $userId;
            $slotObj->booking_time = date("Y-m-d H:i:s");

            if ($slotObj->save()) {
                $mailData = [];

                $adminObj = User::where('user_type', 'admin')->first();

                $mailData['slot_detail'] = $slotObj;
                $mailData['user_detail'] = $request->user();
                $mailData['admin_detail'] = $adminObj;

                Mail::to($adminObj->email)->send(new InterviewSlotBookAlert($mailData));
            }

            $response['message'] = 'Slot booked successfully';
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
}
