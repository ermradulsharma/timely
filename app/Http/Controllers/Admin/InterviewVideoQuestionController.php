<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterviewVideo;
use App\Models\InterviewVideoQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;

class InterviewVideoQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Interview Video Questions';
        $data['qid'] = $request->get('qid');
        $result = InterviewVideoQuestion::where('interview_video_id', $request->get('qid'))->paginate(10);

        return view('admin.interview-video-question.index')->with(compact('data', 'result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Add Interview Video Question';
        $data['qid'] = $request->get('qid');

        return view('admin.interview-video-question.create')->with(compact('data'));
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
            'question' => 'required',
            'answer' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $resourceObj = new InterviewVideoQuestion;
            $resourceObj->interview_video_id = $requestData['qid'];
            $resourceObj->question = $requestData['question'];
            $resourceObj->answer = $requestData['answer'];

            $resourceObj->save();

            return redirect()->route('interview-video-question.index', ['qid' => $requestData['qid']])->with('success', 'Interview video question added successfully');
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
        $data = InterviewVideoQuestion::find($id);
        $data['page_title'] = 'Interview Video Detail';

        return view('admin.interview-video-question.detail')->with(compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = InterviewVideoQuestion::find($id);
        $data['page_title'] = 'Edit Interview Video Question';
        $data['qid'] = $request->get('qid');

        return view('admin.interview-video-question.edit')->with(compact('data'));
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
            'question' => 'required',
            'answer' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $resourceObj = InterviewVideoQuestion::find($id);
            $resourceObj->question = $requestData['question'] ?? $resourceObj->question;
            $resourceObj->answer = $requestData['answer'] ?? $resourceObj->answer;

            $resourceObj->save();

            return redirect()->route('interview-video-question.index', ['qid' => $requestData['qid']])->with('success', 'Interview video question updated successfully');
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
            return redirect()->route('interview-videos.index')->with('error', 'Invalid question id');
        }

        $resourceObj = InterviewVideoQuestion::find($id);

        if ($resourceObj->delete()) {
            return redirect()->route('interview-video-question.index', ['qid' => $request->get('qid')])->with('success', 'Interview video question deleted successfully');
        }
        return redirect()->route('interview-video-question.index', ['qid' => $request->get('qid')])->with('error', DEFAULT_ERROR_MESSAGE);
    }
}
