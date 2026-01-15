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
                            <a href="{{ route('interview-video.index') }}" type="button" class="btn btn-primary">Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="card">
                    @include('success-error')
                    <div class="card-body">
                        <form action="{{ route('interview-video.update', [$data['id']]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PUT') }}
                            <input type="hidden" name="update_id" value="{{ $data['id'] }}">
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">Title</label>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input name="title" class="form-control form-control-lg" type="text"
                                                    placeholder="Title" value="{{ $data['title'] }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">Description</label>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <textarea name="description" class="form-control form-control-lg"
                                                    placeholder="Description" rows="7"
                                                    required>{{ $data['description'] }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2">Video</label>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input name="file" type="file" class="form-control h-auto video-file"
                                                    accept="video/mp4">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2"></label>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span><strong>Note: </strong>The file must be a file of type: mp4. Size
                                                    should not be greater than 100MB</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2"></label>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="card-img-actions">
                                                    <div class="card-img embed-responsive embed-responsive-16by9">
                                                        <video width="320" height="240" controls>
                                                            <source src="{{ $data['file'] }}"
                                                                id="interview-video-preview">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-2"></label>
                                <div class="col-lg-10">
                                    <button type="submit" id="form-submit-btn" class="btn btn-primary">Submit <i
                                            class="icon-paperplane ml-2"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_script')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".resource-status").on("change", function() {
                const resource_id = $(this).attr('data-id');
                const is_checked = $(this).is(":checked");

                let status = '0';
                if (is_checked) {
                    status = '1';
                }

                $.ajax({
                    url: "{{ route('update.user.status') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        resource_id: resource_id,
                        status: status,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {

                    },
                    error: function(error) {

                    }
                });
            });

            $(document).on("change", ".video-file", function(evt) {
                var $source = $('#interview-video-preview');
                $source[0].src = URL.createObjectURL(this.files[0]);
                $source.parent()[0].load();

                let fileSize = this.files[0].size;

                fileSize = sizeConverter(fileSize);

                if (fileSize > 100) {
                    $("#form-submit-btn").prop('disabled', true);

                    swal("File size should not be greater than 100MB");
                } else {
                    $("#form-submit-btn").prop('disabled', false);
                }
            });
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
