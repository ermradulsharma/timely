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
                            <a href="{{ route('employer.index', ['page' => $data['page']]) }}" type="button"
                                class="btn btn-primary">Back</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><strong>First Name:</strong></label>
                                    <div class="col-lg-10 col-form-label">
                                        {{ $data['first_name'] ?? '' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><strong>Last Name:</strong></label>
                                    <div class="col-lg-10 col-form-label">
                                        {{ $data['last_name'] ?? '' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><strong>Email:</strong></label>
                                    <div class="col-lg-10 col-form-label">
                                        {{ $data['email'] ?? '' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><strong>Mobile:</strong></label>
                                    <div class="col-lg-10 col-form-label">
                                        {{ $data['country_code'] ?? '' }}{{ $data['mobile'] ?? '' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><strong>Address:</strong></label>
                                    <div class="col-lg-10 col-form-label">
                                        {{ $data['address'] ?? '' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><strong>City:</strong></label>
                                    <div class="col-lg-10 col-form-label">
                                        {{ $data['city'] ?? '' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><strong>State:</strong></label>
                                    <div class="col-lg-10 col-form-label">
                                        {{ $data['state'] ?? '' }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"><strong>Image:</strong></label>
                                    <div class="col-lg-10 col-form-label">
                                        <div class="d-inline-block mb-3">
        <img class="img-fluid rounded-circle" src="{{ $data['image'] ?? '' }}"
            width="150" height="150" alt="">
        <div class="card-img-actions-overlay card-img rounded-circle">
            <a href="#" class="btn btn-outline-white border-2 btn-icon rounded-pill">
                <i class="icon-pencil"></i>
            </a>
        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <h5 class="card-title">Latest 5 Jobs ({{ count($result ?? []) }})</h5>
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table my-datatable">
                                    <thead class="t-head">
                                        <tr class="">
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
                                        @if (count($result) > 0)
                                            @foreach ($result as $key => $value)
                                                <tr>
                                                    <td>{{ $value['title'] ?? '' }}</td>
                                                    <td>{{ $value['description'] ?? '' }}</td>
                                                    <td>{{ $value['position'] ?? '' }}</td>
                                                    <td>{{ $value['number_of_position'] ?? '' }}</td>
                                                    <td>{{ $value['min_experience'] ?? '' }}-{{ $value['max_experience'] ?? '' }}
                                                        years</td>
                                                    <td>
                                                        @if ($value['status'] == '0')
                                                            <span class="badge badge-primary badge-pill">Open</span>
                                                        @else
                                                            <span
                                                                class="badge badge-primary badge-pill badge-success">Closed</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('employer.job.detail', [$value['id'], 'q' => $value['employer_id'], 'p' => 'edp']) }}"
                                                            class="" title="Detail"><i
                                                                class="icon-eye"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center" colspan="7">No Record Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_script')
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection
@section('page_style')
    <style>
        .popular-items-chart-wrapper {
            width: 50%;
            float: left;
        }

        .form-group {
            margin-bottom: 0px;
        }

    </style>
@endsection
