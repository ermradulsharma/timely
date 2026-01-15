@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->
<div class="content-wrapper">

    <div class="content-inner">

        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-lg-inline">
                <div class="page-title d-flex">
                    <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Dashboard' }} - {{ $data['employer_detail']['first_name'] . " " . $data['employer_detail']['last_name'] }}</span></h4>
                    <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
                </div>

                <div class="header-elements d-none">
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('employer.index') }}" type="button" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="card">
                <div class="table-responsive">
                    <table class="table my-datatable">
                        <thead class="t-head">
                            <tr class="bg-teal text-white">
                                <th>Title</th>
                                <th>Description</th>
                                <th>Position</th>
                                <th>Number Of Seats</th>
                                <th>Experience</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($result))
                            @foreach($result as $key => $value)
                            <tr>
                                <td>{{ $value['title'] ?? '' }}</td>
                                <td>{{ $value['description'] ?? '' }}</td>
                                <td>{{ $value['position'] ?? '' }}</td>
                                <td>{{ $value['number_of_position'] ?? '' }}</td>
                                <td>{{ $value['min_experience'] ?? '' }}-{{ $value['max_experience'] ?? '' }} years</td>
                                <td>
                                    @if($value['status'] == '0')
                                    <span class="badge badge-primary badge-pill">Open</span>
                                    @else
                                    <span class="badge badge-primary badge-pill badge-success">Closed</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('employer.job.detail', [$value['id']]) }}" class="" title="Detail"><i class="icon-eye"></i></a>
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