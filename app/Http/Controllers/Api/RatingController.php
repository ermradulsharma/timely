<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $userId = $request->user()->id;
            $validation = Validator::make($request->all(), [
                //     // 'provider_id' => 'required',
                //     // 'service_id' => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors()->first();
                $data['message'] = $errors;
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, $data['status']);
            }
            if ($request->user()->user_type == "provider") {
                $rating = Rating::where('rating_send_to', $userId)->get();
            }

            if ($request->user()->user_type == "user") {
                $rating = Rating::where('rating_send_to', $userId)->get();
            }

            $response['data'] = $rating;
            $response['message'] = 'Rating fetch Succcefully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }
        return response()->json($response, $response['status']);
    }

    public function save(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            $validation = Validator::make($request->all(), []);

            if ($validation->fails()) {
                $errors = $validation->errors()->first();
                $data['message'] = $errors;
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, $data['status']);
            }

            $requestData = $request->all();

            $ratingObj = new Rating;
            $ratingObj->rating_send_to = $requestData['rating_send_to'];
            $ratingObj->rating_send_by = $request->user()->id;
            $ratingObj->rating = (int)$requestData['rating'] ?? null;
            $ratingObj->review = $requestData['review'] ?? null;
            $ratingObj->save();

            $response['data'] = $ratingObj;
            $response['message'] = 'Review given successfully. ';
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
