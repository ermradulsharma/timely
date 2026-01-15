<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\Booking;
use App\Models\ForgotPasswordMail;
use App\Models\InterviewSlot;
use App\Models\InterviewVideo;
use App\Models\Job;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
        //$this->middleware('guest:restaurant_web')->except('logout');
    }

    public function login(Request $request)
    {
        $data = [];

        if ($request->isMethod('post')) {
            $checkUser = User::webLogin($request->all());

            if (!isset($checkUser['user'])) {
                $message = $checkUser['message'];

                return redirect()->route('login')->with('error', $message);
            }

            if ($checkUser['user']['user_type'] != 'admin') {
                return redirect()->route('login')->with('error', "Invalid credentials");
            }

            return redirect()->route('dashboard');
        }

        return view('admin.login')->with(compact('data'));
    }

    public function dashboard(Request $request, $edit_id = null)
    {
        $data['action_url'] = '';
        $data['page_title'] = "Dashboard";

        $data['user_count'] = User::where('user_type', 'user')->count();
        $data['provider_count'] = User::where('user_type', 'provider')->count();
        $data['booking_count'] = Booking::count();
        $data['payment_count'] = Payment::count();

        // Helper function to fetch monthly data
        $fetchMonthlyData = function ($model, $typeColumn = null, $typeValue = null) {
            $query = $model::select(
                DB::raw('COUNT(id) as total'),
                DB::raw("DATE_FORMAT(created_at, '%b') as month")
            );

            if ($typeColumn && $typeValue) {
                $query->where($typeColumn, $typeValue);
            }

            $records = $query->groupBy('month')->get()->toArray();

            $monthlyData = [];
            foreach (MONTH_ARR as $month) {
                $key = array_search($month, array_column($records, 'month'));
                if ($key !== false) {
                    $monthlyData[] = [
                        'month' => $records[$key]['month'],
                        'total' => $records[$key]['total'],
                    ];
                } else {
                    $monthlyData[] = [
                        'month' => $month,
                        'total' => 0,
                    ];
                }
            }
            return $monthlyData;
        };

        $monthlyJobData = $fetchMonthlyData(Booking::class);
        $monthlyUserData = $fetchMonthlyData(User::class, 'user_type', 'user');
        $monthlyEmployerData = $fetchMonthlyData(User::class, 'user_type', 'provider');
        $monthlyPaymentData = $fetchMonthlyData(Payment::class);

        return view('admin.dashboard')->with(compact(
            'data',
            'monthlyJobData',
            'monthlyUserData',
            'monthlyEmployerData',
            'monthlyPaymentData'
        ));
    }


    public function changePasswordAdmin(Request $request)
    {
        $rules = [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return redirect()->back()->with('error', $errorResponse['message']);
            }

            $user = Auth::user();

            if (Hash::check($request->old_password, $user->password)) {
                $user->fill([
                    'password' => Hash::make($request->password),
                ])->save();

                $request->session()->flash('success', 'Password changed successfully');
                return redirect()->back();
            } else {
                $request->session()->flash('error', 'Wrong old password');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function forgotPassword()
    {
        $data = [];
        //$data['email'] = SUPER_ADMIN_EMAIL;

        return view('admin.forgot-password')->with(compact('data'));
    }

    public function sendForgotPasswordMail(Request $request)
    {
        $userObj = User::where(['email' => $request->get('email'), 'user_type' => 'admin'])->first();
        if (!$userObj) {
            return redirect()->route('forgot.password')->with('error', 'Please enter valid admin email.');
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
        if ($userObj) {
            $mailData['name'] = $userObj->name;
        }

        $mailData['link'] = route('password.reset', [$token, 'email' => $request->get('email')]);

        Mail::to($request->get('email'))->send(new ForgotPassword($mailData));

        return redirect()->route('/')->with('success', 'Please check your email to reset password');
    }

    public function getUsers(Request $request, $edit_id = null)
    {
        $data['action_url'] = '';
        $data['page_title'] = "Users";

        $userObj = User::where('is_admin', '=', '0')->orderBy('created_at', 'DESC');

        $data['data'] = $userObj->paginate(10);

        return view('admin.user.list')->with(compact('data'));
    }

    public function userDetail(Request $request, $id = null)
    {
        $data['action_url'] = '';
        $data['page_title'] = "Users";

        $userObj = User::where('id', $id)
            ->with('children')
            ->first()->toArray();

        $data['data'] = $userObj;

        return view('admin.user.detail')->with(compact('data'));
    }

    public function getUserById(Request $request)
    {
        $response = [];
        $response['success'] = false;

        try {
            $rules = [
                'resource_id' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $requestData = $request->all();

            $userObj = User::with('bank_detail')->find($requestData['resource_id']);
            if (!$userObj) {
                $response['message'] = 'Invalid user id';
                return response()->json($response, STATUS_BAD_REQUEST);
            }

            $response['data'] = $userObj;

            $response['message'] = 'User detail fetched successfully';
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        if ($response['success']) {
            return response()->json($response, STATUS_OK);
        }
        return response()->json($response, STATUS_BAD_REQUEST);
    }

    public function updateProfile(Request $request)
    {
        try {
            $requestData = $request->all();

            $userObj = User::find($request->user()->id);

            $userObj->name = $requestData['name'] ?? $userObj->name;
            $userObj->email = $requestData['email'] ?? $userObj->email;

            if ($request->hasFile('image')) {
                $rules['image'] = 'required|mimes:jpeg,jpg,png|max:5000';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $errorResponse = validation_error_response($validator->errors()->toArray());

                    return redirect()->back()->withInput()->with('error', $errorResponse['message']);
                }

                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(IMAGE_UPLOAD_PATH, $fileName);
                $userObj->image = $fileName;
            }

            $userObj->save();

            return redirect()->route('settings')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage())->withInput();
        }
    }

    public function updateUserStatus(Request $request)
    {
        $response = [];
        $response['success'] = false;

        try {
            $rules = [
                'resource_id' => 'required',
                'status' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $requestData = $request->all();

            $userObj = User::find($requestData['resource_id']);
            if (!$userObj) {
                $response['message'] = 'Invalid user id';
                return response()->json($response, STATUS_BAD_REQUEST);
            }

            $userObj->status = $requestData['status'];
            $userObj->save();

            $response['message'] = 'User status updated successfully';
            $response['success'] = true;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        if ($response['success']) {
            return response()->json($response, STATUS_OK);
        }
        return response()->json($response, STATUS_BAD_REQUEST);
    }

    public function deleteUser($resourceId = "")
    {
        if (empty($resourceId) || $resourceId == "1") {
            return redirect()->route('users')->with('error', 'Invalid user id');
        }
        if (User::where('id', $resourceId)->delete()) {
            return redirect()->route('advisors')->with('success', 'Advisor deleted successfully');
        }
        return redirect()->route('advisors')->with('error', DEFAULT_ERROR_MESSAGE);
    }

    public function checkEmailExist(Request $request)
    {
        $response = [];
        $response['success'] = true;

        $rules = [
            'email' => 'required|email|unique:users',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorResponse = validation_error_response($validator->errors()->toArray());
            $response['message'] = $errorResponse['message'];
            $response['success'] = false;
        }
        return $response;
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = [];
            $data['page_title'] = 'Settings';
            $settingObj = Setting::first();
            $settings = $settingObj['settings'] ?? [];

            /* config([
            'mail.mailers.smtp.port' => 1000,
            ]); */

            //echo config('mail.mailers.smtp.port');die;



            $smtpSetting = Setting::where('name', 'smtp')->first();
            $smtp = $smtpSetting->value ?? [];

            $stripeSetting = Setting::where('name', 'stripe')->first();
            $stripe = $stripeSetting->value ?? [];

            $stripeSetting = Setting::where('name', 'stripe')->first();
            $stripe = $stripeSetting->value ?? [];

            $appSetting = Setting::where('name', 'app')->first();
            $app = $appSetting->value ?? [];

            $push_notification_server_key_setting = Setting::where('name', 'push_notification_server_key')->first();
            $push_notification_server_key = $push_notification_server_key_setting->value ?? [];

            $debug_mode_setting = Setting::where('name', 'debug_mode')->first();
            $debug_mode = $debug_mode_setting->value ?? [];

            return view('admin.setting')->with(compact('data', 'settings', 'smtp', 'stripe', 'app', 'push_notification_server_key', 'debug_mode'));
        }

        try {
            $requestData = $request->all();

            $rules = [];
            $settingData = [];

            if ($requestData['request_type'] == 'change_password') {
                $rules['old_password'] = 'required|min:6';
                $rules['password'] = 'required|min:6|confirmed';

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    $errorResponse = validation_error_response($validator->errors()->toArray());
                    return redirect()->back()->with('error', $errorResponse['message']);
                }

                $user = Auth::user();

                if (Hash::check($request->old_password, $user->password)) {
                    $user->fill([
                        'password' => Hash::make($request->password),
                    ])->save();

                    $request->session()->flash('success', 'Password changed successfully');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Wrong old password');
                    return redirect()->back();
                }
            }

            if ($requestData['request_type'] == 'smtp') {
                $smtp = [
                    'email' => $requestData['smtp_email'],
                    'password' => $requestData['smtp_password'],
                    'host' => $requestData['smtp_host'] ?? "",
                    'port' => $requestData['smtp_port'] ?? "",
                    'from_address' => $requestData['smtp_from_address'],
                    'from_name' => $requestData['smtp_from_name'],
                ];

                $jsonData = json_encode($smtp);

                $settingObj = Setting::where('name', 'smtp')->first();

                if (!$settingObj) {
                    $settingObj = new Setting;
                    $settingObj->name = 'smtp';
                    $settingObj->description = 'SMTP setting is using to setup the mail configuration';
                }

                $settingObj->value = $jsonData;
                $settingObj->save();

                $request->session()->flash('success', 'SMTP setting updated successfully');
                return redirect()->back();
            }

            if ($requestData['request_type'] == 'push_notification_server_key') {
                $push_notification_server_key = [
                    'push_notification_server_key' => $requestData['push_notification_server_key'] ?? null,
                ];

                $jsonData = json_encode($push_notification_server_key);

                $settingObj = Setting::where('name', 'push_notification_server_key')->first();

                if (!$settingObj) {
                    $settingObj = new Setting;
                    $settingObj->name = 'push_notification_server_key';
                    $settingObj->description = 'Push notification server key';
                }

                $settingObj->value = $jsonData;
                $settingObj->save();

                $request->session()->flash('success', 'Push notification server key updated successfully');
                return redirect()->back();
            }

            if ($requestData['request_type'] == 'stripe') {
                $stripe = [
                    'public_key' => $requestData['public_key'],
                    'secret_key' => $requestData['secret_key'],
                ];

                $jsonData = json_encode($stripe);

                $settingObj = Setting::where('name', 'stripe')->first();

                if (!$settingObj) {
                    $settingObj = new Setting;
                    $settingObj->name = 'stripe';
                    $settingObj->description = 'Stripe setting is using to setup the payment gateway configuration';
                }

                $settingObj->value = $jsonData;
                $settingObj->save();

                $request->session()->flash('success', 'Stripe detail updated successfully');
                return redirect()->back();
            }

            if ($requestData['request_type'] == 'debug_mode') {
                $debug_mode = [
                    'debug_mode' => isset($requestData['debug_mode']) ? true : false,
                ];

                $jsonData = json_encode($debug_mode);

                $settingObj = Setting::where('name', 'debug_mode')->first();

                if (!$settingObj) {
                    $settingObj = new Setting;
                    $settingObj->name = 'debug_mode';
                    $settingObj->description = 'App debug mode on/off';
                }

                $settingObj->value = $jsonData;
                $settingObj->save();

                $request->session()->flash('success', 'Debug mode updated successfully');
                return redirect()->back();
            }

            if ($requestData['request_type'] == 'app') {
                $app = [];

                if (isset($requestData['app_name']) && !empty($requestData['app_name'])) {
                    $app['app_name'] = $requestData['app_name'];
                }
                if (isset($requestData['rate_on_apple_store']) && !empty($requestData['rate_on_apple_store'])) {
                    $app['rate_on_apple_store'] = $requestData['rate_on_apple_store'];
                }
                if (isset($requestData['rate_on_google_store']) && !empty($requestData['rate_on_google_store'])) {
                    $app['rate_on_google_store'] = $requestData['rate_on_google_store'];
                }
                if (isset($requestData['terms_conditions']) && !empty($requestData['terms_conditions'])) {
                    $app['terms_conditions'] = $requestData['terms_conditions'];
                }
                if (isset($requestData['privacy_policy']) && !empty($requestData['privacy_policy'])) {
                    $app['privacy_policy'] = $requestData['privacy_policy'];
                }

                if (isset($requestData['search_distance_limit']) && !empty($requestData['search_distance_limit'])) {
                    $app['search_distance_limit'] = $requestData['search_distance_limit'];
                }
                if (isset($requestData['instant_slot_notification']) && !empty($requestData['instant_slot_notification'])) {
                    $app['instant_slot_notification'] = $requestData['instant_slot_notification'];
                }

                $jsonData = json_encode($app);
                $settingObj = Setting::where('name', 'app')->first();
                if (!$settingObj) {
                    $settingObj = new Setting;
                    $settingObj->name = 'app';
                    $settingObj->description = 'APP setting is using to setup the Application Details';
                }

                $settingObj->value = $jsonData;
                $settingObj->save();

                $request->session()->flash('success', 'APP setting updated successfully');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function inAppPurchasePayment(Request $request)
    {
        $data = [];
        $data['page_title'] = 'In-app Purchase Payment';

        $resourceObj = InterviewSlot::whereNotNull('user_id')->with('user_detail')->latest();

        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;

            $resourceObj->join('users', 'users.id', '=', 'interview_slots.user_id')
                ->select('interview_slots.*', 'users.first_name', 'users.last_name')
                ->whereRaw("(first_name ILIKE '%" . $q . "%' OR last_name ILIKE '%" . $q . "%')")->latest('interview_slots.created_at');

            $result = $resourceObj->paginate(10)->appends(['q' => $q]);
        } else {
            $result = $resourceObj->paginate(10);
        }

        return view('admin.payment.in-app-purchase-payment')->with(compact('data', 'result'));
    }

    public function termsConditions(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = [];

            $termsConditions = Setting::where('name', 'terms_conditions')->first();
            $data = $termsConditions->value ?? [];
            $data['page_title'] = 'Terms & Conditions';

            return view('admin.terms_conditions.index')->with(compact('data'));
        }
        try {
            $requestData = $request->all();
            if (isset($requestData['terms_conditions']) && !empty($requestData['terms_conditions'])) {
                $termsConditions['terms_conditions'] = $requestData['terms_conditions'];
            }

            $jsonData = json_encode($termsConditions);

            $settingObj = Setting::where('name', 'terms_conditions')->first();

            if (!$settingObj) {
                $settingObj = new Setting;
                $settingObj->name = 'terms_conditions';
                $settingObj->description = 'Users terms & conditions';
            }

            $settingObj->value = $jsonData;
            $settingObj->save();

            $request->session()->flash('success', 'Terms Conditions updated successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
