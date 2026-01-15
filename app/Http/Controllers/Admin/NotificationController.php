<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Notification';
        $roleObj = Notification::with('user_details')->latest();
        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;
            $roleObj->whereRaw("(name ILIKE '%" . $q . "%')");
        }
        $result = $roleObj->paginate(10);

        $data['page'] = $request->get('page') ?? 1;

        return view('admin.notification.index')->with(compact('data', 'result'));
    }

  
}
