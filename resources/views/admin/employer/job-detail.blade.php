@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->


<div class="page-header page-header-light">
    <div class="page-header-content header-elements-lg-inline">
        <div class="page-title d-flex">
            <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Dashboard' }}</span></h4>
            <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                @if(isset($data['p']) && !empty($data['p']))
                <a href="{{ route('employer.show', [$data['q']]) }}" type="button" class="btn btn-primary">Back</a>
                @else
                <a href="{{ route('employer.jobs', [$result['employer_id']]) }}" type="button" class="btn btn-primary">Back</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <h5 class="card-title">Basic Detail</h5>
            <div class="card">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label"><strong>Company Name:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['company_name'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Title:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['title'] }}
                        </div>
                        <!-- <label class="col-lg-2 col-form-label"><strong>Position:</strong></label>
                                <div class="col-lg-4 col-form-label">
                                    {{ $result['position'] }}
                                </div> -->
                        <label class="col-lg-2 col-form-label"><strong>Experience:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['min_experience'] }}-{{ $result['max_experience'] }} years
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>City:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['city'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>State:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['state'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Address Line 1:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['address_line_1'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Address Line 2:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['address_line_2'] }}
                        </div>
                        <!-- <label class="col-lg-2 col-form-label"><strong>Address Line 3:</strong></label>
                                <div class="col-lg-4 col-form-label">
                                    {{ $result['address_line_3'] }}
                                </div> -->
                        <label class="col-lg-2 col-form-label"><strong>ZIP:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['pincode'] ?? '' }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Skills:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['skills'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Number Of Position:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['number_of_position'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Contract Type:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['contract_type_detail'] && $result['contract_type_detail']['title'] ? $result['contract_type_detail']['title'] : '' }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Type Of Employment:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['employment_type_detail'] && $result['employment_type_detail']['title'] ? $result['employment_type_detail']['title'] : '' }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Currency:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['currency'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Salary:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['hourly_rate'] }} {{ isset($result['payment_type']) && !empty($result['payment_type']) ? "(".$result['payment_type'] .")" : "" }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Qualification:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['qualification'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Description:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['description'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Benefits:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            {{ $result['benefits'] }}
                        </div>
                        <label class="col-lg-2 col-form-label"><strong>Status:</strong></label>
                        <div class="col-lg-4 col-form-label">
                            @if($result['status'] == '0')
                            <span class="badge badge-primary badge-pill">Open</span>
                            @else
                            <span class="badge badge-primary badge-pill badge-success">Closed</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-lg-6">
                    <h5 class="card-title">Shorlisted Users ({{ count($data['shortlisted_user'] ?? []) }})</h5>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="t-head">
                                    <tr class="">
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($data['shortlisted_user']) > 0)
                                    @foreach($data['shortlisted_user'] as $key => $value)
                                    <tr>
                                        <td>{{ $value['first_name'] ?? '' }}</td>
                                        <td>{{ $value['last_name'] ?? '' }}</td>
                                        <td>{{ $value['email'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center" colspan="4">No Record Found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h5 class="card-title">Hired Users ({{ count($data['hired_user'] ?? []) }})</h5>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="t-head">
                                    <tr class="">
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($data['hired_user']) > 0)
                                    @foreach($data['hired_user'] as $key => $value)
                                    <tr>
                                        <td>{{ $value['first_name'] ?? '' }}</td>
                                        <td>{{ $value['last_name'] ?? '' }}</td>
                                        <td>{{ $value['email'] ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center" colspan="4">No Record Found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h5 class="card-title">Applied Candidates ({{ count($result['applied'] ?? []) }})</h5>
            <div class="card">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="t-head">
                            <tr class="">
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($result['applied']) > 0)
                            @foreach($result['applied'] as $key => $value)
                            <tr>
                                <td>{{ $value['user_detail']['first_name'] ?? '' }}</td>
                                <td>{{ $value['user_detail']['last_name'] ?? '' }}</td>
                                <td>{{ $value['user_detail']['email'] ?? '' }}</td>
                                <td>{{ JOB_STATUS[$value['status']] }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="4">No Record Found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h5 class="card-title">Payments Detail</h5>
            <div class="card">
                <div class="table-responsive">
                    <table class="table my-datatable">
                        <thead class="t-head">
                            <tr class="">
                                <!-- <th>Transaction ID</th> -->
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($payments) > 0)
                            @foreach($payments as $key => $payment)
                            <tr>
                                <!-- <td>{{ $payment['transaction_id'] ?? '' }}</td> -->
                                <td>{{ $payment['user_detail']['first_name'] ?? '' }}</td>
                                <td>${{ $payment['amount'] ?? '' }}</td>
                                <td>{{ strtoupper($payment['payment_status']) ?? '' }}</td>
                                <td>{{ date('m/d/Y h:i A', strtotime($payment['created_at'])) ?? '' }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="8">No Record Found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_script')
<script>
    $(document).ready(function() {

    });
</script>
@endsection
@section('page_style')
<style>
    .popular-items-chart-wrapper {
        width: 50%;
        float: left;
    }
</style>
@endsection