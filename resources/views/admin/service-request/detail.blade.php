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
                <a href="{{ route('service-request', ['page' => $data['page']]) }}" type="button" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Customer Name :</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ $result['customerDetails']['first_name']?? "" }} {{ $result['customerDetails']['last_name']?? "" }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Service Provider Name:</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ $result['servicesProviderDetails']['first_name'] ?? "" }} {{ $result['servicesProviderDetails']['last_name'] ?? "" }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Requested Service Name:</strong></label>
                        <div class="col-lg-9 col-form-label">
                            @foreach($result['serviceBooking'] as $serviceBooking)
                            <span class="badge badge-warning"> {{ @$serviceBooking->service->name}} </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Customer address</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ $result['customerDetails']['address'] ?? "" }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Request Time:</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ date('m/d/Y h:i A', strtotime($result['created_at'])) ?? '' }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Approved Time:</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ date('m/d/Y h:i A', strtotime($result['updated_at'])) ?? '' }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Status:</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ $result['status'] == 0 ? 'Decline' : ""}} {{ $result['status'] == 1 ? 'Accepted' : ""}} {{ $result['status'] == 2 ? 'New' : ""}} {{ $result['status'] == 3 ? 'Completed' : ""}} {{ $result['status'] == 4 ? 'Cancelled' : ""}}
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

    .form-group {
        margin-bottom: 0px;
    }
</style>
@endsection