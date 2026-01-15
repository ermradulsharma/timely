<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Category';
        $roleObj = Category::latest();

        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;

            $roleObj->whereRaw("(name ILIKE '%" . $q . "%')");
        }

        $result = $roleObj->paginate(10);

        $data['page'] = $request->get('page') ?? 1;

        return view('admin.services.index')->with(compact('data', 'result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data['page_title'] = 'Add Category';

        return view('admin.services.create')->with(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $resourceObj = new Category;
            $resourceObj->name = $requestData['name'];
            //$resourceObj->mobile = $requestData['mobile'] ?? NULL;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(SERVICES_IMAGE_PATH, $fileName);
                $resourceObj->image = $fileName;
            }


            $resourceObj->save();

            return redirect()->route('categories.index')->with('success', 'Category added successfully');
        } catch (\Exception $e) {
            $message = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());

            return redirect()->back()->withInput()->with('error', $message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Category::find($id);
        $data['page_title'] = 'Category Detail';

        return view('admin.services
            .detail')->with(compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $data = Category::find($id);
        $data['page_title'] = 'Edit Category';

        $data['page'] = $request->get('page') ?? 1;

        return view('admin.services.edit')->with(compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errorResponse = validation_error_response($validator->errors()->toArray());

                return redirect()->back()->withInput()->with('error', $errorResponse['message']);
            }

            $requestData = $request->all();

            $resourceObj = Category::find($id);
            $resourceObj->name = $requestData['name'] ?? $resourceObj->name;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(SERVICES_IMAGE_PATH, $fileName);
                $resourceObj->image = $fileName;
            }

            $resourceObj->save();
            if (isset($requestData['page_number']) && !empty($requestData['page_number'])) {
                return redirect()->route('categories.index', ['page' => $requestData['page_number']])->with('success', 'Category updated successfully');
            } else {
                return redirect()->route('categories.index')->with('success', 'Category updated successfully');
            }
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
    public function destroy($id)
    {
        //
    }
    public function delete(Request $request, $id = NULL)
    {
        if (!$id) {
            return redirect()->route('categories.index')->with('error', 'Invalid id');
        }

        $resourceObj = Category::find($id);

        $pageNo = $request->get('page') ?? 1;

        if ($resourceObj->delete()) {
            return redirect()->route('categories.index', ['page' => $pageNo])->with('success', 'Category deleted successfully');
        }

        return redirect()->route('categories.index', ['page' => $pageNo])->with('error', DEFAULT_ERROR_MESSAGE);
    }
}
