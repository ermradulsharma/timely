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
                <form action="{{ route('payment') }}">
                    <div class="navbar-search d-flex align-items-center py-2 py-lg-0">
                        <div class="form-group-feedback form-group-feedback-left flex-grow-1">
                            <input type="search" name="q" class="form-control" placeholder="Search" value="{{ $data['q'] ?? '' }}">
                            <button type="submit" id="search-btn-my" class="btn btn-primary"><i class="icon-search4 fa fa-fw"></i></button>
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
                        <!-- <th>Transaction ID</th> -->
                        <th>No</th>
                        <th>Customer Name</th>
                        <th>Service Provider Name</th>
                        <th>Amount</th>
                        <th>Transaction Id</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($result) > 0)
                    @foreach($result as $key => $value)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $value['customer_details']['first_name'] ?? $value['customer_details']['name'] }} {{ $value['customer_details']['last_name'] ?? '' }}</td>
                        <td>{{ $value['service_provider']['first_name'] ?? $value['service_provider']['name'] ?? '' }} {{ $value['service_provider']['last_name'] ?? '' }}</td>
                        <td>$ {{ $value['amount'] ?? '' }}</td>
                        <td>{{ $value['transaction_id'] ?? '' }}</td>
                        <td>{{ strtoupper($value['payment_status']) ?? '' }}</td>
                        <td>{{ date('m/d/Y h:i A', strtotime($value['created_at'])) ?? '' }}</td>
                        <td>
                            <div class="list-icons">
                                <div class="dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle" data-toggle="dropdown"><i class="icon-cog6"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('payment.details', [$value['id'], 'page' => $data['page']]) }}" class="dropdown-item"><i class="icon-eye"></i>Detail</a>
                                    </div>
                                </div>
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