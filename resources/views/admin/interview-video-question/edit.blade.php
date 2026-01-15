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
                        <a href="{{ route('interview-video-question.index', ['qid' => $data['qid']]) }}" type="button" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="card">
                @include('success-error')
                <div class="card-body">
                    <form action="{{ route('interview-video-question.update', [$data['id']]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{ method_field('PUT') }}
                        <input type="hidden" name="update_id" value="{{ $data['id'] }}">
                        <input type="hidden" name="qid" value="{{ $data['qid'] }}">
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">Question</label>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input name="question" class="form-control form-control-lg" type="text" placeholder="Question" value="{{ $data['question'] }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2">Answer</label>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <textarea name="answer" class="form-control form-control-lg" placeholder="Answer" rows="7" required>{{ $data['answer'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-lg-2"></label>
                            <div class="col-lg-10">
                                <button type="submit" class="btn btn-primary">Submit <i class="icon-paperplane ml-2"></i></button>
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
</style>
@endsection