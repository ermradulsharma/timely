@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->


<div class="page-header page-header-light">
    <div class="page-header-content header-elements-lg-inline">
        <div class="page-title d-flex">
            <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Dashboard' }} -
                    {{ $data['user_detail']['first_name'] . ' ' . $data['user_detail']['last_name'] }}</span>
            </h4>
            <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">

                <a href="{{ route('user.index', ['page' => $data['page']]) }}" type="button" class="btn btn-primary">Back</a>
                <div style="margin-left: 5px;" class="header-elements d-none">
                    <div class="d-flex justify-content-center">
                        <form action="{{ route('user.jobs', [$data['user_detail']['id']]) }}">
                            <div class="navbar-search d-flex align-items-center py-2 py-lg-0">
                                <div class="form-group-feedback form-group-feedback-left flex-grow-1 mr-my">
                                    <input type="search" name="q" class="form-control"
                                        placeholder="Search" value="{{ $data['q'] ?? '' }}">
                                    <button type="submit" id="search-btn-my" class="btn btn-primary"><i
                                            class="icon-search4 fa fa-fw"></i></button>
                                    <div class="form-control-feedback">
                                        <i class="icon-search4 opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="content">

    <div class="card">
        <div class="table-responsive">
            <table class="table my-datatable">
                <thead class="t-head">
                    <tr class="">
                        <th>Title</th>
                        <th>Description</th>
                        <!-- <th>Position</th> -->
                        <th>Number Of Position(s)</th>
                        <th>Experience</th>
                        <th>Status</th>
                        <th>Hire Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($result) > 0)
                    @foreach ($result as $key => $value)
                    <tr>
                        <td>{{ $value['title'] ?? '' }}</td>
                        <td>{{ $value['description'] ?? '' }}</td>
                        <!-- <td>{{ $value['position'] ?? '' }}</td> -->
                        <td>{{ $value['number_of_position'] ?? '' }}</td>
                        <td>{{ $value['min_experience'] ?? '' }}-{{ $value['max_experience'] ?? '' }}
                            years</td>
                        <td>
                            @if ($value['status'] == '0')
                            <span class="badge badge-primary badge-pill">Open</span>
                            @else
                            <span class="badge badge-primary badge-pill badge-success">Closed</span>
                            @endif
                        </td>
                        <td>
                            <span
                                class="badge badge-secondary badge-pill">{{ JOB_STATUS[$value['applied_status']['status']] }}</span>
                        </td>
                        <td>
                            <a href="{{ route('user.job.detail', [$value['id'], 'q' => $data['user_detail']['id']]) }}"
                                class="" title="Detail"><i class="icon-eye"></i></a>
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
                    <td class="text-center" colspan="7">No Record Found</td>
                </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    @if (count($result) > 0)
    <div style="float: right;">
        {{ $result->links() }}
    </div>
    @endif
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