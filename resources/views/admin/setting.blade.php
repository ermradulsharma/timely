@extends('layouts.admin.admin')
@section('content')
<!-- Main content -->


<div class="page-header page-header-light">
    <div class="page-header-content header-elements-lg-inline">
        <div class="page-title d-flex">
            <h4> <span class="font-weight-semibold">{{ $data['page_title'] ?? 'Dashboard' }}</span></h4>
            <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>

<div class="content">
    @include('success-error')
    <div class="row">
        <div class="col-lg-4">

            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">SMTP</h6>
                </div>

                <div class="card-body">
                    <form action="{{ route('settings') }}" method="POST">
                        @csrf
                        <input type="hidden" name="request_type" value="smtp">
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="smtp_email" class="form-control" placeholder="Email"
                                value="{{ $smtp['email'] ?? '' }}" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" id="smtp-password" name="smtp_password"
                                class="form-control show-password-sd" placeholder="Password"
                                value="{{ $smtp['password'] ?? '' }}" autocomplete="off">
                            <div style="margin-top: 3px;">
                                <span>
                                    <input type="checkbox" class="show-password-checkbox">
                                </span>
                                <span style="margin-left: 3px;">Show Password</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Mail Host:</label>
                            <input type="text" name="smtp_host" class="form-control" placeholder="Mail Host"
                                value="{{ $smtp['host'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label>Mail Port:</label>
                            <input type="text" name="smtp_port" class="form-control" placeholder="Mail Port"
                                value="{{ $smtp['port'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label>From Address:</label>
                            <input type="text" name="smtp_from_address" class="form-control"
                                placeholder="From Address" value="{{ $smtp['from_address'] ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label>From Name:</label>
                            <input type="text" name="smtp_from_name" class="form-control"
                                placeholder="From Name" value="{{ $smtp['from_name'] ?? '' }}">
                        </div>

                        <div class="d-flex justify-content-start align-items-center">
                            <button type="submit" class="btn btn-primary">Submit <i
                                    class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Stripe Detail</h6>
                </div>

                <div class="card-body">
                    <form id="" action="{{ route('settings') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="request_type" value="stripe">
                        <div class="form-group">
                            <label>Public Key:</label>
                            <input type="text" name="public_key" class="form-control"
                                placeholder="Public Key" value="{{ $stripe['public_key'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>Secret Key:</label>
                            <input type="text" name="secret_key" class="form-control"
                                placeholder="Secret Key" value="{{ $stripe['secret_key'] ?? '' }}">
                        </div>

                        <div class="d-flex justify-content-start align-items-center">
                            <button type="submit" class="btn btn-primary">Submit <i
                                    class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Change Password</h6>
                </div>

                <div class="card-body">
                    <form id="change-password-form" action="{{ route('settings') }}" method="POST">
                        @csrf
                        <input type="hidden" name="request_type" value="change_password">
                        <div class="form-group">
                            <label>Current Password:</label>
                            <input type="password" name="old_password" class="form-control show-password-sd"
                                placeholder="Current Password" required>
                            <div style="margin-top: 3px;">
                                <span>
                                    <input type="checkbox" class="show-password-checkbox">
                                </span>
                                <span style="margin-left: 3px;">Show Password</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>New Password:</label>
                            <input type="password" id="password" name="password"
                                class="form-control show-password-sd" placeholder="New Password" required>
                            <div style="margin-top: 3px;">
                                <span>
                                    <input type="checkbox" class="show-password-checkbox">
                                </span>
                                <span style="margin-left: 3px;">Show Password</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password:</label>
                            <input type="password" name="password_confirmation"
                                class="form-control show-password-sd" placeholder="Confirm Password" required>
                            <div style="margin-top: 3px;">
                                <span>
                                    <input type="checkbox" class="show-password-checkbox">
                                </span>
                                <span style="margin-left: 3px;">Show Password</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start align-items-center">
                            <button type="submit" class="btn btn-primary">Submit <i
                                    class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Push Notification Server Key</h6>
                </div>

                <div class="card-body">
                    <form id="change-password-form1" action="{{ route('settings') }}" method="POST">
                        @csrf
                        <input type="hidden" name="request_type" value="push_notification_server_key">
                        <div class="form-group">
                            <label>Server Key:</label>
                            <textarea name="push_notification_server_key" class="form-control" placeholder="Server Key">{{ $push_notification_server_key['push_notification_server_key'] ?? null }}</textarea>
                        </div>

                        <div class="d-flex justify-content-start align-items-center">
                            <button type="submit" class="btn btn-primary">Submit <i
                                    class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Debug Mode</h6>
                </div>

                <div class="card-body">
                    <form id="change-password-form1" action="{{ route('settings') }}" method="POST">
                        @csrf
                        <input type="hidden" name="request_type" value="debug_mode">
                        <div class="form-group">
                            <div
                                class="custom-control custom-switch custom-switch-square custom-control-secondary mb-2">
                                <input type="checkbox" name="debug_mode" class="custom-control-input"
                                    id="sc_s_secondary"
                                    {{ isset($debug_mode['debug_mode']) && $debug_mode['debug_mode'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="sc_s_secondary">Debug Mode</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start align-items-center">
                            <button type="submit" class="btn btn-primary">Submit <i
                                    class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>




            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Application Details</h6>
                </div>
                <div class="card-body">
                    <form id="app" action="{{ route('settings') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="request_type" value="app">
                        @php
                        $smtp = $settings->smtp ?? null;
                        @endphp
                        <div class="form-group">
                            <label>APP Name:</label>
                            <input type="text" name="app_name" class="form-control"
                                placeholder="APP Name" value="{{ $app['app_name'] ?? '' }}">
                        </div>
                        {{-- <div class="form-group">
                                        <label>Search Distance Limit (Miles):</label>
                                        <input type="number" name="search_distance_limit" class="form-control"
                                            placeholder="Search Distance Limit (Miles)"
                                            value="{{ $app['search_distance_limit'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label>Instant Slot Notification Duration (Minutes):</label>
                    <input min="30" type="number" name="instant_slot_notification"
                        class="form-control"
                        placeholder="Please enter instant slot notification duration"
                        value="{{ $app['instant_slot_notification'] ?? '' }}">
                </div> --}}
                <div class="form-group">
                    <label>Terms & Conditions:</label>
                    <input type="text" name="terms_conditions" class="form-control"
                        placeholder="Please enter link for terms & conditions"
                        value="{{ $app['terms_conditions'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label>Privacy Policy:</label>
                    <input type="text" name="privacy_policy" class="form-control"
                        placeholder="Please enter link for privacy policy"
                        value="{{ $app['privacy_policy'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label>Rate us on Apple Store:</label>
                    <input type="text" name="rate_on_apple_store" class="form-control"
                        placeholder="Please enter app link for apple store"
                        value="{{ $app['rate_on_apple_store'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label>Rate us on Google Store:</label>
                    <input type="text" name="rate_on_google_store" class="form-control"
                        placeholder="Please enter app link for google store"
                        value="{{ $app['rate_on_google_store'] ?? '' }}">
                </div>
                <div class="d-flex justify-content-start align-items-center">
                    <button type="submit" class="btn btn-primary">Submit <i
                            class="icon-paperplane ml-2"></i></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Personal Detail</h6>
            </div>

            <div class="card-body">
                <form id="" action="{{ route('update.profile.admin') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="request_type" value="smtp">

                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" class="form-control" placeholder="Name"
                            value="{{ \Auth::user()->name ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Email"
                            value="{{ \Auth::user()->email ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label>Image:</label>
                        <input type="file" name="image" class="form-control h-auto">
                    </div>

                    <div class="form-group">
                        <div class="d-inline-block mb-3">
                            <img class="img-fluid" src="{{ \Auth::user()->image ?? '' }}" width="150"
                                height="150" alt="">
                            <div class="card-img-actions-overlay card-img rounded-circle">
                                <a href="#"
                                    class="btn btn-outline-white border-2 btn-icon rounded-pill">
                                    <i class="icon-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start align-items-center">
                        <button type="submit" class="btn btn-primary">Submit <i
                                class="icon-paperplane ml-2"></i></button>
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
        $.validator.addMethod("strong_password", function(value, element) {
            let password = value;
            if (!(/^(?=.*[A-Za-z])(?=.*[0-9])(.{8,50}$)/.test(password))) {
                return false;
            }
            return true;
        }, function(value, element) {
            let password = $(element).val();
            if (!(/^(?=.*[A-Za-z])(?=.*[0-9])(.{8,50}$)/.test(password))) {
                return 'Password should be at least 8 characters and a combination of letters and digits.';
            }
            return false;
        });

        $("#change-password-form").validate({
            rules: {
                old_password: {
                    required: true,
                },
                password: {
                    required: true,
                    strong_password: true,
                    minlength: 8,
                    maxlength: 32,
                },
                password_confirmation: {
                    required: true,
                    strong_password: true,
                    equalTo: "#password"
                },
            },
            message: {

            },
            errorElement: 'span',
            errorClass: 'error text-danger',
            submitHandler: function(form) {
                form.submit();
            }
        });

        $(".show-password-checkbox").on("change", function() {
            var x = $(this).parent("span").parent("div").siblings(".show-password-sd");
            if ($(x).attr("type") === "password") {
                $(x).attr("type", "text")
            } else {
                $(x).attr("type", "password")
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