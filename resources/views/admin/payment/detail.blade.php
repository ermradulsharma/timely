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
                <a href="{{ route('payment', ['page' => $data['page']]) }}" type="button" class="btn btn-primary">Back</a>
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
                            {{ $result['customer_details']['first_name'] ?? $result['customer_details']['name'] }} {{ $result['customer_details']['last_name'] ?? '' }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Service Provider Name:</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ $result['service_provider']['name'] ?? "" }}
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Amount</strong></label>
                        <div class="col-lg-9 col-form-label">
                            ${{ $result['amount'] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Transaction Id</strong></label>
                        <div class="col-lg-9 col-form-label">
                            ${{ $result['transaction_id'] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Time:</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ date('m/d/Y h:i A', strtotime($result['created_at'])) ?? '' }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><strong>Status:</strong></label>
                        <div class="col-lg-9 col-form-label">
                            {{ strtoupper($result['payment_status']) ?? '' }}
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