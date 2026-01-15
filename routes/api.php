<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServicesController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api', 'middleware' => ['\App\Http\Middleware\LogAfterRequest::class']], function () {
    Route::get('/app-basic-details', [HomeController::class, 'applicationBasicDetails']);
    Route::get('/terms-conditions', [HomeController::class, 'termsConditions']);

    Route::post('/signup', 'UserController@signup');
    Route::post('/login', 'UserController@login')->middleware(\Spatie\HttpLogger\Middlewares\HttpLogger::class);
    Route::post('/forgot-password', 'UserController@forgotPassword');
    Route::post('/social-login', 'UserController@socialLogin');
    Route::post('/send-otp', 'UserController@sendOtp');
    Route::post('/verify-otp', 'UserController@verifyOtp');

    Route::post('/update-guardian-profile', 'UserController@updateGuardianProfile');
    Route::get('/assistance-profile', 'UserController@getassistanceProfile');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/addproviderprofile', 'UserController@Add_profile_provider');
        Route::post('/v2/addproviderprofile', 'UserController@Add_profile_provider_v2');
        Route::post('/update-profile-provider', 'UserController@updateProfileprovider');
        Route::post('/get-profile-list', 'UserController@get_profile_list');
        Route::post('/v2/get-profile-list', 'UserController@get_profile_list_v2');
        Route::post('/get-all-provider', 'UserController@get_profile_list_new');
        Route::get('/profile-detail', 'UserController@profileDetail');
        Route::post('/change-password', 'UserController@changePassword');
        Route::post('/update-profile-pic', 'UserController@updateProfilePic');
        Route::post('/update-profile', 'UserController@updateProfile');
        Route::post('/update-device-token', 'UserController@updateDeviceToken');
        Route::post('/update-lat-lng', 'UserController@updateLatLng');
        Route::post('/logout', 'UserController@logout');
        Route::post('/delete-account', 'UserController@deleteAccount');

        // Add Service By Provider
        Route::get('/get-service', [ServicesController::class, 'index']);
        Route::post('/add-service', [ServicesController::class, 'create']);
        Route::post('/delete-service', [ServicesController::class, 'delete']);
        Route::post('/update-service', [ServicesController::class, 'update']);

        Route::get('/services-list', [UserController::class, 'getServicesList']);
        Route::post('/get-category', [ServicesController::class, 'getCategory']);
        
        // Category Price list
        Route::get('/v2/services-list', [UserController::class, 'getServicesList_v2']);

        //Services Request
        Route::post('/send-service-request', 'ServicesrequestController@create');
        Route::post('/get-request-service-provider', 'ServicesrequestController@getRequestServiceProvider');
        Route::post('/customer-check-request', 'ServicesrequestController@customerCheckRequest');
        Route::post('/change-request-status', 'ServicesrequestController@changeRequestStatus');

        //payment Request
        Route::post('/send-payment-request', 'ServicesrequestController@sendPaymentRequest');
        Route::post('/customer-check-payment-request', 'ServicesrequestController@customerCheckPaymentRequest');
        Route::post('/reject-payment-request', 'ServicesrequestController@rejectPaymentRequest');
        Route::post('/make-payment', 'ServicesrequestController@makePayment');

        // JOB
        Route::get('/get-job-experience', 'JobController@getJobExperience');
        Route::get('/get-job', 'JobController@get');
        Route::get('/job-detail', 'JobController@detail');
        Route::post('/create-job', 'JobController@create');
        Route::post('/update-job', 'JobController@update');
        Route::post('/delete-job', 'JobController@delete');
        Route::get('/search-job', 'JobController@search');
        Route::post('/apply-job', 'JobController@apply');
        Route::get('/get-applied-job', 'JobController@getAppliedJob');
        Route::post('/hire', 'JobController@hire');
        Route::post('/add-to-favourite', 'JobController@addToFavourite');
        Route::get('/get-favourite', 'JobController@getFavourite');
        Route::post('/delete-favourite', 'JobController@deleteFavourite');
        Route::get('/get-interview-video', 'JobController@getInterviewVideo');
        Route::post('/update-applied-job-status', 'JobController@updateAppliedJobStatus');
        Route::post('/update-job-status', 'JobController@updateStatus');
        Route::get('/interview-slot', 'JobController@interviewSlot');
        Route::post('/book-interview-slot', 'JobController@bookInterviewSlot');
        Route::get('/contract-employment-types', 'JobController@contractEmploymentTypes');

        // WORK EXPERIENCE
        Route::post('/add-work-experience', 'UserController@addWorkExperience');
        Route::post('/update-work-experience', 'UserController@updateWorkExperience');
        Route::get('/get-work-experience', 'UserController@getWorkExperience');
        Route::post('/delete-work-experience', 'UserController@deleteWorkExperience');

        // EDUCATION
        Route::post('/add-education', 'UserController@addEducation');
        Route::post('/update-education', 'UserController@updateEducation');
        Route::get('/get-education', 'UserController@getEducation');
        Route::post('/delete-education', 'UserController@deleteEducation');

        // SKILL
        Route::get('/search-skill', 'UserController@searchSkill');
        Route::get('/get-skill', 'UserController@getSkill');
        Route::post('/add-skill', 'UserController@addSkill');
        Route::post('/delete-skill', 'UserController@deleteSkill');

        // NOTIFICATION
        Route::get('/get-notification', 'UserController@notifications');
        Route::post('/add-notification', 'UserController@addnotifications');

        //Booking
        Route::post('booking', [BookingController::class, 'bookingService']);
        Route::get('get-booking-list', [BookingController::class, 'getBookingList']);
        Route::get('getBookingDetails', [BookingController::class, 'getBookingDetails']);
        Route::post('booking-accept', [BookingController::class, 'bookingAccept']);

        // CARD ROUTES
        Route::post('/save-card', [CardController::class, 'save']);
        Route::get('/get-saved-cards', [CardController::class, 'get']);
        Route::post('/delete-card', [CardController::class, 'delete']);
        Route::post('/validate-card', [CardController::class, 'validateCard']);
        Route::post('/update-subscription', [CardController::class, 'updateSubscription']);
        Route::get('/download-resume', [CardController::class, 'downloadResume']);
        Route::post('/add-bank-account', [CardController::class, 'saveBank']);
        Route::get('/get-bank-account', [CardController::class, 'getBank']);
        Route::post('/delete-bank-account', [CardController::class, 'deleteBank']);
        Route::post('/transfer', [CardController::class, 'transfer']);
        Route::post('/update-stripe-account', [CardController::class, 'updateStripeAccount']);
        Route::get('/get-stripe-customer', [CardController::class, 'getStripeCustomer']);
        Route::get('/get-state', [CardController::class, 'getState']);

        // Rating
        Route::post('save-rating', [RatingController::class, 'save']);
        Route::get('get-rating', [RatingController::class, 'index']);
    });
});
