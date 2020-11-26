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
    <link href="https://localhost/royal_green/public_html/app-assets/css/css-circular-prog-bar.css" rel="stylesheet">


</head>
<!-- END: Head-->

<style type="text/css">

    
    [class*="col"] {
          padding-top: 1rem;
          padding-bottom: 1rem;
          border: 1px solid rgba(86,61,124,.2);
        }

    body {
      font-family: 'Montserrat', sans-serif;
    }


</style>

<!-- BEGIN: Body-->


    {{-- header 
    @include('layouts.include.header')
 --}}
    {{-- menu 
    @if (Auth::user()->rol_id == 0)
        @include('layouts.include.sidebar')
    @else
        @include('layouts.include.sidebar2')
    @endauth--}}

    {{-- contenido --}}
    <!-- BEGIN: Content-->

     <body data-menu="vertical-menu-modern" class="vertical-layout vertical-menu-modern 2-columns navbar-sticky fixed-footer menu-expanded">

    <!-- fixed-top-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top navbar-light navbar-shadow ">
            @include('layouts.include.header')

    </nav>

    <!-- BEGIN Navigation-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow expanded bg-transparent">
          @include('layouts.include.sidebar2')
    </div>
    <!-- END Navigation-->

    <!-- BEGIN Content-->
    <div class="app-content content ">
        <div class="content-wrapper">
            <div class="content-body bg-dark">
                
               @yield('content') 
            </div>
            
        </div>
    </div>
    <!-- END Content-->


  </body>
    <!-- END: Content-->
<!-- END: Body-->

@include('layouts.include.scripts')

</html>
