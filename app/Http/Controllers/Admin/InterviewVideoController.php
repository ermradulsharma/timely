<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppliedJob;
use App\Models\InterviewVideo;
use App\Models\InterviewVideoQuestion;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;

class InterviewVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $data['page_title'] = 'Interview Videos';
        $result = InterviewVideo::latest()->paginate(10);

        return view('admin.interview-video.index')->with(compact('data', 'result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data['page_title'] = 'Add Interview Video';

        return view('admin.interview-video.create')->with(compact('data'));
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
            'description' => 'required',
            //'file' => 'required|mimes:mp4,mov,3gp',
            'file' => 'required|mimes:mp4|max:102400',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $resourceObj = new InterviewVideo;
            $resourceObj->title = $requestData['title'];
            $resourceObj->description = $requestData['description'];

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                if (is_object($file)) {
                    if (isset($_FILES['file'])) {
                        $mime = $_FILES['file']['type'];
                    }
                    $fileType = "";
                    if (strstr($mime, "video/")) {
                        $fileType = "video";
                    } else if (strstr($mime, "image/")) {
                        $fileType = "image";
                    } else if (strstr($mime, "audio/")) {
                        $fileType = "audio";
                    }

                    $fileName = time() . '-' . $file->getClientOriginalName();
                    $fileName = str_replace(" ", "_", $fileName);
                    $extension = $file->getClientOriginalExtension();
                    $file->move(INTERVIEW_QUESTION_UPLOAD_PATH, $fileName);

                    $resourceObj->file = $fileName;
                    $resourceObj->extension = $extension;

                    if ($fileType == "video") {
                        if ($request->hasFile('thumbnail')) {
                            $file = $request->file('thumbnail');

                            $thumbnailName = time() . '-' . $file->getClientOriginalName();
                            $thumbnailName = str_replace(" ", "_", $thumbnailName);
                            $extension = $file->getClientOriginalExtension();
                            $file->move(INTERVIEW_QUESTION_UPLOAD_PATH, $thumbnailName);
                            $resourceObj->thumbnail = $thumbnailName;
                        }
                    }
                }
            }

            $resourceObj->save();

            return redirect()->route('interview-video.index')->with('success', 'Interview video added successfully');
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
        $data = InterviewVideo::find($id);
        $data['page_title'] = 'Interview Video Detail';

        return view('admin.interview-video.detail')->with(compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = InterviewVideo::find($id);
        $data['page_title'] = 'Edit Interview Video';

        return view('admin.interview-video.edit')->with(compact('data'));
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
            'description' => 'required',
        ];

        if ($request->hasFile('file')) {
            //$rules['file'] = 'required|mimes:mp4,mov,3gp';
            $rules['file'] = 'required|mimes:mp4|max:102400';
        }

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $resourceObj = InterviewVideo::find($id);
            $resourceObj->title = $requestData['title'] ?? $resourceObj->title;
            $resourceObj->description = $requestData['description'] ?? $resourceObj->description;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                if (is_object($file)) {
                    if (isset($_FILES['file'])) {
                        $mime = $_FILES['file']['type'];
                    }
                    $fileType = "";
                    if (strstr($mime, "video/")) {
                        $fileType = "video";
                    } else if (strstr($mime, "image/")) {
                        $fileType = "image";
                    } else if (strstr($mime, "audio/")) {
                        $fileType = "audio";
                    }

                    $fileName = time() . '-' . $file->getClientOriginalName();
                    $fileName = str_replace(" ", "_", $fileName);
                    $extension = $file->getClientOriginalExtension();
                    $file->move(INTERVIEW_QUESTION_UPLOAD_PATH, $fileName);

                    $resourceObj->file = $fileName;
                    $resourceObj->extension = $extension;

                    if ($fileType == "video") {
                        if ($request->hasFile('thumbnail')) {
                            $file = $request->file('thumbnail');

                            $thumbnailName = time() . '-' . $file->getClientOriginalName();
                            $thumbnailName = str_replace(" ", "_", $thumbnailName);
                            $extension = $file->getClientOriginalExtension();
                            $file->move(INTERVIEW_QUESTION_UPLOAD_PATH, $thumbnailName);
                            $resourceObj->thumbnail = $thumbnailName;
                        }
                    }
                }
            }

            $resourceObj->save();

            return redirect()->route('interview-video.index')->with('success', 'Interview video updated successfully');
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
            return redirect()->route('interview-videos.index')->with('error', 'Invalid category id');
        }

        $resourceObj = InterviewVideo::find($id);

        if ($resourceObj->delete()) {
            return redirect()->route('interview-video.index')->with('success', 'Interview video deleted successfully');
        }
        return redirect()->route('interview-video.index')->with('error', DEFAULT_ERROR_MESSAGE);
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
                    'email' => Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());
                return response()->json($errorResponse, STATUS_BAD_REQUEST);
            }

            $userObj = InterviewVideo::find($requestData['resource_id']);
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
}
