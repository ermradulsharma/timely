<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentRequest;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Models\User;
use App\Models\ServicesRequest;

class ServicesRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Services request list';
        $userObj = User::latest();
        $resourceObj = Booking::with('customerDetails', 'servicesProviderDetails', 'serviceBooking', 'serviceBooking.service')->orderBy('id', 'desc')->latest('created_at');

        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;
            $userIds = $userObj->whereRaw("(name ILIKE '%" . $q . "%' OR first_name ILIKE '%" . $q . "%' OR last_name ILIKE '%" . $q . "%' OR email ILIKE '%" . $q . "%' OR mobile ILIKE '%" . $q . "%' OR country_code ILIKE '%" . $q . "%' OR city ILIKE '%" . $q . "%')")->pluck('id')->toArray();
            $result = $resourceObj->whereIn('user_id_send_by', $userIds)->orWhereIn('provider_send_to', $userIds)->paginate(10)->appends(['q' => $q]);
        }

        $result = $resourceObj->paginate(10);

        $data['page'] = $request->get('page') ?? 1;
        return view('admin.service-request.index')->with(compact('data', 'result'));
    }

    public function serviceReport(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Services report list';
        $userObj = User::latest();
        $booking_ids = PaymentRequest::where('payment_status', 4)->pluck('booking_id')->toArray() ?? [];
        $resourceObj = Booking::whereIn('id', $booking_ids)->with('customerDetails', 'servicesProviderDetails', 'serviceBooking', 'serviceBooking.service')->orderBy('id', 'desc')->latest('created_at');

        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;
            $userIds = $userObj->whereRaw("(name ILIKE '%" . $q . "%' OR first_name ILIKE '%" . $q . "%' OR last_name ILIKE '%" . $q . "%' OR email ILIKE '%" . $q . "%' OR mobile ILIKE '%" . $q . "%' OR country_code ILIKE '%" . $q . "%' OR city ILIKE '%" . $q . "%')")->pluck('id')->toArray();
            $result = $resourceObj->whereIn('user_id_send_by', $userIds)->orWhereIn('provider_send_to', $userIds)->paginate(10)->appends(['q' => $q]);
        }

        $result = $resourceObj->paginate(10);

        $data['page'] = $request->get('page') ?? 1;
        return view('admin.service-request.report')->with(compact('data', 'result'));
    }

    public function servicesRequestDetail(Request $request, $id = NULL)
    {
        $data = [];
        $data['page_title'] = 'Services request detail';
        $data['page'] = $request->get('page') ?? 1;
        $result = Booking::with('customerDetails', 'servicesProviderDetails', 'serviceBooking', 'serviceBooking.service')->where('id', $id)->first();
        return view('admin.service-request.detail')->with(compact('data', 'result'));
    }

    public function payment(Request $request)
    {
        $data = [];
        $data['page_title'] = 'Payment';
        $userObj = User::latest();
        $resourceObj = Payment::with('customer_details', 'service_provider')->orderBy('id', 'desc')->latest('created_at');
        if ($request->has('q') && !empty($request->get('q'))) {
            $q = $request->get('q');
            $data['q'] = $q;
            $userIds = $userObj->whereRaw("(name ILIKE '%" . $q . "%' OR first_name ILIKE '%" . $q . "%' OR last_name ILIKE '%" . $q . "%' OR email ILIKE '%" . $q . "%' OR mobile ILIKE '%" . $q . "%' OR country_code ILIKE '%" . $q . "%' OR city ILIKE '%" . $q . "%')")->pluck('id')->toArray();
            $result = $resourceObj->whereRaw("(transaction_id ILIKE '%" . $q . "%')")->whereIn('user_id', $userIds)->orWhereIn('payment_by', $userIds)->paginate(10)->appends(['q' => $q]);
        } else {
            $result = $resourceObj->paginate(10);
        }
        $data['page'] = $request->get('page') ?? 1;
        return view('admin.payment.index')->with(compact('data', 'result'));
    }

    public function paymentDetail(Request $request, $id = NULL)
    {
        $data = [];
        $data['page_title'] = 'Payment';
        $result = Payment::with('customer_details', 'service_provider')->where('id', $id)->first();
        $data['page'] = $request->get('page') ?? 1;
        return view('admin.payment.detail')->with(compact('data', 'result'));
    }
}
