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
                            <a href="{{ route('interview-video.create') }}" type="button" class="btn btn-primary">Add</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                @include('success-error')
                <div class="row">
                    @if (count($result) > 0)
                        @foreach ($result as $value)
                            <div class="col-sm-6 col-xl-3">
                                <div class="card video-card">
                                    <div class="card-img-actions mx-1 mt-1">
                                        <div class="card-img embed-responsive embed-responsive-16by9">
                                            {{-- <iframe allowfullscreen="" frameborder="0" mozallowfullscreen="" src="{{ $value['file'] }}" webkitallowfullscreen=""></iframe> --}}
                                            <video width="320" height="240" controls>
                                                <source src="{{ $value['file'] }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="d-flex align-items-start flex-nowrap">
                                            <div>
                                                <h6 class="font-weight-semibold mr-2">
                                                    @if (strlen($value['title']) > 42)
                                                        {{ substr($value['title'], 0, 41) . '...' }}
                                                    @else
                                                        {{ $value['title'] }}
                                                    @endif
                                                </h6>
                                                <span>
                                                    @if (strlen($value['description']) > 94)
                                                        {{ substr($value['description'], 0, 90) . '...' }}
                                                    @else
                                                        {{ $value['description'] }}
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- <div class="list-icons list-icons-extended ml-auto">
                                        <a href="{{ $value['file'] }}" class="list-icons-item" download><i class="icon-download top-0"></i></a>
                                    </div> -->
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="d-flex align-items-start flex-nowrap">
                                            <div class="list-icons">
                                                <a href="{{ route('interview-video.edit', [$value['id']]) }}"
                                                    class="list-icons-item text-primary" title="Edit" data-popup="tooltip"
                                                    title="" data-placement="bottom">
                                                    <i class="icon-pencil7"></i>
                                                </a>
                                                <a href="{{ route("interview-video.delete", [$value['id']]) }}"
                                                    data-href="{{ route('interview-video.delete', [$value['id']]) }}"
                                                    class="list-icons-item text-danger delete-resource-confirm"
                                                    title="Delete" data-popup="tooltip" title="" data-placement="bottom"><i
                                                        class="icon-trash"></i></a>
                                                <a href="{{ route('interview-video-question.index', ['qid' => $value['id']]) }}"
                                                    class="list-icons-item text-teal" title="Questions" data-popup="tooltip"
                                                    title="" data-placement="bottom"><i class="icon-file-text3"></i></a>
                                                <a href="{{ $value['file'] }}" class="list-icons-item text-success"
                                                    title="Download" data-popup="tooltip" title="" data-placement="bottom"
                                                    download><i class="icon-download4 top-0"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-sm-12 col-xl-12">
                            <div class="no-record-found">
                                <h3>No Record Found</h3>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_script')
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
        });
    </script>
@endsection
@section('page_style')
    <style>
        .popular-items-chart-wrapper {
            width: 50%;
            float: left;
        }

        .no-record-found {
            text-align: center;
            padding: 100px;
        }

        .video-card {
            min-height: 386px;
            height: auto;
        }

    </style>
@endsection
