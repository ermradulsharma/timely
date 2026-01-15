<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterviewSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;

class InterviewSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Interview Slot';
        $data['qid'] = $request->get('qid');
        $result = InterviewSlot::select('id', 'user_id', 'slot_date AS start', 'start_time', 'end_time', 'booking_time')
        ->with('user_detail:id,name,first_name,last_name,email')
        ->orderBy('slot_date', 'ASC')->orderBy('start_time', 'ASC')->get();

        /* foreach($result as $k => $val) {
            $index = $k + 1;
            $result[$k]->extendedProps = [
                'order' => (string)$index
            ];
        } */
        //return $result;
        /* $result = InterviewSlot::selectRaw("slot_date AS start, CONCAT(TIME_FORMAT(start_time, '%h:%i %p'), ' - ', TIME_FORMAT(end_time, '%h:%i %p')) AS title, CONCAT(TIME_FORMAT(start_time, '%h:%i %p'), ' - ', TIME_FORMAT(end_time, '%h:%i %p')) AS description")
            ->latest()->get(); */

        return view('admin.interview-slot.index')->with(compact('data', 'result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Add Interview Slot';
        $data['qid'] = $request->get('qid');

        return view('admin.interview-slot.create')->with(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'slot_date' => 'required',
            'start_time' => 'required',
            //'end_time' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $startTime = date("H:i:s", strtotime($requestData['start_time']));
            $endTime = date("H:i:s", strtotime($requestData['end_time'] ?? $requestData['end_time_hidden']));

            $availability = InterviewSlot::where('slot_date', $requestData['slot_date'])->where(function ($query) use ($startTime, $endTime) {
                $query
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query
                            ->where('start_time', '<=', $startTime)
                            ->where('end_time', '>', $startTime);
                    })
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query
                            ->where('start_time', '<', $endTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })->count();

            if ($availability > 0) {
                return redirect()->route('interview-slot.index')->with('error', 'Slot not available');
            }

            $resourceObj = new InterviewSlot;
            $resourceObj->slot_date = $requestData['slot_date'];
            $resourceObj->start_time = date("H:i:s", strtotime($requestData['start_time']));
            $resourceObj->end_time = date("H:i:s", strtotime($requestData['end_time'] ?? $requestData['end_time_hidden']));

            $resourceObj->save();

            return redirect()->route('interview-slot.index')->with('success', 'Interview slot added successfully');
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error($e->getTraceAsString());

            return redirect()->back()->withInput()->with('error', $message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = InterviewSlot::find($id);
        $data['page_title'] = 'Interview Slot Detail';

        return view('admin.interview-slot.detail')->with(compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = InterviewSlot::find($id);
        $data['page_title'] = 'Edit Available Slot';
        // $data['id'] = $request->get('id');
        // dd($data);

        return view('admin.interview-slot.edit')->with(compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'slot_date' => 'required',
            'start_time' => 'required',
            //'end_time' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $startTime = date("H:i:s", strtotime($requestData['start_time']));
            $endTime = date("H:i:s", strtotime($requestData['end_time'] ?? $requestData['end_time_hidden']));

            $availability = InterviewSlot::where('id', '!=', $requestData['update_id'])->where('slot_date', $requestData['slot_date'])->where(function ($query) use ($startTime, $endTime) {
                $query
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query
                            ->where('start_time', '<=', $startTime)
                            ->where('end_time', '>', $startTime);
                    })
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query
                            ->where('start_time', '<', $endTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })->count();

            if ($availability > 0) {
                return redirect()->route('interview-slot.available')->with('error', 'Slot not available');
            }

            $resourceObj = InterviewSlot::find($requestData['update_id']);
            $resourceObj->slot_date = $requestData['slot_date'];
            $resourceObj->start_time = date("H:i:s", strtotime($requestData['start_time']));
            $resourceObj->end_time = date("H:i:s", strtotime($requestData['end_time'] ?? $requestData['end_time_hidden']));

            $resourceObj->save();

            return redirect()->route('interview-slot.available')->with('success', 'Interview slot updated successfully');
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::error($e->getTraceAsString());

            return redirect()->back()->withInput()->with('error', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id = NULL)
    {
        if (!$id) {
            return redirect()->route('interview-slot.index')->with('error', 'Invalid question id');
        }

        $resourceObj = InterviewSlot::find($id);

        if ($resourceObj->delete()) {
            return redirect()->route('interview-slot.index', ['qid' => $request->get('qid')])->with('success', 'Interview slot deleted successfully');
        }
        return redirect()->route('interview-slot.index', ['qid' => $request->get('qid')])->with('error', DEFAULT_ERROR_MESSAGE);
    }

    public function bookedSlot(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Booked Slot';
        $result = InterviewSlot::where('user_id', '!=', NULL)->with('user_detail')->latest()->paginate(10);

        return view('admin.interview-slot.booked-slot')->with(compact('data', 'result'));
    }

    public function availableSlot(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Available Slot';
        $result = InterviewSlot::where('user_id', NULL)->with('user_detail')->latest()->paginate(10);

        return view('admin.interview-slot.available-slot')->with(compact('data', 'result'));
    }
}
