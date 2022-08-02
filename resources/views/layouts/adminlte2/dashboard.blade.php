<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <!-- Font Sahel -->
    <link rel="stylesheet" href="{{asset('dist/vazirmatn-v32.102/Vazirmatn-font-face.css')}}">
    <!-- All -->
    <link rel="stylesheet" href="{{asset('dist/fontawesome-free-5.15.4-web/css/all.min.css')}}">
    <!-- Bootstrap-3 -->
    <link rel="stylesheet" href="{{asset('dist/bootstrap-3.4.1-dist/css/bootstrap.min.css')}}">
    <!-- bootstrap-rtl -->
    <link rel="stylesheet" href="{{asset('dist/bootstrap-3.3.4-rtl-dist/bootstrap-rtl.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/adminlte-2-rtl/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/adminlte-2-rtl/css/_all-skins.min.css')}}">
    <!-- Bootstrap-Social -->
    <link rel="stylesheet" href="{{asset('dist/bootstrap-social-rtl/bootstrap-social.css')}}">
    <!-- style -->
    <link rel="stylesheet" href="{{asset('css/adminlte2.css')}}">
    @yield('POS_HEAD')
</head>

<body class="skin-blue sidebar-mini">
    @yield('POS_BEGIN')
    <div class="wrapper">
        <!-- Main Header -->
        <header class="main-header">
            <!-- Logo -->
            @include('layouts.adminlte2.logo')
            <!-- Navbar -->
            @include('layouts.adminlte2.navbar')
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        @auth
        @include('layouts.adminlte2.left')
        @endif
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content-header">
                <h1>@yield('content-header')</h1>
                <ol class="breadcrumb">@yield('content-breadcrumb')</ol>
            </section>
            <!-- Main content -->
            <section class="content">
                @yield('content')
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- jQuery -->
    <script src="{{asset('dist/jquery-3.6.0/jquery.min.js')}}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{asset('dist/jquery-ui-1.13.1/jquery-ui.min.js')}}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 3 -->
    <script src="{{asset('dist/bootstrap-3.4.1-dist/js/bootstrap.min.js')}}"></script>
    <!-- adminlte App -->
    <script src="{{asset('dist/adminlte-2-rtl/js/adminlte.min.js')}}"></script>
    <!-- script -->
    <script src="{{asset('js/adminlte2.js')}}"></script>

    @yield('POS_END')
</body>

</html>