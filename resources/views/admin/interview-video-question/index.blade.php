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
                        <a style="margin-right: 5px;" href="{{ route('interview-video.index', ['qid' => $data['qid']]) }}" type="button" class="btn btn-primary">Back</a>
                        <a href="{{ route('interview-video-question.create', ['qid' => $data['qid']]) }}" type="button" class="btn btn-primary ml-1">Add</a>

                    </div>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="card listing-card lc-overflow-hidden">
                <div class="table-responsive">
                    <table class="table my-datatable">
                        <thead class="t-head">
                            <tr class="bg-black text-white">
                                <th class="corner-left">#</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th class="corner-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($result) > 0)
                            <?php $i = $result->firstItem();  ?>
                            @foreach($result as $key => $value)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $value['question'] ?? '' }}</td>
                                <td>{{ $value['answer'] ?? '' }}</td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{ route('interview-video-question.edit', [$value['id'], 'qid' => $data['qid']]) }}" class="list-icons-item text-primary"><i class="icon-pencil7"></i></a>
                                        <a href="{{ route('interview-video-question.delete', [$value['id'], 'qid' => $data['qid']]) }}" class="list-icons-item text-danger"><i class="icon-trash"></i></a>
                                    </div>
                                </td>
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
                            <td class="text-center" colspan="5">No Record Found</td>
                        </tr>
                        @endif
                        </tbody>
                        <div style="float: right;">
                            {{ $result->links() }}
                        </div>
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