<?php

namespace App\Http\Controllers;

use App\Models\RequestResponseLog;
use Illuminate\Http\Request;

class RequestResponseLogController extends Controller
{
    public function index(Request $request)
    {
        $data = [];

        $requestData = $request->all();

        $testLogObj = RequestResponseLog::latest();

        if (isset($requestData['type']) && !empty($requestData['type'])) {
            $testLogObj->where('type', $requestData['type']);
        }
        if (isset($requestData['action']) && !empty($requestData['action'])) {
            $testLogObj->where('action', $requestData['action']);
        }


        if (isset($requestData['log_date']) && !empty($requestData['log_date'])) {
            $testLogObj->where('log_date', $requestData['log_date']);
            $selectedDate = date("d F, Y", strtotime($requestData['log_date']));
        } else {
            $testLogObj->where('log_date', date("Y-m-d"));
            $selectedDate = date("d F, Y");
        }

        $data['data'] = $testLogObj->paginate(10);
        $data['type'] = ['' => 'Please select type'] + ['error' => 'Error', 'info' => 'Info'];
        $data['action'] = ['' => 'Please select action'] + RequestResponseLog::groupBy('action')->pluck('action', 'action')->toArray();

        $data['selected_type'] = $requestData['type'] ?? '';
        $data['selected_action'] = $requestData['action'] ?? '';
        $data['selected_date'] = $selectedDate;

        return view('request-response-logs')->with(compact('data'));
    }
}
