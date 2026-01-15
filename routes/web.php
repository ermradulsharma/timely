<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
    //return view('welcome');

    $userId = $request->user()->id ?? NULL;

    if ($userId) {
        return redirect()->route('dashboard');
    } else {
        return view('admin.login');
    }
})->name('/');

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

Route::get('/password-reset/{token}', 'ForgotPasswordController@resetPassword')->name('password.reset');
Route::post('/password-reset-update', 'ForgotPasswordController@updatePassword')->name('password.reset.update');
Route::get('/congratulation', 'ForgotPasswordController@congratulation')->name('congratulation');



Route::group(['namespace' => 'Admin'], function () {
    Route::match(['GET', 'POST'], '/login', 'AdminController@login')->name('login');
    Route::get('/forgot-password', 'AdminController@forgotPassword')->name('forgot.password');
    Route::post('/send-forgot-password-mail', 'AdminController@sendForgotPasswordMail')->name('send.forgot.password.mail');

    Route::get('service-request/{id}', 'ServicesRequestController@servicesRequestDetail')->name('service-request.detail');
    Route::get('/service-request', 'ServicesRequestController@index')->name('service-request');

    Route::get('/booking-report', 'ServicesRequestController@serviceReport')->name('booking-report');

    Route::get('/payment', 'ServicesRequestController@payment')->name('payment');
    Route::get('/payment/{id}', 'ServicesRequestController@paymentDetail')->name('payment.details');

    Route::get('/service-gaurdian', 'ServicesProviderController@index')->name('service-gaurdian.index');
    Route::get('/service-gaurdian/{id}', 'ServicesProviderController@serviceProviderDetails')->name('service-gaurdian.details');

    Route::get('/notification', 'NotificationController@index')->name('notification.index');


    // Controllers Within The "App\Http\Controllers\Admin" Namespace
    Route::group(['middleware' => ['auth']], function () {
        Route::get('/dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::post('/update-profile-admin', 'AdminController@updateProfile')->name('update.profile.admin');
        Route::match(['GET', 'POST'], '/settings', 'AdminController@settings')->name('settings');
        Route::get('/admin-logout', 'AdminController@logout')->name('admin.logout');
        Route::post('change-status', [UserController::class, 'changeStatus'])->name('change-status');
        Route::post('/change-password-admin', 'AdminController@changePasswordAdmin')->name('change.password.admin');

        // USER
        Route::get('job-details/{id}', 'UserController@jobDetail')->name('user.job.detail');
        Route::get('applied-jobs/{id}', 'UserController@jobs')->name('user.jobs');
        Route::post('update.user.status', 'UserController@updateStatus')->name('update.user.status');
        Route::get('delete-user/{id}', 'UserController@delete')->name('user.delete');
        Route::resource('user', 'UserController');

        // EMPLOYER
        /* Route::post('update.user.status', 'UserController@updateStatus')->name('update.user.status'); */
        Route::get('job-detail/{id}', 'EmployerController@jobDetail')->name('employer.job.detail');
        Route::get('employer-jobs/{id}', 'EmployerController@jobs')->name('employer.jobs');
        Route::get('delete-employer/{id}', 'EmployerController@delete')->name('employer.delete');
        Route::resource('employer', 'EmployerController');

        // INTERVIEW VIDEOS
        Route::get('delete-interview-video/{id}', 'InterviewVideoController@delete')->name('interview-video.delete');
        Route::resource('interview-video', 'InterviewVideoController');

        // QUESTIONS
        Route::get('delete-interview-video-question/{id}', 'InterviewVideoQuestionController@delete')->name('interview-video-question.delete');
        Route::resource('interview-video-question', 'InterviewVideoQuestionController');

        // INTERVIEW SLOTS
        Route::get('delete-interview-slot/{id}', 'InterviewSlotController@delete')->name('interview-slot.delete');
        Route::get('booked-slot', 'InterviewSlotController@bookedSlot')->name('interview-slot.booked');
        Route::get('available-slot', 'InterviewSlotController@availableSlot')->name('interview-slot.available');
        Route::resource('interview-slot', 'InterviewSlotController');

        //Terms Conditions
        Route::match(['GET', 'POST'], 'terms-conditions', 'AdminController@termsConditions')->name('terms-conditions');

        // CONTRACT TYPE
        Route::get('delete-contract-type/{id}', 'ContractTypeController@delete')->name('contract-type.delete');
        Route::resource('contract-type', 'ContractTypeController');

        // EMPLOYMENT TYPE
        Route::get('delete-employment-type/{id}', 'EmploymentTypeController@delete')->name('employment-type.delete');
        Route::resource('employment-type', 'EmploymentTypeController');

        Route::get('/in-app-purchase-payment', 'AdminController@inAppPurchasePayment')->name('in.app.purchase.payment');

        // SERVICES
        Route::get('delete-category/{id}', 'ServicesController@delete')->name('categories.delete');
        Route::resource('categories', 'ServicesController');
    });

    Route::post('/check-email-exist', 'AdminController@checkEmailExist')->name('check.email.exist');
});

//Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
//Route::get('request-response-logs', 'RequestResponseLogController@index')->name('daily.logs');
