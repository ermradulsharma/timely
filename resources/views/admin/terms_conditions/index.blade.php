@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->


<div class="page-header page-header-light">
    <div class="page-header-content header-elements-lg-inline">
        <div class="page-title d-flex">
            <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Dashboard' }}</span> </h4>
            <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
        </div>

    </div>
</div>

<div class="content">
    <div class="card">
        @include('success-error')
        <div class="card-body">
            <form action="{{ route('terms-conditions') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <label class="col-form-label col-lg-2">Terms & Conditions</label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="form-group">
                                    <textarea name="terms_conditions" class="form-control form-control-lg"
                                        type="text" placeholder="Terms & Conditions"
                                        style="height:60vh !important;"
                                        required>{{ $data['terms_conditions'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-2"></label>
                    <div class="col-lg-10">
                        <button type="submit" class="btn btn-primary">Submit <i
                                class="icon-paperplane ml-2"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('page_script')
<script>

</script>
@endsection
@section('page_style')
<style>

</style>
@endsection