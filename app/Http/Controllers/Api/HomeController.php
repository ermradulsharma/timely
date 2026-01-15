<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class HomeController extends Controller
{
    public function applicationBasicDetails()
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $appSetting = Setting::where('name', 'app')->first();
            $requestData = $appSetting->value ?? [];
            $data['app_name'] = $requestData['app_name'] ?? "";
            $data['rate_on_apple_store'] = $requestData['rate_on_apple_store']  ?? "";
            $data['rate_on_google_store'] = $requestData['rate_on_google_store']  ?? "";
            $data['terms_conditions'] = $requestData['terms_conditions']  ?? "";
            $data['privacy_policy'] = $requestData['privacy_policy']  ?? "";
            $data['search_distance_limit'] = $requestData['search_distance_limit']  ?? "";
            $data['instant_slot_notification'] = $requestData['instant_slot_notification']  ?? "";

            $response['data'] = $data;
            $response['message'] = 'Application basic details fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        return response()->json($response, $response['status']);
    }

    public function termsConditions()
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
           $termsConditions = Setting::where('name', 'terms_conditions')->first();
            $data = $termsConditions->value ?? [];

            $response['data'] = $data;
            $response['message'] = 'Terms conditions fetched successfully';
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
