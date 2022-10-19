<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- jQuery -->
        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
        <!-- JQVMap -->
        <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
        <!-- summernote -->
        <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">

        <!-- Datatables -->
		<link href="{{ asset('/plugins/r_plugins/DataTables/datatables.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('/plugins/r_plugins/DataTables/Responsive-2.2.2/css/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

        <script src="{{ asset('/plugins/r_plugins/DataTables/jquery.datatables.min.js') }}"></script>
        <script src="{{ asset('/plugins/r_plugins/DataTables/datatables.js') }}"></script>
        <script src="{{ asset('/plugins/r_plugins/DataTables/dataTables.bootstrap4.min.js') }}"></script>
        <!-- DataTables Responsive -->
        <script src="{{ asset('/plugins/r_plugins/DataTables/Responsive-2.2.2/js/responsive.bootstrap4.js') }}"></script>
        <!-- Datatables closes-->

        <!-- Bootstrap Validator -->
        <link href="{{ asset('/plugins/r_plugins/bootstrap-validator/bootstrapValidator.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ asset('/plugins/r_plugins/bootstrap-validator/bootstrapValidator.js') }}"></script>
         <!-- Bootstrap Validator closes-->

        <!-- Select2 -->
        <script src="{{ asset('/plugins/r_plugins/select2/select2.full.min.js') }}"></script>
		<link rel="stylesheet" href="{{ asset('/plugins/r_plugins/select2/select2.min.css')}}">
         <!-- Select2 closes-->

        <!-- Sweet Alert Plugin -->
        <link rel="stylesheet" href="{{ asset('plugins/r_plugins/sweetAlerts/sweetalert.css') }}" />
        <script src="{{ asset('plugins/r_plugins/sweetAlerts/sweetalert.min.js') }}"></script>
        <!-- Sweet Alert Plugin Close -->

    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">

            <!-- Preloader -->
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTELogo" height="60" width="60">
            </div>

            <!-- header -->
            @include('layouts.header')


            <!-- sidebar -->
            @include('layouts.sidebar')


            <!-- dynamic body -->
            @yield('content_dynamic')


            <footer class="main-footer">
                <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.2.0
                </div>
            </footer>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>
        <!-- ./wrapper -->

        <!-- footer -->
        @include('layouts.footer')


    </body>

</html>   