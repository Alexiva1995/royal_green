<html lang="{{ app()->getLocale() }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $settings->name }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/semi-dark-layout.css')}}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/authentication.css')}}">
    <!-- END: Page CSS-->

    <style>

        .btn-whatsapp {
           display:block;
           width:100px;
           height:100px;
           color:#fff;
           position: fixed;
           right:20px;
           bottom:20px;
           border-radius:50%;
           line-height:80px;
           text-align:center;
           z-index:999;
    }

     footer{

            background: #11262C 0% 0% no-repeat padding-box;
            opacity: 1;
            position: fixed;
            bottom:0;
            right:0;
            width: 100%;

        }

       
        /* [class*="col-"] {
          padding-top: 1rem;
          padding-bottom: 1rem;
          border: 1px solid rgba(86,61,124,.2);
        } */

        html body.bg-full-screen-image{
            background: url("{{asset('assets/fondo.jpg')}}") no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        @media only screen and (max-width: 600px){
            html body.bg-full-screen-image{
                background: url("{{asset('assets/fondo-movil.jpg')}}") no-repeat center center;
                background-size: cover;

            }
        }
            
         
        .btn-primary{
            border-color: rgba(106,193,255,1);
            border-color: -moz-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 0%);
            border-color: -webkit-gradient(left top, right top, color-stop(0%, rgba(106,193,255,1)), color-stop(100%, rgba(104,255,113,1)));
            border-color: -webkit-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 0%);
            border-color: -o-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 0%);
            border-color: -ms-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 0%);
            border-color: linear-gradient(to right, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 0%);

            background: #66FFCC 0% 0% no-repeat padding-box;
            background: -moz-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);
            background: -webkit-gradient(left top, right top, color-stop(0%, rgba(106,193,255,1)), color-stop(100%, rgba(104,255,113,1)));
            background: -webkit-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);
            background: -o-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);
            background: -ms-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);
            border-radius: 4px;
            opacity: 1;
            background: #66FFCC !important;
        }

        .btn-conectar{

            color: #66FFCC;
        }

        .btn-outline-primary {

            border-color: rgba(106,193,255,1);
            border-color: -moz-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);
            border-color: -webkit-gradient(left top, right top, color-stop(0%, rgba(106,193,255,1)), color-stop(100%, rgba(104,255,113,1)));
            border-color: -webkit-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);
            border-color: -o-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);
            border-color: -ms-linear-gradient(left, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);
            border-color: linear-gradient(to right, rgba(106,193,255,1) 0%, rgba(104,255,113,1) 100%);

            border: 1px solid;
            background-color: transparent;
            color: #6AC1FF;
        }

        .btn-defaul{
            background: #11262C 0% 0% no-repeat padding-box;
            border-radius: 32px;
            opacity: 1;
        }


    </style>

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    <!-- END: Custom CSS-->


    <div class="navbar-wrapper">
        <button type="button" class="btn btn-default btn-lg text-white">
        <span class="fa fa-arrow-left"></span>   
             Regresar al inicio 
        </button>
    </div>
 
</head>

<body
    class="vertical-layout vertical-menu-modern 1-column  navbar-floating bg-full-screen-image  blank-page blank-page "
    data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                @yield('content')

      
      
       <!-- START FOOTER Light-->
    <footer class=" navbar-wrapper background-color">  

        <button type="button" class="btn btn-default waves-effect waves-light">  © Royal Green </button>
         <button type="button" class="btn btn-icon waves-effect waves-light float-right"><i class="feather icon-twitter"></i></button>
         <button type="button" class="btn btn-icon waves-effect waves-light float-right"><i class="feather icon-instagram"></i></button>
         <button type="button" class="btn btn-icon waves-effect waves-light float-right"><i class="feather icon-youtube"></i></button>
         <button type="button" class="btn btn-icon waves-effect waves-light float-right"><i class="feather icon-facebook"></i></button>
    </footer>

    <!-- END FOOTER Light-->

 </div>


</div>
    

    </div>


<div class="btn-whatsapp">
<a href="https://t.me/ALIAS" target="_blank">
 <button type="button " class="btn btn-icon rounded-circle btn-defaul" style="background: #11262C;">
        <i class="font-medium-5 fa fa-paper-plane-o"></i> 
    </button>
</a>
</div>


</body>




<!-- BEGIN: Vendor JS-->
<script src="{{asset('/app-assets/vendors/js/vendors.min.js')}}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('/app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('/app-assets/js/core/app.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/components.js')}}"></script>
<!-- END: Theme JS-->
<script src="{{asset('assets/scripts/general.js')}}"></script>
<!-- BEGIN: Page JS-->
<!-- END: Page JS-->
</html>