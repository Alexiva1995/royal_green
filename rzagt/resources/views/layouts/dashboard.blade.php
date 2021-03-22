<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    {{-- <meta name="description"
        content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities."> --}}
    {{-- <meta name="keywords"
        content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app"> --}}
    <meta name="author" content="VALDUSOFT">
    <title>{{$settings->name}}</title>
    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    @include('layouts.include.styles')


</head>


<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click"
    data-menu="vertical-menu-modern" data-col="2-columns">

    @include('layouts.include.header')


    <!-- BEGIN Navigation-->

    @if (Auth::user()->ID != 1)
    @include('layouts.include.sidebar2')
    @else
    @include('layouts.include.sidebar')
    @endif
    <!-- END Navigation-->

    <!-- BEGIN Content-->
    <div class="app-content content ">
        <div class="content-wrapper">

            {{-- migaja de pan --}}
            @include('layouts.include.breadcrum')

            <div class="content-body bg-dark">
                @yield('content')
                
                {{-- acciones globales --}}
                @include('layouts.include.acciones_globales')
            </div>

        </div>
    </div>
    <!-- END Content-->


</body>
<!-- END: Content-->
<!-- END: Body-->

@include('layouts.include.scripts')

</html>