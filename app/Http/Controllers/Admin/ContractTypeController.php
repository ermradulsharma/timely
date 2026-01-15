<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;

class ContractTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Contract Type';
        $contractTypeObj = ContractType::latest();
        
        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;

            $contractTypeObj->whereRaw("(title ILIKE '%" . $q . "%')");
            $result = $contractTypeObj->paginate(10)->appends(['q' => $q]);
        } else
        {
            $result = $contractTypeObj->paginate(10);

        }

        $data['page'] = $request->get('page') ?? 1;

        return view('admin.contract-type.index')->with(compact('data', 'result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data['page_title'] = 'Add Contract Type';

        return view('admin.contract-type.create')->with(compact('data'));
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

            $resourceObj = new ContractType;
            $resourceObj->title = $requestData['title'];
            $resourceObj->description = $requestData['description'] ?? NULL;

            $resourceObj->save();

            return redirect()->route('contract-type.index')->with('success', 'Contract type added successfully');
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
        $data = ContractType::find($id);
        $data['page_title'] = 'Contract Type Detail';

        return view('admin.contract-type.detail')->with(compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = ContractType::find($id);
        $data['page_title'] = 'Edit Contract Type';

        $data['page'] = $request->get('page') ?? 1;

        return view('admin.contract-type.edit')->with(compact('data'));
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

            $resourceObj = ContractType::find($id);
            $resourceObj->title = $requestData['title'] ?? $resourceObj->title;
            $resourceObj->description = $requestData['description'] ?? $resourceObj->description;

            $resourceObj->save();

            if (isset($requestData['page_number']) && !empty($requestData['page_number'])) {
                return redirect()->route('contract-type.index', ['page' => $requestData['page_number']])->with('success', 'Contract type updated successfully');
            } else {
                return redirect()->route('contract-type.index')->with('success', 'Contract type updated successfully');
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

        $resourceObj = ContractType::find($id);

        $page = $request->get('page') ?? 1;

        if ($resourceObj->delete()) {
            return redirect()->route('contract-type.index', ['page' => $page])->with('success', 'Contract type deleted successfully');
        }
        return redirect()->route('contract-type.index', ['page' => $page])->with('error', DEFAULT_ERROR_MESSAGE);
    }
}
