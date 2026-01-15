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
                <form action="{{ route('in.app.purchase.payment') }}">
                    <div class="navbar-search d-flex align-items-center py-2 py-lg-0">
                        <div class="form-group-feedback form-group-feedback-left flex-grow-1">
                            <input type="search" name="q" class="form-control" placeholder="Search"
                                value="{{ $data['q'] ?? '' }}">
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

<div class="content">

    <div class="card">
        <div class="table-responsive">
            <table class="table my-datatable">
                <thead class="t-head">
                    <tr class="">
                        <th>Name</th>
                        <th>Slot Date</th>
                        <th>Slot Start Time</th>
                        <th>Slot End Time</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($result) > 0)
                    @foreach ($result as $key => $value)
                    <tr>
                        <?php
                        $slotStartTime = $value['slot_date'] . ' ' . $value['start_time'];
                        $slotEndtTime = $value['slot_date'] . ' ' . $value['end_time'];
                        ?>
                        <td>{{ $value['user_detail']['first_name'] ?? '' }}</td>
                        <td>{{ date('d F, Y', strtotime($value['slot_date'])) ?? '' }}</td>
                        <td>{{ date('h:i A', strtotime($slotStartTime)) ?? '' }}</td>
                        <td>{{ date('h:i A', strtotime($slotEndtTime)) ?? '' }}</td>
                        <td>${{ 5 ?? '' }}</td>
                    </tr>
                    @endforeach
                <tfoot class="datatable">
                    <tr>
                        <td class="text" colspan="8">
                            <?php echo 'Showing ' . $result->firstItem() . ' to ' . $result->lastItem() . ' out of ' . $result->total() . ' entries'; ?>
                        </td>
                    </tr>
                </tfoot>
                @else
                <tr>
                    <td class="text-center" colspan="8">No Record Found</td>
                </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div style="float: right;">
        {{ $result->links() }}
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