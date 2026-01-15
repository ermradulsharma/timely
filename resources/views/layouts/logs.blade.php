<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ config('app.name') }} </title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"
        type="text/css">
    <link href="{{ asset('admin') }}/css/icons/icomoon/styles.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin') }}/css/icons/icomoon/styles.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin') }}/css/main.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin') }}/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- <link href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"> -->
    <link href="{{ asset('admin') }}/css/all.min.css" rel="stylesheet" type="text/css">


    @yield('page_style')
</head>

<body>

    <div class="page-content">

        @yield('content')

    </div>
    <!-- /page content -->

    <!-- Core JS files -->
    <script src="{{ asset('admin') }}/js/main/jquery.min.js"></script>
    <script src="{{ asset('admin') }}/js/main/bootstrap.bundle.min.js"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="{{ asset('admin') }}/js/plugins/visualization/d3/d3.min.js"></script>
    <script src="{{ asset('admin') }}/js/plugins/visualization/d3/d3_tooltip.js"></script>
    <script src="{{ asset('admin') }}/js/plugins/ui/moment/moment.min.js"></script>
    <script src="{{ asset('admin') }}/js/plugins/pickers/daterangepicker.js"></script>
    <script src="{{ asset('admin') }}/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script> -->

    <!-- Theme JS files -->
    <script src="{{ asset('admin') }}/js/plugins/tables/datatables/datatables.min.js"></script>

    <script src="{{ asset('admin') }}/js/demo_pages/datatables_basic.js"></script>

    <script src="{{ asset('admin') }}/js/demo_pages/dashboard.js"></script>
    <script src="{{ asset('admin') }}/js/jquery.validate.min.js"></script>
    <script src="{{ asset('admin') }}/js/jquery.shorten.min.js"></script>
    <script src="{{ asset('admin') }}/js/plugins/notifications/sweet_alert.min.js"></script>


    <script src="{{ asset('admin') }}/js/app.js"></script>
    <script src="{{ asset('admin') }}/js/demo_pages/extra_sweetalert.js"></script>
    <script src="{{ asset('admin') }}/js/common.js"></script>

    @yield('page_script')
</body>

</html>
