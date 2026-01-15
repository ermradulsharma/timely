<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;

        try {
            $requestData = $request->all();
            $serviceObj = Services::where(['user_id'=> $request->user()->id,'deleted_at' => null, 'category_id'=>  $requestData['category_id']])->orderBy('id', 'ASC')->get();
            $response['data'] = $serviceObj;
            $response['message'] = 'Service fetched successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            DB::beginTransaction();

            $rules = [
                'service_name' => 'required',
                'price' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;

            $resourceObj = new Services;
            $resourceObj->user_id = $userId;
            $resourceObj->category_id = $requestData['category_id'] ?? "";
            $resourceObj->name = $requestData['service_name'] ?? "";
            $resourceObj->price = $requestData['price'] ?? "";
            $resourceObj->is_active = $requestData['is_active'] ?? false;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(SERVICES_IMAGE_PATH, $fileName);
                $resourceObj->image = $fileName;
            }

            if ($resourceObj->save()) {
                DB::commit();

                $response['data'] = $resourceObj;
                $response['message'] = 'Service add successfully';
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
            'service_id' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;
            $resourceObj = Services::where(['id' => $requestData['service_id'], 'user_id' => $userId])->first();
            $resourceObj->name = $requestData['service_name'] ?? $resourceObj->name;
            $resourceObj->category_id = $requestData['category_id'] ?? $resourceObj->category_id;
            $resourceObj->price = $requestData['price'] ?? $resourceObj->price;
            $resourceObj->is_active = $requestData['is_active'] ?? $resourceObj->is_active;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(SERVICES_IMAGE_PATH, $fileName);
                $resourceObj->image = $fileName;
            }

            $resourceObj->save();
            DB::commit();

            $response['data'] = $resourceObj;
            $response['message'] = 'Service update successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (\Exception $e) {
            $message = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());

            return redirect()->back()->withInput()->with('error', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $rules = [
                'service_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;

            $serviceObj = Services::where(['user_id' => $userId, 'id' => $requestData['service_id']])->first();
            if (!$serviceObj) {
                $data['message'] = "Service Does Not Found";
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, 200);
            }
            $serviceObj->deleted_at = Carbon::now();
            $serviceObj->save();

            $response['message'] = 'Service deleted successfully';
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

    public function getCategory(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $rules = [
                'service_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return response()->json($errorResponse, $response['status']);
            }

            $requestData = $request->all();
            $userId = $request->user()->id;

            $serviceObj = Services::where(['user_id' => $userId, 'id' => $requestData['service_id']])->first();
            if (!$serviceObj) {
                $data['message'] = "Service Does Not Found";
                $data['status'] = STATUS_BAD_REQUEST;
                return response()->json($data, 200);
            }
            $serviceObj->deleted_at = Carbon::now();
            $serviceObj->save();

            $response['message'] = 'Service deleted successfully';
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
