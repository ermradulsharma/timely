@extends('layouts.logs')
@section('content')


<div class="content-wrapper">

    <div class="content-inner">

        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-lg-inline">
                <div class="page-title d-flex">
                    <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Request Response Logs' }}</span>
                    </h4>
                    <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
                </div>


            </div>
        </div>

        <div class="content">
            <form id="filter-form" action="{{ route('daily.logs') }}">
                <div class="form-group row">
                    <div class="col-lg-2">
                        <label>Date</label>
                        <input type="date" name="log_date" class="form-control filter-fields" value="{{ date("Y-m-d", strtotime($data['selected_date'])) }}" data-id="{{ date("Y-m-d", strtotime($data['selected_date'])) }}">
                    </div>
                    <div class="col-lg-2">
                        <label>Action</label>
                        <select name="action" id="action" class="form-control filter-fields">
                            @foreach($data['action'] as $key => $value)
                            <option value="{{ $key }}" {{ $data['selected_action'] == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1">
                        <label></label>
                        <button type="submit" class="btn btn-primary form-control" style="margin-top: 7px;">Submit</button>
                    </div>
                    <div class="col-lg-1">
                        <label></label>
                        <input type="reset" class="btn btn-primary form-control" style="margin-top: 7px;" value="Reset">
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="t-head">
                            <tr role="row">
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 135px;">Date</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 135px;">Type</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 297px;">Action
                                </th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 245px;">Request
                                    Params
                                </th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 135px;">
                                    Response
                                </th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 135px;">
                                    Extra
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($data['data']) > 0)
                            @foreach ($data['data'] as $key => $value)
                            <tr role="row" class="odd">
                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('d F Y, g:i A') }}
                                </td>
                                <td>
                                    @if ($value['type'] == 'error')
                                    <span
                                        class="btn btn-sm light btn-danger">{{ ucfirst($value['type']) }}</span>
                                    @elseif($value['type'] == 'info')
                                    <span
                                        class="btn btn-sm light btn-primary">{{ ucfirst($value['type']) }}</span>
                                    @endif
                                </td>
                                <td>{{ $value['action'] }}</td>
                                <td class="request-params word-break-rr">
                                    @if (count($value['request_params']) > 0)
                                    @foreach ($value['request_params'] as $key => $item)
                                    <strong>{{ $key }}: </strong>{{ $item }}<br>
                                    @endforeach
                                    @endif
                                <td class="response word-break-rr">
                                    @if (count($value['response']) > 0)
                                    @foreach ($value['response'] as $key => $item)
                                    <strong>{{ $key }}:
                                    </strong>{{ is_bool($item) ? ($item ? 'TRUE' : 'FALSE') : $item }}<br>
                                    @endforeach
                                    @endif
                                </td>
                                <td class="extra word-break-rr">{{ json_encode($value['extra']) }}</td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="6" class="text-center">No Record Found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="float: right;">
                {{ $data['data']->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_script')

<script>
    (function($) {

        /*var table = $('#example5').DataTable({
            searching: false,
            paging: false,
            select: false,
            info: false,
            lengthChange: false,
            ordering: false

        });*/

        $('#example tbody').on('click', 'tr', function() {
            var data = table.row(this).data();

        });

        $(".request-params").shorten({
            moreText: 'read more',
            lessText: 'read less',
            showChars: 500,
        });

        $(".response").shorten({
            moreText: 'read more',
            lessText: 'read less',
            showChars: 500,
        });

        $(".extra").shorten({
            moreText: 'read more',
            lessText: 'read less',
            showChars: 500,
        });

        /* $(".filter-fields").on("change", function() {
            $("#filter-form").submit();
        }); */
    })(jQuery);
</script>
@endsection
@section('page_style')

<style>
    .word-break-rr {
        word-break: break-word;
    }

    .form-control[readonly] {
        background-color: white !important;
    }

    .datatable-header {
        display: none;
    }
</style>
@endsection