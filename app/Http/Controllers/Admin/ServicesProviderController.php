<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Services;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class ServicesProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Service Gaurdian';
        $userObj = User::latest();

        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;

            $userObj->whereRaw("(name ILIKE '%" . $q . "%' OR first_name ILIKE '%" . $q . "%' OR last_name ILIKE '%" . $q . "%' OR email ILIKE '%" . $q . "%' OR mobile ILIKE '%" . $q . "%' OR country_code ILIKE '%" . $q . "%' OR city ILIKE '%" . $q . "%')");
            $result = $userObj->paginate(10)->appends(['q' => $q]);
        } else {
            $result = $userObj->where('user_type', 'provider')->paginate(10);
        }

        $data['page'] = $request->get('page') ?? 1;


        return view('admin.service-providers.index')->with(compact('data', 'result'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data['page_title'] = 'Add Services';

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

            $resourceObj = new Services;
            $resourceObj->name = $requestData['name'];
            //$resourceObj->mobile = $requestData['mobile'] ?? NULL;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(SERVICES_IMAGE_PATH, $fileName);
                $resourceObj->image = $fileName;
            }


            $resourceObj->save();

            return redirect()->route('services.index')->with('success', 'Services added successfully');
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
        $data = Services::find($id);
        $data['page_title'] = 'Services Detail';

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
        $data = Services::find($id);
        $data['page_title'] = 'Edit Services';
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

            $resourceObj = Services::find($id);
            $resourceObj->name = $requestData['name'] ?? $resourceObj->name;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(SERVICES_IMAGE_PATH, $fileName);
                $resourceObj->image = $fileName;
            }

            $resourceObj->save();
            if (isset($requestData['page_number']) && !empty($requestData['page_number'])) {
                return redirect()->route('services.index', ['page' => $requestData['page_number']])->with('success', 'Services updated successfully');
            } else {
                return redirect()->route('services.index')->with('success', 'Services updated successfully');
            }
        } catch (\Exception $e) {
            $message = $e->getMessage() . ' Line No ' . $e->getLine() . ' in File' . $e->getFile();
            Log::error($e->getTraceAsString());

            return redirect()->back()->withInput()->with('error', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
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
            return redirect()->route('services.index')->with('error', 'Invalid id');
        }

        $resourceObj = Services::find($id);

        $pageNo = $request->get('page') ?? 1;

        if ($resourceObj->delete()) {
            return redirect()->route('services.index', ['page' => $pageNo])->with('success', 'Services deleted successfully');
        }

        return redirect()->route('services.index', ['page' => $pageNo])->with('error', DEFAULT_ERROR_MESSAGE);
    }

    public function serviceProviderDetails(Request $request, $id = NULL)
    {
        $data = [];

        $data['page_title'] = 'Service Gaurdian Details';
        $services = Services::where('user_id', $id)->get();
        $result = User::where('user_type', 'provider')->where('id', $id)->first();
        $data['page'] = $request->get('page') ?? 1;
        return view('admin.service-providers.detail')->with(compact('data', 'result', 'services'));
    }
}
