<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentType;
use App\Models\InterviewVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;

class EmploymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Employment Type';
        $employmentTypeObj = EmploymentType::latest();


        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;

            $employmentTypeObj->whereRaw("(title ILIKE '%" . $q . "%')");
            $result = $employmentTypeObj->paginate(10)->appends(['q' => $q]);
        } else {
            $result = $employmentTypeObj->paginate(10);
        }


        $data['page'] = $request->get('page') ?? 1;

        return view('admin.employment-type.index')->with(compact('data', 'result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data['page_title'] = 'Add Employment Type';

        return view('admin.employment-type.create')->with(compact('data'));
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

            $resourceObj = new EmploymentType;
            $resourceObj->title = $requestData['title'];
            $resourceObj->description = $requestData['description'] ?? NULL;

            $resourceObj->save();

            return redirect()->route('employment-type.index')->with('success', 'Employment type added successfully');
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
        $data = EmploymentType::find($id);
        $data['page_title'] = 'Employment Type Detail';

        return view('admin.employment-type.detail')->with(compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = EmploymentType::find($id);
        $data['page_title'] = 'Edit Employment Type';

        $data['page'] = $request->get('page') ?? 1;

        return view('admin.employment-type.edit')->with(compact('data'));
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

            $resourceObj = EmploymentType::find($id);
            $resourceObj->title = $requestData['title'] ?? $resourceObj->title;
            $resourceObj->description = $requestData['description'] ?? $resourceObj->description;

            $resourceObj->save();

            if (isset($requestData['page_number']) && !empty($requestData['page_number'])) {
                return redirect()->route('employment-type.index', ['page' => $requestData['page_number']])->with('success', 'Employment type updated successfully');
            } else {
                return redirect()->route('employment-type.index')->with('success', 'Employment type updated successfully');
            }
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
            return redirect()->route('interview-videos.index')->with('error', 'Invalid id');
        }

        $resourceObj = EmploymentType::find($id);

        $page = $request->get('page') ?? 1;

        if ($resourceObj->delete()) {
            return redirect()->route('employment-type.index', ['page' => $page])->with('success', 'Employment type deleted successfully');
        }
        return redirect()->route('employment-type.index', ['page' => $page])->with('error', DEFAULT_ERROR_MESSAGE);
    }
}
