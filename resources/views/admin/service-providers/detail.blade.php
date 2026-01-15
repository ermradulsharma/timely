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
                <a href="{{ route('service-gaurdian.index', ['page' => $data['page']]) }}" type="button"
                    class="btn btn-primary">Back</a>
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
                        <label class="col-lg-2 col-form-label"><strong>Name:</strong></label>
                        <div class="col-lg-10 col-form-label">
                            {{ $result['first_name'] ?? '' }} {{ $result['last_name'] ?? '' }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label"><strong>Email:</strong></label>
                        <div class="col-lg-10 col-form-label">
                            {{ $result['email'] ?? '' }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label"><strong>Mobile:</strong></label>
                        <div class="col-lg-10 col-form-label">
                            {{ $result['country_code'] ?? '' }}{{ $result['mobile'] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label"><strong>Address:</strong></label>
                        <div class="col-lg-10 col-form-label">
                            {{ $result['address'] ?? '' }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label"><strong>Image:</strong></label>
                        <div class="col-lg-10 col-form-label">
                            <div class="d-inline-block mb-3">
                                <img class="img-fluid rounded-circle" src="{{ $result['image'] ?? '' }}"
                                    width="150" height="150" alt="">
                                <div class="card-img-actions-overlay card-img rounded-circle">
                                    <a href="#" class="btn btn-outline-white border-2 btn-icon rounded-pill">
                                        <i class="icon-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" col-form-label">
                            <strong>Services providing</strong>
                        </label>
                    </div>
                    <div class="form-group row">
                        <?php $selected_services = explode(',', $result['services_provided']) ?>
                        <?php foreach ($services as $key => $service): ?>
                            <label class="col-lg-3 col-form-label"><input type="checkbox" name="services" value="" <?php if (in_array($service->id, $selected_services)) {
                                                                                                                        echo "checked";
                                                                                                                    } ?>> {{$service->name}}</label>
                        <?php endforeach ?>
                        </label>

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