<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin') }}/css/icons/icomoon/styles.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin') }}/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin') }}/css/session-page.css" rel="stylesheet" type="text/css">

    <style>
        .login-cover {
            background: url("{{ asset('admin/images/login-bg.png') }}") no-repeat !important;
            background-size: cover !important;
        }
    </style>

    @yield('page_style')
</head>

<body>

    <div class="page-content login-cover">
        <div class="overlay_black"></div>

        @yield('content')

    </div>

    <script src="{{ asset('admin') }}/js/main/jquery.min.js"></script>
    <script src="{{ asset('admin') }}/js/main/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin') }}/js/app.js"></script>
    <script src="{{ asset('admin') }}/js/jquery.validate.min.js"></script>
    <script src="{{ asset('admin') }}/js/jquery.shorten.min.js"></script>
    @yield('page_script')

    <script>
        // Hide notification message
        $("div.alert").fadeTo(2000, 500).slideUp(500, function() {
            $("div.alert").slideUp(500);
        });
    </script>
</body>

</html>