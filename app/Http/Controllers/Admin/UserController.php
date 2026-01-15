<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppliedJob;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Users';

        $userObj = User::latest();

        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;

            $userObj->whereRaw("(name ILIKE '%" . $q . "%' OR first_name ILIKE '%" . $q . "%' OR last_name ILIKE '%" . $q . "%' OR email ILIKE '%" . $q . "%' OR mobile ILIKE '%" . $q . "%' OR country_code ILIKE '%" . $q . "%' OR city ILIKE '%" . $q . "%')");

            $result = $userObj->paginate(10)->appends(['q' => $q]);
        } else {
            $result = $userObj->where('user_type', 'user')->paginate(10);
        }

        $data['page'] = $request->get('page') ?? 1;

        // $result = $userObj->paginate(10);

        return view('admin.user.index')->with(compact('data', 'result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data['page_title'] = 'Add User';
        return view('admin.user.create')->with(compact('data'));
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
            'title' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            //$resourceObj = new User;

            return redirect()->route('user.index')->with('success', 'User added successfully');
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
    public function show(Request $request, $id)
    {
        $data = User::where('id', $id)->first();
        $data['page_title'] = 'User Detail';

        $userId = $id;
        $appliedJobIds = AppliedJob::where('user_id', $userId)->get()->pluck('job_id');
        $result = Job::whereIn('id', $appliedJobIds)
            ->with(['applied_status' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->latest()->limit(5)->get();

        $data['page'] = $request->get('page') ?? 1;

        return view('admin.user.detail')->with(compact('data', 'result'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::find($id);
        $data['page_title'] = 'Edit User';

        return view('admin.user.edit')->with(compact('data'));
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
            'title' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $resourceObj = User::find($requestData['update_id']);
            $resourceObj->name = $requestData['name'];

            $resourceObj->save();

            return redirect()->route('user.index')->with('success', 'User updated successfully');
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
    public function destroy($id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id = NULL)
    {
        if (!$id) {
            return redirect()->route('user.index')->with('error', 'Invalid category id');
        }

        $resourceObj = User::find($id);

        if ($resourceObj->delete()) {
            return redirect()->route('user.index')->with('success', 'User deleted successfully');
        }
        return redirect()->route('user.index')->with('error', DEFAULT_ERROR_MESSAGE);
    }

    public function updateStatus(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();

            $rules = [
                'resource_id' => 'required',
                'status' => 'required',
            ];
            $rules = [];

            if (isset($requestData['email']) && !empty($requestData['email'])) {
                //$rules['email'] = ['required', Rule::unique('users')->ignore($request->get('email'))->whereNull('deleted_at')];
                $rules['email'] = [
                    'email' => Rule::unique('users', 'email')->ignore($requestData['resource_id'])->whereNull('deleted_at')
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $userObj = User::find($requestData['resource_id']);
            $userObj->status = $requestData['status'];

            $userObj->save();

            $response['data'] = $userObj;
            $response['message'] = 'Status updated successfully';
            $response['success'] = TRUE;
            $response['status'] = STATUS_OK;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());
            $response['status'] = STATUS_GENERAL_ERROR;
        }

        return response()->json($response, $response['status']);
    }

    public function jobs(Request $request, $userId = NULL)
    {
        $data = [];
        $data['page_title'] = 'Jobs';

        $appliedJobIds = AppliedJob::where('user_id', $userId)->get()->pluck('job_id');

        $jobObj = Job::whereIn('id', $appliedJobIds)
            ->with(['applied_status' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->latest();

        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;

            $jobObj->whereRaw("(position ILIKE '%" . $q . "%' OR title ILIKE '%" . $q . "%' OR description ILIKE '%" . $q . "%')");

            $result = $jobObj->paginate(10)->appends(['q' => $q]);
        } else {
            $result = $jobObj->paginate(10);
        }

        $data['user_detail'] = User::find($userId);

        $data['page'] = $request->get('page') ?? 1;

        return view('admin.user.jobs')->with(compact('data', 'result'));
    }

    public function jobDetail(Request $request, $jobId = NULL)
    {
        $data = [];
        $data['page_title'] = 'Job Detail';

        $userId = $request->get('q');

        $result = Job::where('id', $jobId)
            ->with(['applied_status' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->with('applied', 'applied.user_detail:id,first_name,last_name,email,image')
            ->first();

        $shortListedIds = AppliedJob::where('job_id', $jobId)->where('status', '2')->get()->pluck('user_id');
        $data['shortlisted_user'] = User::whereIn('id', $shortListedIds)->get();

        $hiredIds = AppliedJob::where('job_id', $jobId)->where('status', '3')->get()->pluck('user_id');
        $data['hired_user'] = User::whereIn('id', $hiredIds)->get();

        $data['q'] = $request->get('q');
        $data['p'] = $request->get('p');

        return view('admin.user.job-detail')->with(compact('data', 'result'));
    }

    public function changeStatus(Request $request)
    {
        $response = [];
        $response['success'] = FALSE;
        $response['status'] = STATUS_BAD_REQUEST;

        try {
            $requestData = $request->all();
            if ($request->resource_id) {
                $userId = $request->resource_id;
                $userObj = User::find($requestData['resource_id']);
                $userObj->is_verified = $userObj->is_verified == true ? false : true;
                $userObj->save();
                $response['data'] = $userObj;
                $response['message'] = 'Status updated successfully';
            }
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
