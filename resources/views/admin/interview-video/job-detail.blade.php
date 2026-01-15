@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->
<div class="content-wrapper">

    <div class="content-inner">

        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-lg-inline">
                <div class="page-title d-flex">
                    <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Dashboard' }}</span></h4>
                    <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
                </div>

                <div class="header-elements d-none">
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('employer.jobs', [$result['employer_id']]) }}" type="button" class="btn btn-primary">Back</a>
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
                                <label class="col-lg-1 col-form-label"><strong>Title:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['title'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Description:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['description'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Company Name:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['company_name'] }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-1 col-form-label"><strong>Position:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['position'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Experience:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['min_experience'] }}-{{ $result['max_experience'] }} years
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>City:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['city'] }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-1 col-form-label"><strong>State:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['state'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['min_experience'] }}-{{ $result['address_line_1'] }} years
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Address Line 2:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['address_line_2'] }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <!-- <label class="col-lg-1 col-form-label"><strong>Address Line 3:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['address_line_3'] }}
                                </div> -->
                                <label class="col-lg-1 col-form-label"><strong>Skills:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['skills'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Number Of Position:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['number_of_position'] }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-1 col-form-label"><strong>Contract Type:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['contract_type'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Type Of Employment:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['type_of_employment'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Currency:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['currency'] }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-1 col-form-label"><strong>Salary:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['hourly_rate'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Qualification:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['qualification'] }}
                                </div>
                                <label class="col-lg-1 col-form-label"><strong>Benefits:</strong></label>
                                <div class="col-lg-3 col-form-label">
                                    {{ $result['benefits'] }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-1 col-form-label"><strong>Status:</strong></label>
                                <div class="col-lg-3 col-form-label">
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
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <h5 class="card-title">Applied Candidates</h5>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table my-datatable">
                                <thead class="t-head">
                                    <tr class="bg-teal text-white">
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
                                    <tfoot class="datatable">
                                    <tr>
                                        <td class="text" colspan="8">
                                        <?php echo ('Showing ' . $result->firstItem() . ' to ' . $result->lastItem() . ' out of '  . $result->total() . ' entries'); ?>
                                        </td>
                                    </tr>
                                    </tfoot>
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
                    <h5 class="card-title">Shorlisted Users</h5>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table my-datatable">
                                <thead class="t-head">
                                    <tr class="bg-teal text-white">
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
                                    <tfoot class="datatable">
                                    <tr>
                                        <td class="text" colspan="8">
                                        <?php echo ('Showing ' . $result->firstItem() . ' to ' . $result->lastItem() . ' out of '  . $result->total() . ' entries'); ?>
                                        </td>
                                    </tr>
                                    </tfoot>
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