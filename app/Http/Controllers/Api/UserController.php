<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\RequestResponse;
use App\Mail\ForgotPassword;
use App\Models\AppliedJob;
use App\Models\FavouriteJob;
use App\Models\ForgotPasswordMail;
use App\Models\JobExperience;
use App\Models\Notification;
use App\Models\OtpVerification;
use App\Models\RequestResponseLog;
use App\Models\Skill;
use App\Models\Subscription;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use App\Models\User;
use App\Models\Booking;
use App\Models\Category;
use App\Models\ProviderCategory;
use App\Models\Rating;
use App\Models\ServiceProvider;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserSkill;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function updateProfileprovider(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();

            /*$rules = [
                'name' => 'required',
                'email' => 'required',
            ];*/
            $rules = [];
            $userId = $request->user()->id;

            if (isset($requestData['email']) && !empty($requestData['email'])) {
                $rules['email'] = [
                    'email' => Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            if (isset($requestData['mobile']) && !empty($requestData['mobile'])) {
                $rules['mobile'] = [
                    Rule::unique('users', 'mobile')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $userId = $request->user()->id;
            $userObj = User::find($userId);
            $userObj->name = $requestData['name'] ?? $userObj->name;
            $userObj->mobile = $requestData['mobile'] ?? $userObj->mobile;
            $userObj->city = $requestData['city'] ?? $userObj->city;
            $userObj->state = $requestData['state'] ?? $userObj->state;
            $userObj->country = $requestData['country'] ?? $userObj->country;
            $userObj->address = $requestData['address'] ?? $userObj->address;
            $userObj->pincode = $requestData['pincode'] ?? $userObj->pincode;
            $userObj->company_name = $requestData['company_name'] ?? $userObj->company_name;
            $userObj->user_type = $requestData['user_type'] ?? "provider";
            $userObj->lat = $requestData['lat'] ?? NULL;
            $userObj->lng = $requestData['lng'] ?? NULL;
            $userObj->is_profile_updated = '1';

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(IMAGE_UPLOAD_PATH, $fileName);
                $userObj->image = $fileName;
            }
            if ($userObj->save()) {

                $lat = $userObj->lat ?? NULL;
                $lng = $userObj->lng ?? NULL;
                if ($lat && $lng) {
                    saveGeolocation(DB::class, 'users', $userObj->id, $lat, $lng);
                }
            }


            // if (isset($requestData['services'])) {
            //     foreach ($requestData['services']  as $id) {
            //         $serviceProvider = new ServiceProvider;
            //         $serviceProvider->provider_id = $request->user()->id;
            //         $serviceProvider->service_id = $id;
            //         $serviceProvider->save();
            //     }
            // }

            $response['data'] = $userObj;
            $response['message'] = 'Profile updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    //==== old ======
    public function Add_profile_provider(Request $request)
    {
        try {
            $requestData = $request->all();

            /*$rules = [
                'name' => 'required',
                'email' => 'required',
            ];*/
            $rules = [];
            $userId = $request->user()->id;
            if (isset($requestData['email']) && !empty($requestData['email'])) {
                $rules['email'] = [
                    'email' => Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            // if (isset($requestData['mobile']) && !empty($requestData['mobile'])) {
            //     $rules['mobile'] = [
            //         Rule::unique('users', 'mobile')->ignore($userId)->whereNull('deleted_at')
            //     ];
            // }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $userId = $request->user()->id;
            $userObj = User::find($userId);
            $userObj->first_name = $requestData['first_name'] ?? $userObj->first_name;
            $userObj->last_name = $requestData['last_name'] ?? $userObj->last_name;
            $userObj->name =  $userObj->first_name . ' ' . $userObj->last_name;
            $userObj->mobile = $requestData['mobile'] ?? $userObj->mobile;
            $userObj->city = $requestData['city'] ?? $userObj->city;
            $userObj->state = $requestData['state'] ?? $userObj->state;
            $userObj->country = $requestData['country'] ?? $userObj->country;
            $userObj->address = $requestData['address'] ?? $userObj->address;
            $userObj->pincode = $requestData['pincode'] ?? $userObj->pincode;
            $userObj->status = $requestData['status'] ?? $userObj->status;
            $userObj->provider_id_number = $requestData['provider_id_number'] ?? "";
            $userObj->is_profile_updated = '1';
            $userObj->lat = $requestData['lat'] ?? "";
            $userObj->lng = $requestData['lng'] ?? "";

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(IMAGE_UPLOAD_PATH, $fileName);
                $userObj->image = $fileName;
            }


            if ($userObj->save()) {

                $lat = $userObj->lat ?? "";
                $lng = $userObj->lng ?? "";
                if ($lat != '' && $lng != '' || $lat != null && $lng != null) {
                    saveGeolocation(DB::class, 'users', $userObj->id, $lat, $lng);
                }
            }
            $servicesIds = $requestData['services'] ?? [];
            if (!is_array($servicesIds)) {
                $servicesIds = str_replace(" ", "", $servicesIds);
                $servicesIds = explode(",", $servicesIds);
                $servicesIds = array_filter($servicesIds);
            }
            Services::whereIn('id', $servicesIds)->where('user_id', $request->user()->id)->update(['is_active' => true]);
            Services::whereNotIn('id', $servicesIds)->where('user_id', $request->user()->id)->update(['is_active' => false]);


            if (count($servicesIds) > 0) {
                ServiceProvider::with('servicedetails', 'userdetails')->where('provider_id', $userObj->id)->delete();
                foreach ($servicesIds as $id) {
                    $serviceProvider = new ServiceProvider;
                    $serviceProvider->provider_id = $request->user()->id;
                    $serviceProvider->category_id = $requestData['category_id'];
                    $serviceProvider->service_id = $id ?? "";
                    $serviceProvider->save();
                }
            }


            $providers =  ServiceProvider::with('servicedetails', 'userdetails')->where('provider_id', $userObj->id)->get();
            foreach ($providers as $providerdetails) {
                $response['data'] = $providerdetails;
            }
            $userObj = User::where('id', $userObj->id)->with('service_provider')->first();
            $response['message'] = ' Provider Profile add successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
            $response['data'] = $userObj;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    //=== updated v2 ====
    public function Add_profile_provider_v2(Request $request)
    {
        try {
            $requestData = $request->all();

            /*$rules = [
                'name' => 'required',
                'email' => 'required',
            ];*/
            $rules = [];
            $userId = $request->user()->id;
            if (isset($requestData['email']) && !empty($requestData['email'])) {
                $rules['email'] = [
                    'email' => Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            // if (isset($requestData['mobile']) && !empty($requestData['mobile'])) {
            //     $rules['mobile'] = [
            //         Rule::unique('users', 'mobile')->ignore($userId)->whereNull('deleted_at')
            //     ];
            // }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $userId = $request->user()->id;
            $userObj = User::find($userId);
            $userObj->first_name = $requestData['first_name'] ?? $userObj->first_name;
            $userObj->last_name = $requestData['last_name'] ?? $userObj->last_name;
            $userObj->name =  $userObj->first_name . ' ' . $userObj->last_name;
            $userObj->mobile = $requestData['mobile'] ?? $userObj->mobile;
            $userObj->city = $requestData['city'] ?? $userObj->city;
            $userObj->state = $requestData['state'] ?? $userObj->state;
            $userObj->country = $requestData['country'] ?? $userObj->country;
            $userObj->address = $requestData['address'] ?? $userObj->address;
            $userObj->pincode = $requestData['pincode'] ?? $userObj->pincode;
            $userObj->status = $requestData['status'] ?? $userObj->status;
            $userObj->provider_id_number = $requestData['provider_id_number'] ?? "";
            $userObj->is_profile_updated = '1';
            $userObj->lat = $requestData['lat'] ?? "";
            $userObj->lng = $requestData['lng'] ?? "";

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(IMAGE_UPLOAD_PATH, $fileName);
                $userObj->image = $fileName;
            }

            if ($userObj->save()) {
                $lat = $userObj->lat ?? "";
                $lng = $userObj->lng ?? "";
                if ($lat != "" && $lng != "") {
                    saveGeolocation(DB::class, 'users', $userObj->id, $lat, $lng);
                }
            }
            $categories = json_decode($requestData['categories'], true) ?? [];
            $categoryArr = [];
            foreach ($categories as $category) {
                $serviceObj = ServiceProvider::where(['category_id' => $category['category_id'], 'provider_id' => $userId])->first();
                if (!$serviceObj) {
                    $serviceObj = new ServiceProvider;
                    $serviceObj->provider_id = $userId;
                    $serviceObj->category_id = $category['category_id'];
                }
                $price = (float)$category['price'] ?? 0.00;
                $serviceObj->price = (float)$price;
                $serviceObj->save();
                $categoryArr[] = $category['category_id'];
            }

            ServiceProvider::whereNotIn('category_id', $categoryArr)->where(['provider_id' => $userId])->delete();
            $userObj = User::where('id', $userObj->id)->with('service_provider')->first();
            $response['message'] = ' Provider Profile added successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
            $response['data'] = $userObj;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function get_profile_list(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        $requestData = $request->all();
        $userId = $request->user()->id ?? 0;
        $service_ids = $requestData['service_id'] ?? null;
        $lat = $requestData['lat'] ?? NULL;
        $lng = $requestData['lng'] ?? NULL;
        error_log(print_r($lat, true));
        error_log(print_r($lng, true));
        if ($lat == NULL || $lng == NULL || $lat == 0.0 || $lng == 0.0) {
            try {
                $service_providers = ServiceProvider::with('userdetails.service_provider.servicedetails')->where('category_id', $service_ids)->get();
                $response['data'] = $service_providers;
                $response['message'] = 'Services fetched successfully';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
                Log::error($e->getTraceAsString());
                $response['status'] = STATUS_GENERAL_ERROR;
            }
            return response()->json($response, $response['status']);
        } else {
            try {

                $kms = (config('custom.search_distance_limit') * 1.609); # Default 80.46 KMS, 50 Miles, 1 miles = 1.609 Kms
                $userQuery = User::where('is_verified', true)->selectRaw("users.*")->selectRaw('*, users.created_at AS created_time, users.updated_at AS updated_time, ST_Distance(users.geolocation, ST_MakePoint(?,?)::geography) AS distance', [$lng, $lat])
                    ->whereRaw("ST_Distance(users.geolocation, ST_MakePoint($lng,$lat)::geography) < " . $kms * 1000);
                $userIds = $userQuery->pluck('id');
                $service_providers = ServiceProvider::with('userdetails.service_provider.servicedetails')->whereIn('provider_id', $userIds)->where('category_id', $service_ids)->select('provider_id')->groupBy('provider_id')->get();
                if (!empty($service_providers)) {
                    foreach ($service_providers as $key => $provider) {
                        $averageRating = Rating::where('rating_send_by', $provider->provider_id)->get()->avg('rating') ?? 0;
                        $service_providers[$key]->averageRating = number_format((float)$averageRating, 1, '.', '');
                        $service_providers[$key]->review_count = Rating::where('rating_send_by', $provider->provider_id)->count();
                    }
                }
                $response['message'] = 'Services fetched successfully';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
                $response['data'] = $service_providers;
            } catch (Exception $e) {
                DB::rollback();
                $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
                Log::error($e->getTraceAsString());
                $response['status'] = STATUS_GENERAL_ERROR;
            }
            return response()->json($response, $response['status']);
        }
    }


    public function get_profile_list_v2(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        $requestData = $request->all();
        $userId = $request->user()->id ?? 0;
        $service_ids = $requestData['service_id'] ?? null;
        $lat = $requestData['lat'] ?? NULL;
        $lng = $requestData['lng'] ?? NULL;
        error_log(print_r($lat, true));
        error_log(print_r($lng, true));
        if ($lat == NULL || $lng == NULL || $lat == 0.0 || $lng == 0.0) {
            try {
                $service_providers = ServiceProvider::with('userdetails.service_provider.servicedetails')->where('category_id', [$service_ids])->where('is_verified', true)->get();
                $response['data'] = $service_providers;
                $response['message'] = 'Services fetched successfully';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
                Log::error($e->getTraceAsString());
                $response['status'] = STATUS_GENERAL_ERROR;
            }
            return response()->json($response, $response['status']);
        } else {
            try {

                $kms = (config('custom.search_distance_limit') * 1.609); # Default 80.46 KMS, 50 Miles, 1 miles = 1.609 Kms
                $userQuery = User::where('is_verified', true)->selectRaw("users.*")->selectRaw('*, users.created_at AS created_time, users.updated_at AS updated_time, ST_Distance(users.geolocation, ST_MakePoint(?,?)::geography) AS distance', [$lng, $lat])
                    ->whereRaw("ST_Distance(users.geolocation, ST_MakePoint($lng,$lat)::geography) <= " . $kms * 1000);
                $userIds = $userQuery->pluck('id');
                $service_providers = ServiceProvider::with('userdetails.service_provider.servicedetails')->whereIn('provider_id', $userIds)->where('category_id', [$service_ids])->select('provider_id')->groupBy('provider_id')->get();
                if (!empty($service_providers)) {
                    foreach ($service_providers as $key => $provider) {
                        $averageRating = Rating::where('rating_send_by', $provider->provider_id)->get()->avg('rating') ?? 0;
                        $service_providers[$key]->averageRating = number_format((float)$averageRating, 1, '.', '');
                        $service_providers[$key]->review_count = Rating::where('rating_send_by', $provider->provider_id)->count();
                    }
                }
                $response['message'] = 'Services fetched successfully';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
                $response['data'] = $service_providers;
            } catch (Exception $e) {
                DB::rollback();
                $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
                Log::error($e->getTraceAsString());
                $response['status'] = STATUS_GENERAL_ERROR;
            }
            return response()->json($response, $response['status']);
        }
    }

    public function get_profile_list_new(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;
        $requestData = $request->all();
        $userId = $request->user()->id ?? 0;
        // $service_ids = $requestData['service_id'] ?? null;
        $lat = $requestData['lat'] ?? NULL;
        $lng = $requestData['lng'] ?? NULL;

        if ($lat == NULL || $lng == NULL || $lat == 0.0 || $lng == 0.0) {
            try {
                $service_providers = ServiceProvider::with('userdetails.service_provider.servicedetails')->get();
                $response['data'] = $service_providers;
                $response['message'] = 'Services fetched successfully';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
                Log::error($e->getTraceAsString());
                $response['status'] = STATUS_GENERAL_ERROR;
            }
            return response()->json($response, $response['status']);
        } else {
            try {
                $kms = (config('custom.search_distance_limit') * 1.609); # Default 80.46 KMS, 50 Miles, 1 miles = 1.609 Kms
                $userQuery = User::selectRaw("users.*")->selectRaw('*, users.created_at AS created_time, users.updated_at AS updated_time, ST_Distance(users.geolocation, ST_MakePoint(?,?)::geography) AS distance', [$lng, $lat])
                    ->whereRaw("ST_Distance(users.geolocation, ST_MakePoint($lng,$lat)::geography) <= " . $kms * 1000);
                $userIds = $userQuery->pluck('id');
                error_log(print_r($userIds, true));
                $service_providers = ServiceProvider::with('userdetails.service_provider.servicedetails')->whereIn('provider_id', $userIds)->get();

                $response['message'] = 'Services fetched successfully';
                $response['success'] = TRUE;
                $response['status'] = STATUS_OK;
                $response['data'] = $service_providers;
            } catch (Exception $e) {
                DB::rollback();
                $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
                Log::error($e->getTraceAsString());
                $response['status'] = STATUS_GENERAL_ERROR;
            }
            return response()->json($response, $response['status']);
        }
    }

    //hfghfg
    public function signup(Request $request)
    {
        error_log(print_r($request->all(), true));
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            DB::beginTransaction();
            $requestData = $request->all();
            $rules = [
                //'name' => 'required',
                'email' => ['required'],
                'password' => 'required|min:6',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                throw new Exception($errorResponse['message'], $response['status']);
            }
            $userObjSocialCheck = User::where('email', $requestData['email'])->where('password', '=', SOCIAL_PASS)->whereNull('deleted_at')->first();
            if ($userObjSocialCheck) {
                $userObj = $userObjSocialCheck;
            } else {
                $userObjSocialCheck = User::where('email', $requestData['email'])->whereNull('deleted_at')->first();

                if ($userObjSocialCheck) {
                    $response['message'] = "The email has already been taken.";

                    return response()->json($response, $response['status']);
                } else {
                    $userObj = new User();
                }
            }
            $requestData = $request->all();
            $userObj = new User();
            $userObj->name = $requestData['name'] ?? NULL;
            $userObj->first_name = $requestData['first_name'] ?? NULL;
            $userObj->last_name = $requestData['last_name'] ?? NULL;
            $userObj->email = strtolower($requestData['email']);
            $userObj->password = bcrypt($requestData['password']);
            $userObj->country_code = $requestData['country_code'] ?? NULL;
            $userObj->mobile = $requestData['mobile'] ?? NULL;
            $userObj->city = $requestData['city'] ?? NULL;
            $userObj->state = $requestData['state'] ?? NULL;
            $userObj->country = $requestData['country'] ?? NULL;
            $userObj->address = $requestData['address'] ?? NULL;
            $userObj->pincode = $requestData['pincode'] ?? NULL;
            $userObj->user_type = $requestData['user_type'] ?? 'user';
            $userObj->device_token = $requestData['device_token'] ?? "";
            $userObj->device_type = $requestData['device_type'] ?? "";
            $userObj->company_name = $requestData['company_name'] ?? NULL;
            $userObj->position = $requestData['position'] ?? NULL;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(IMAGE_UPLOAD_PATH, $fileName);
                $userObj->image = $fileName;
            }

            $userObj->save();
            $response['message'] = 'Registration successful';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
            $userId = $userObj->id;

            $userData = User::where('id', $userId)->with('subscription')->first();
            $userData->resume = $userData->id . ".pdf";
            $userData->save();
            $response['data'] = $userData;

            $token = $userData->createToken($userData->id . ' token ')->accessToken;
            if ($token) {
                DB::commit();
            }

            $response['access_token'] = $token;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            DB::rollback();
            unset($response['data']);
            return $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = $e->getCode() ?? STATUS_GENERAL_ERROR;

            $requestResponseLogData = [
                'type' => 'error',
                'action' => 'Signup',
                'end_point' => '',
                'request_params' => $request->all(),
                'response' => $response,
                'extra' => [],
                'log_date' => date("Y-m-d"),
            ];
            RequestResponse::save($requestResponseLogData);
        }
        return response()->json($response, $response['status']);
    }

    public function login(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errorResponse = validation_error_response($validator->errors()->toArray());
            return response()->json($errorResponse, STATUS_BAD_REQUEST);
        }

        $requestData = $request->all();

        $checkUser = User::checkUser($request->all());

        if (!isset($checkUser['user'])) {
            $response['message'] = $checkUser['message'];
            $response['status'] = STATUS_UNAUTHORIZED;

            $requestResponseLogData = [
                'type' => 'error',
                'action' => 'Login',
                'end_point' => '',
                'request_params' => $request->all(),
                'response' => $response,
                'extra' => [],
                'log_date' => date("Y-m-d"),
            ];

            RequestResponse::save($requestResponseLogData);

            return $response;
        }

        try {
            $user = $checkUser['user'];

            if ($user->user_type == 'admin') {
                $user = Auth::user()->token();
                $user->revoke();

                $response['message'] = "You are not authorize to access";
                return $response;
            }

            // Update device token
            $userObj = User::where('id', $user->id)->with('subscription')->first();
            $userObj->device_token = $requestData['device_token'] ?? "";
            $userObj->device_type = $requestData['device_type'] ?? "";

            // if (is_null($userObj->resume) || empty($userObj->resume)) {
            //     $userObj->resume = $userObj->id . ".pdf";
            // }

            $userObj->save();

            $token = $user->createToken($user->id . ' token ')->accessToken;

            $response['message'] = "Login successfully";
            $response['access_token'] = $token;
            $response['data'] = $userObj;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;

            $requestResponseLogData = [
                'type' => 'error',
                'action' => 'Login',
                'end_point' => '',
                'request_params' => $request->all(),
                'response' => $response,
                'extra' => [],
                'log_date' => date("Y-m-d"),
            ];

            RequestResponse::save($requestResponseLogData);
        }

        return response()->json($response, $response['status']);
    }

    public function socialLogin(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        try {
            $post_data = $request->all();
            $requestData = $request->all();

            // $post_data['name'] = $post_data['name'] ?? "";
            $post_data['first_name'] = $post_data['first_name'] ?? "  ";
            $post_data['last_name'] = $post_data['last_name'] ?? "  ";

            //$rules['email'] = 'required';
            // $rules['name'] = 'required';
            //$rules['first_name'] = 'required';
            // $rules['last_name'] = 'required';
            $rules['user_type'] = 'required|In:user,provider';
            $rules['type'] = 'required';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $response['message'] = $validator->errors()->first();
                $response['status'] = UNPROCESSABLE_ENTITY;
                return response()->json($response, $response['status']);
            }

            $isNewAccount = FALSE;

            if ($request->get('type') == 1) {
                $rules['facebook_id'] = 'required';

                if (!isset($post_data['email']) || empty($post_data['email'])) {
                    $post_data['email'] = $post_data['facebook_id'] . "@facebook.com";
                }

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $response['message'] = $validator->errors()->first();
                    $response['status'] = UNPROCESSABLE_ENTITY;
                    return response()->json($response, $response['status']);
                }

                $check_email_exist = User::where('email', $post_data['email'])->count();

                if ($check_email_exist) {
                    $userObj = User::where('email', $post_data['email'])->first();
                    $check_facebook_id_exist = User::where('email', $post_data['email'])->where('facebook_id', $post_data['facebook_id'])->count();

                    if (!$check_facebook_id_exist) {
                        $userObj->facebook_id = $post_data['facebook_id'];
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userObj->username = $post_data['username'];
                            }
                        }

                        $userObj->save();
                    } else {
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;
                        $userObj->save();
                    }
                } else {
                    $isNewAccount = TRUE;

                    $userObj = new User;
                    $userObj->facebook_id = $post_data['facebook_id'];
                    // $userObj->name = $post_data['name'] ?? NULL;
                    $userObj->first_name = $post_data['first_name'] ?? "";
                    $userObj->last_name = $post_data['last_name'] ?? "";
                    $userObj->name = $userObj->first_name . " " . $userObj->last_name;
                    $userObj->email = $post_data['email'];
                    if (isset($post_data['username'])) {
                        if ($post_data['username'] != NULL && $post_data['username'] != '') {
                            $userObj->username = $post_data['username'];
                        }
                    }
                    $userObj->password = SOCIAL_PASS;
                    $userObj->save();
                }
            } elseif ($request->get('type') == 2) {
                $rules['google_id'] = 'required';
                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $response['message'] = $validator->errors()->first();
                    $response['status'] = UNPROCESSABLE_ENTITY;
                    return response()->json($response, $response['status']);
                }

                if (!isset($post_data['email']) || empty($post_data['email'])) {
                    $post_data['email'] = $post_data['google_id'] . "@gmail.com";
                }
                $check_email_exist = User::where('email', $post_data['email'])->count();
                if ($check_email_exist) {
                    $userObj = User::where('email', $post_data['email'])->first();
                    $check_google_id_exist = User::where('email', $post_data['email'])->where('google_id', $post_data['google_id'])->count();

                    if (!$check_google_id_exist) {
                        $userObj->google_id = $post_data['google_id'];
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userObj->username = $post_data['username'];
                            }
                        }
                        $userObj->save();
                    } else {
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;
                        $userObj->save();
                    }
                } else {
                    $isNewAccount = TRUE;
                    $userObj = new User;
                    $userObj->google_id = $post_data['google_id'];
                    // $userObj->name = $post_data['name'] ?? NULL;
                    $userObj->first_name = $post_data['first_name'] ?? "";
                    $userObj->last_name = $post_data['last_name'] ?? "";
                    $userObj->name = $userObj->first_name . " " . $userObj->last_name;
                    $userObj->email = $post_data['email'];
                    if (isset($post_data['username'])) {
                        if ($post_data['name'] != NULL && $post_data['name'] != '') {
                            $userObj->username = $post_data['name'];
                        }
                    }
                    $userObj->password = SOCIAL_PASS;
                    $userObj->save();
                }
            } elseif ($request->get('type') == 3) {
                $rules['twitter_id'] = 'required';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $response['message'] = $validator->errors()->first();
                    $response['status'] = UNPROCESSABLE_ENTITY;
                    return response()->json($response, $response['status']);
                }
                if (!isset($post_data['email']) || empty($post_data['email'])) {
                    $post_data['email'] = $post_data['twitter_id'] . "@twitter.com";
                }
                $check_email_exist = User::where('email', $post_data['email'])->count();

                if ($check_email_exist) {
                    $userObj = User::where('email', $post_data['email'])->first();
                    $check_twitter_id_exist = User::where('email', $post_data['email'])->where('twitter_id', $post_data['twitter_id'])->count();

                    if (!$check_twitter_id_exist) {
                        $userObj->twitter_id = $post_data['twitter_id'];
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userObj->username = $post_data['username'];
                            }
                        }

                        $userObj->save();
                    } else {
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        $userObj->save();
                    }
                } else {
                    $isNewAccount = TRUE;

                    $userObj = new User;
                    $userObj->twitter_id = $post_data['twitter_id'];
                    // $userObj->name = $post_data['name'] ?? NULL;
                    $userObj->first_name = $post_data['first_name'] ?? "";
                    $userObj->last_name = $post_data['last_name'] ?? "";
                    $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                    $userObj->email = $post_data['email'];
                    if (isset($post_data['username'])) {
                        if ($post_data['username'] != NULL && $post_data['username'] != '') {
                            $userObj->username = $post_data['username'];
                        }
                    }
                    $userObj->password = SOCIAL_PASS;
                    $userObj->save();
                }
            } elseif ($request->get('type') == 4) {
                $rules['instagram_id'] = 'required';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $response['message'] = $validator->errors()->first();
                    $response['status'] = UNPROCESSABLE_ENTITY;
                    return response()->json($response, $response['status']);
                }

                $check_email_exist = User::where('email', $post_data['email'])->count();

                if ($check_email_exist) {
                    $userObj = User::where('email', $post_data['email'])->first();
                    $check_instagram_id_exist = User::where('email', $post_data['email'])->where('instagram_id', $post_data['instagram_id'])->count();

                    if (!$check_instagram_id_exist) {
                        $userObj->instagram_id = $post_data['instagram_id'];
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userObj->username = $post_data['username'];
                            }
                        }

                        $userObj->save();
                    } else {
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        $userObj->save();
                    }
                } else {
                    $isNewAccount = TRUE;

                    $userObj = new User;
                    $userObj->instagram_id = $post_data['instagram_id'];
                    // $userObj->name = $post_data['name'] ?? NULL;
                    $userObj->first_name = $post_data['first_name'] ?? "";
                    $userObj->last_name = $post_data['last_name'] ?? "";
                    $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                    $userObj->email = $post_data['email'];
                    if (isset($post_data['username'])) {
                        if ($post_data['username'] != NULL && $post_data['username'] != '') {
                            $userObj->username = $post_data['username'];
                        }
                    }
                    $userObj->password = SOCIAL_PASS;
                    $userObj->save();
                }
            } elseif ($request->get('type') == 5) {
                $rules['apple_id'] = 'required';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $response['message'] = $validator->errors()->first();
                    $response['status'] = UNPROCESSABLE_ENTITY;
                    return response()->json($response, $response['status']);
                }
                if (!isset($post_data['email']) || empty($post_data['email'])) {
                    $post_data['email'] = $post_data['apple_id'] . "@apple.com";
                }
                $check_email_exist = User::where('email', $post_data['email'])->first();

                if ($check_email_exist) {
                    $userObj = User::where('email', $post_data['email'])->first();
                    $check_apple_id_exist = User::where('email', $post_data['email'])->where('apple_id', $post_data['apple_id'])->count();

                    if (!$check_apple_id_exist) {
                        $userObj->apple_id = $post_data['apple_id'];
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userObj->username = $post_data['username'];
                            }
                        }

                        $userObj->save();
                    } else {
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        $userObj->save();
                    }
                } else {
                    // Check apple id exist or not
                    $userObj = User::where('apple_id', $post_data['apple_id'])->first();

                    if ($userObj) {
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        $userObj->email = $post_data['email'];
                        $userObj->save();
                    } else {
                        $isNewAccount = TRUE;

                        $userObj = new User;
                        $userObj->apple_id = $post_data['apple_id'];
                        // $userObj->name = $post_data['name'] ?? NULL;
                        $userObj->first_name = $post_data['first_name'] ?? "";
                        $userObj->last_name = $post_data['last_name'] ?? "";
                        $userObj->name = $userObj->first_name . " " . $userObj->last_name;

                        $userObj->email = $post_data['email'];
                        if (isset($post_data['username'])) {
                            if ($post_data['username'] != NULL && $post_data['username'] != '') {
                                $userObj->username = $post_data['username'];
                            }
                        }
                        $userObj->password = SOCIAL_PASS;
                        $userObj->save();
                    }
                }
            }

            $response['message'] = LOGIN_SUCCESSFULLY;
            $user_id = $userObj->id;
            $token = $userObj->createToken($user_id . ' token ')->accessToken;
            $userObj = User::select('id', 'first_name', 'last_name', 'email', 'image', 'user_type')->where('id', $userObj->id)->first();
            $userObj->device_token = $post_data['device_token'] ?? "";
            $userObj->device_type = $post_data['device_type'] ?? "";
            $userObj->lat = $requestData['lat'] ?? $userObj->lat;
            $userObj->lng = $requestData['lng'] ?? $userObj->lng;

            if ($isNewAccount) {
                $userObj->user_type = $requestData['user_type'] ?? 'user';
            }

            $userObj->save();

            // Save point
            $lat = $userObj->lat;
            $lng = $userObj->lng;

            if ($lat && $lng) {
                DB::insert("UPDATE users SET geolocation = ST_MakePoint($lng, $lat) WHERE id = '" . $userObj->id . "'");
            }
            $userObj = User::where('id', $userObj->id)->first();
            $userObj->access_token = $token;
            $response['is_new_account'] = $isNewAccount;
            $response['data'] = $userObj;
            $response['access_token'] = $token;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return $response;
    }

    public function forgotPassword(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        $rules = [
            'email' => 'required|email|max:255'
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $userObj = User::where('email', strtolower($request->get('email')))->first();
            if (!$userObj) {
                $response['message'] = "Email id does not exist.";
                $response['status'] = STATUS_NOT_FOUND;
                return $response;
            }

            $token = generateRandomToken(50, $request->get('email'));

            $tokenMailObj = ForgotPasswordMail::where('email', $request->get('email'))->first();
            if (!$tokenMailObj) {
                $tokenMailObj = new ForgotPasswordMail;
            }

            $tokenMailObj->email = $request->get('email');
            $tokenMailObj->token = $token;

            $currentTime = date("Y-m-d H:i:s");
            $mailExpireTime = date('Y-m-d H:i:s', strtotime('+60 minutes', strtotime($currentTime)));

            $tokenMailObj->expired_at = $mailExpireTime;
            $tokenMailObj->save();

            $mailData = [];
            $mailData['name'] = $userObj->name ?? '';

            $mailData['link'] = route('password.reset', [$token, 'email' => $request->get('email')]);

            Mail::to($request->get('email'))->send(new ForgotPassword($mailData));

            $response['message'] = 'Please check your email to reset password';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            //$response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            $response['message'] = "Oops! some error occured, please try again";
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    private function sendResetEmail($email, $token)
    {
        //Retrieve the user from the database
        $user = DB::table('users')->where('email', $email)->select('name', 'email')->first();
        //Generate, the password reset link. The token generated is embedded in the link
        $link = config('base_url') . 'password/reset/' . $token . '?email=' . urlencode($user->email);

        try {
            //Here send the link with CURL with an external email API
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function changePassword(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        $rules = [
            'current_password' => 'required|min:6',
            'password_confirmation' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $requestData = $request->all();

            $userId = $request->user()->id;
            $userObj = User::find($userId);

            if (!Hash::check($request->get('current_password'), $userObj->password)) {
                $response['message'] = "Wrong current password";
                $response['status'] = STATUS_BAD_REQUEST;
                return response()->json($response, STATUS_BAD_REQUEST);
            }

            $userObj->password = bcrypt($requestData['password']);
            $userObj->save();

            $response['message'] = 'Password changed successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getassistanceProfile(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();
            $userId = $requestData['profile_id'];

            $userObj = User::where('id', $userId)->first();
            $response['data'] = $userObj;
            $response['message'] = 'Profile detail fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateGuardianProfile(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            /*$rules = [
                'name' => 'required',
                'email' => 'required',
            ];*/
            $rules = [];
            //$userId = $request->user()->id;
            $userId = $request->user_id;

            if (isset($requestData['mobile']) && !empty($requestData['mobile'])) {
                $rules['mobile'] = [
                    Rule::unique('users', 'mobile')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            //$userId = $request->user()->id;
            $userObj = User::find($userId);
            $userObj->name = $requestData['name'] ?? $userObj->name;
            $userObj->mobile = $requestData['mobile'] ?? $userObj->mobile;
            $userObj->address = $requestData['address'] ?? $userObj->address;
            $userObj->company_name = $requestData['company_name'] ?? $userObj->company_name;
            $userObj->services_provided = $requestData['services_provided'] ?? $userObj->services_provided;

            $userObj->is_profile_updated = '1';

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(IMAGE_UPLOAD_PATH, $fileName);
                $userObj->image = $fileName;
            }

            $userId             = $request->user_id;
            $notificationMsg    = 'Profile updated successfully';
            $notificationType   = 'IOS';
            $this->addNotification($userId, $notificationMsg, $notificationType);

            $userObj->save();
            $response['data'] = $userObj;
            $response['message'] = 'Guardian Profile updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateProfile(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();

            /*$rules = [
                'name' => 'required',
                'email' => 'required',
            ];*/
            $rules = [];
            $userId = $request->user()->id;

            if (isset($requestData['email']) && !empty($requestData['email'])) {
                $rules['email'] = [
                    'email' => Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            if (isset($requestData['mobile']) && !empty($requestData['mobile'])) {
                $rules['mobile'] = [
                    Rule::unique('users', 'mobile')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $userId = $request->user()->id;
            $userObj = User::find($userId);
            $userObj->name = $requestData['name'] ?? $userObj->name;
            $userObj->first_name = $requestData['first_name'] ?? $userObj->first_name;
            $userObj->last_name = $requestData['last_name'] ?? $userObj->last_name;
            $userObj->email = strtolower($requestData['email']) ?? $userObj->email;
            $userObj->age = $requestData['age'] ?? $userObj->age;
            $userObj->gender = $requestData['gender'] ?? $userObj->gender;
            $userObj->country_code = $requestData['country_code'] ?? $userObj->country_code;
            $userObj->mobile = $requestData['mobile'] ?? $userObj->mobile;
            $userObj->city = $requestData['city'] ?? $userObj->city;
            $userObj->state = $requestData['state'] ?? $userObj->state;
            $userObj->country = $requestData['country'] ?? $userObj->country;
            $userObj->address = $requestData['address'] ?? $userObj->address;
            $userObj->pincode = $requestData['pincode'] ?? $userObj->pincode;
            $userObj->summary = $requestData['summary'] ?? $userObj->summary;
            $userObj->company_name = $requestData['company_name'] ?? $userObj->company_name;
            $userObj->position = $requestData['position'] ?? $userObj->position;
            $userObj->status = $requestData['status'] ?? $userObj->status;
            $userObj->user_type = $requestData['user_type'] ?? $userObj->user_type;
            $userObj->is_profile_updated = '1';

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(IMAGE_UPLOAD_PATH, $fileName);
                $userObj->image = $fileName;
            }
            $userObj->save();


            $response['data'] = $userObj;
            $response['message'] = 'Profile updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function profileDetail(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();
            $userId =  $request->user()->id;
            $userObj = User::where('id', $userId);

            if ($request->user()->user_type == 'user') {
                $userObj = $userObj->get();
            }

            if ($request->user()->user_type == 'provider') {
                $userObj = $userObj->with('services', 'services.servicedetails')->get();
            }
            $response['data'] = $userObj;
            $response['message'] = 'Profile detail fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateProfilePic(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        $rules = [
            'image' => 'required'
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $userId = $request->user()->id;
            $userObj = User::find($userId);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(IMAGE_UPLOAD_PATH, $fileName);
                $userObj->image = $fileName;
            }
            $userObj->save();

            $response['message'] = 'Profile pic updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deactivateAccount(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $userObj = User::find($request->user()->id);
            $userObj->delete();

            $response['message'] = 'Account deactivated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function logout(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $userId = $request->user()->id;
            $user = Auth::user()->token();
            $user->revoke();

            $requestData = $request->all();

            $response['message'] = 'Logout successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateLatLng(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $rules = [
                'lat' => 'required',
                'lng' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $requestData = $request->all();

            $userId = $request->user()->id;
            $userObj = User::find($userId);
            $userObj->lat = $requestData['lat'];
            $userObj->lng = $requestData['lng'];
            $userObj->save();

            $response['message'] = 'Latitude longitude updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateDeviceToken(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $rules = [
                'device_type' => 'required',
                'device_token' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $requestData = $request->all();

            $userId = $request->user()->id;
            $userObj = User::find($userId);
            $userObj->device_type = $requestData['device_type'];
            $userObj->device_token = $requestData['device_token'];
            $userObj->save();

            $response['message'] = 'Device token updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addWorkExperience(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'title' => 'required',
                'employer' => 'required',
                'city' => 'required',
                'state' => 'required',
                'start_date' => 'required',
                //'end_date' => 'required',
            ];

            if (isset($requestData['is_currently_here']) && !empty($requestData['is_currently_here']) && $requestData['is_currently_here'] != '1') {
                $rules['end_date'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $userId = $request->user()->id;
            $resourceObj = new UserExperience;
            $resourceObj->user_id = $userId;
            $resourceObj->title = $requestData['title'] ?? NULL;
            $resourceObj->description = $requestData['description'] ?? NULL;
            $resourceObj->employer = $requestData['employer'] ?? NULL;
            $resourceObj->city = $requestData['city'] ?? NULL;
            $resourceObj->state = $requestData['state'] ?? NULL;
            $resourceObj->start_date = $requestData['start_date'] ?? NULL;
            $resourceObj->end_date = $requestData['end_date'] ?? NULL;
            $resourceObj->is_currently_here = $requestData['is_currently_here'] ?? '0';
            $resourceObj->save();

            $response['data'] = $resourceObj;
            $response['message'] = 'Work experience added successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateWorkExperience(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'resource_id' => 'required',
            ];

            if (isset($requestData['is_currently_here']) && !empty($requestData['is_currently_here']) && $requestData['is_currently_here'] != '1') {
                $rules['end_date'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $userId = $request->user()->id;
            $resourceObj = UserExperience::find($requestData['resource_id']);
            $resourceObj->user_id = $userId;
            $resourceObj->title = $requestData['title'] ?? $resourceObj->title;
            $resourceObj->description = $requestData['description'] ?? NULL;
            $resourceObj->employer = $requestData['employer'] ?? $resourceObj->employer;
            $resourceObj->city = $requestData['city'] ?? $resourceObj->city;
            $resourceObj->state = $requestData['state'] ?? $resourceObj->state;
            $resourceObj->start_date = $requestData['start_date'] ?? $resourceObj->start_date;
            $resourceObj->end_date = $requestData['end_date'] ?? NULL;
            $resourceObj->is_currently_here = $requestData['is_currently_here'] ?? '0';
            $resourceObj->save();

            $response['data'] = $resourceObj;
            $response['message'] = 'Work experience updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getWorkExperience(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $response['data'] = UserExperience::where('user_id', $request->user()->id)->orderBy('start_date', 'DESC')->get();
            $response['message'] = 'Work experience fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteWorkExperience(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'resource_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            UserExperience::where('id', $requestData['resource_id'])->delete();

            $response['message'] = 'Work experience deleted successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addEducation(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'institute' => 'required',
                'qualification' => 'required',
                'city' => 'required',
                'state' => 'required',
                'start_date' => 'required',
                //'end_date' => 'required',
            ];

            if (isset($requestData['is_currently_here']) && !empty($requestData['is_currently_here']) && $requestData['is_currently_here'] != '1') {
                $rules['end_date'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $userId = $request->user()->id;
            $resourceObj = new UserEducation;
            $resourceObj->user_id = $userId;
            $resourceObj->institute = $requestData['institute'] ?? NULL;
            $resourceObj->qualification = $requestData['qualification'] ?? NULL;
            $resourceObj->city = $requestData['city'] ?? NULL;
            $resourceObj->state = $requestData['state'] ?? NULL;
            $resourceObj->start_date = $requestData['start_date'] ?? NULL;
            $resourceObj->end_date = $requestData['end_date'] ?? NULL;
            $resourceObj->is_currently_here = $requestData['is_currently_here'] ?? '0';
            $resourceObj->save();

            $response['data'] = $resourceObj;
            $response['message'] = 'Education added successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateEducation(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'resource_id' => 'required',
            ];

            if (isset($requestData['is_currently_here']) && !empty($requestData['is_currently_here']) && $requestData['is_currently_here'] != '1') {
                $rules['end_date'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $userId = $request->user()->id;
            $resourceObj = UserEducation::find($requestData['resource_id']);
            $resourceObj->user_id = $userId;
            $resourceObj->institute = $requestData['institute'] ?? $resourceObj->institute;
            $resourceObj->qualification = $requestData['qualification'] ?? $resourceObj->qualification;
            $resourceObj->city = $requestData['city'] ?? $resourceObj->city;
            $resourceObj->state = $requestData['state'] ?? $resourceObj->state;
            $resourceObj->start_date = $requestData['start_date'] ?? $resourceObj->start_date;
            $resourceObj->end_date = $requestData['end_date'] ?? NULL;
            $resourceObj->is_currently_here = $requestData['is_currently_here'] ?? '0';
            $resourceObj->save();

            $response['data'] = $resourceObj;
            $response['message'] = 'Education updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getEducation(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $response['data'] = UserEducation::where('user_id', $request->user()->id)->orderBy('start_date', 'DESC')->get();
            $response['message'] = 'Education fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteEducation(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'resource_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            UserEducation::where('id', $requestData['resource_id'])->delete();

            $response['message'] = 'Education deleted successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function searchSkill(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $skillObj = Skill::where('title', 'like', '%' . $request->get('search_term') ?? '' . '%');

            $response['data'] = $skillObj->get()->each->append('is_added');

            $response['message'] = 'Skill fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function getSkill(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $skillObj = UserSkill::where('user_id', $request->user()->id)->with('skill_detail')->get();

            $response['data'] = $skillObj;

            $response['message'] = 'Skill fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addSkill(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'skill_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            $userId = $request->user()->id;

            $resourceIds = [];
            if (!is_array($requestData['skill_id'])) {
                $requestData['skill_id'] = json_decode($requestData['skill_id']);
            }

            foreach ($requestData['skill_id'] as $skillId) {
                $resourceObj = UserSkill::where(['user_id' => $userId, 'skill_id' => $skillId])->first();
                if (!$resourceObj) {
                    $resourceObj = new UserSkill;
                }
                $resourceObj->user_id = $userId;
                $resourceObj->skill_id = $skillId;
                $resourceObj->save();

                array_push($resourceIds, $resourceObj->id);
            }

            $response['data'] = UserSkill::whereIn('id', $resourceIds)->with('skill_detail')->get();
            $response['message'] = 'Skill added successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteSkill(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $rules = [
                'resource_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return $errorResponse;
            }

            UserSkill::where('id', $requestData['resource_id'])->delete();

            $response['message'] = 'Skill deleted successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function notifications(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;
            $response['data'] = Notification::where('user_id', $userId)->get();
            $response['message'] = 'Notification fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function addnotifications(Request $request)
    {

        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();

            /*$rules = [
                'name' => 'required',
                'email' => 'required',
            ];*/
            $rules = [];
            $userId = $request->user()->id;

            if (isset($requestData['email']) && !empty($requestData['email'])) {
                $rules['email'] = [
                    'email' => Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            if (isset($requestData['mobile']) && !empty($requestData['mobile'])) {
                $rules['mobile'] = [
                    Rule::unique('users', 'mobile')->ignore($userId)->whereNull('deleted_at')
                ];
            }



            $requestData = $request->all();
            $userId = $request->user()->id;
            $userObj = User::find($userId);
            if (Notification::where('user_id', $userObj->id)->delete()) {
                $Notifications = new Notification();
                $Notifications->user_id = $requestData['user_id'] ??   $userObj->id;
                $Notifications->message = $requestData['message'] ?? NULL;
                $Notifications->data = $requestData['message'] ?? NULL;
                $Notifications->type = $requestData['user_type'] ?? NULL;

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $errorResponse = validation_error_response($validator->errors()->toArray());
                    return response()->json($errorResponse, STATUS_BAD_REQUEST);
                }

                $Notifications->save();
            }


            $response['data'] = $Notifications;
            $response['message'] = 'Your Service Add successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function deleteAccount(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $userId = $request->user()->id;
            //$userId = 4;

            User::where('id', $userId)->delete();
            AppliedJob::where('user_id', $userId)->delete();

            $response['message'] = 'Account deleted successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function downloadResume(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();

            $userId = $request->user()->id;

            $userObj = User::where('id', $userId)->with('skills', 'skills.skill_detail:id,title')
                ->with('work_experiences')
                ->with('educations')->first();

            $path = public_path() . '/resume';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

            $html = view('resume')->with(compact('userObj'))->render();

            $pdf = Pdf::loadHtml($html);
            /** @var Response $response */

            $fileName = $userId . ".pdf";

            $result = $pdf->save(public_path('resume/' . $fileName));

            $response['message'] = 'Resume fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function updateResumeFile($userId = "")
    {
        $userObj = User::where('id', $userId)->with('skills', 'skills.skill_detail:id,title')
            ->with('work_experiences')
            ->with('educations')->first();

        $path = public_path() . '/resume';
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

        $html = view('resume')->with(compact('userObj'))->render();

        $pdf = Pdf::loadHtml($html);
        /** @var Response $response */

        $fileName = $userId . ".pdf";

        $result = $pdf->save(public_path('resume/' . $fileName));

        return $fileName;
    }

    public function updateSubscription(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        $rules = [];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errorResponse = validation_error_response($validator->errors()->toArray());
            return response()->json($errorResponse, STATUS_BAD_REQUEST);
        }

        $requestData = $request->all();

        try {
            $subscriptionObj = Subscription::where('user_id', $request->user()->id)->first();

            if (!$subscriptionObj) {
                $subscriptionObj = new Subscription;
                $subscriptionObj->user_id = $request->user()->id;
            }

            $subscriptionObj->subscription_date = $requestData['subscription_date'] ?? "";
            $subscriptionObj->subscription_days = $requestData['subscription_days'] ?? "";
            $subscriptionObj->grace_period = $requestData['grace_period'] ?? "";
            $subscriptionObj->order_id = $requestData['order_id'] ?? "";
            $subscriptionObj->purchase_token = $requestData['purchase_token'] ?? "";
            $subscriptionObj->save();


            $response['message'] = "Subscription updated successfully";
            $response['data'] = $subscriptionObj;
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            unset($response['data']);
            unset($response['access_token']);

            $response['message'] = $e->getMessage();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status'] ?? STATUS_BAD_REQUEST);
    }

    public function getServicesList(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $userId = $request->user()->id;

            $response['data'] = Category::get();
            $response['message'] = 'Category fetched successfully';
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
}
