<!-- BEGIN: Vendor CSS-->

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/apexcharts.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/tether-theme-arrows.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/tether.min.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/shepherd-theme-default.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}">

@stack('vendor_css')

<!-- END: Vendor CSS-->



<!-- BEGIN: Theme CSS-->

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap-extended.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/colors.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/components.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/dark-layout.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/semi-dark-layout.css')}}">

@stack('theme_css')



<!-- BEGIN: Page CSS-->

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/dashboard-analytics.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/card-analytics.css')}}">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/tour/tour.css')}}">

@stack('page_css')

<!-- END: Page CSS-->



<!-- BEGIN: Custom CSS-->

{{-- <link rel="stylesheet" type="text/css" href="{{('assets/css/style.css')}}"> --}}

<style>


.header-navbar.floating-nav {
        width: calc(100% - calc(2.2rem * 2) - 260px);
        left: unset;
        border-radius: 0px;
        padding: 10px 0px;
    }

    .navbar-light{
        background: #11262c;
    }

    body.vertical-layout.vertical-menu-modern.menu-expanded .main-menu {
        width: 260px !important;
    }

    .header-navbar .navbar-container .bookmark-wrapper ul.nav li>a.nav-link {
        padding: 1rem 1.4rem !important;
    }

    .main-menu.menu-light .navigation{
        margin: 0 20px !important;
    }

    .main-menu .navbar-header {
        width: 260px !important;
    }

    .main-menu ul.navigation-main > li:first-child{
        margin-top: 0 !important;
    }

    .main-menu.menu-light .navigation li a{
        padding: 0px 10px;
        margin: 15px 0px;
    }

    .main-menu.menu-light .navigation li.hover > a{
        background: #66ffcc !important;
        border-radius: 4px;

    }

    body {
        font-family: 'Montserrat', sans-serif;
    }

    #diseng {
        border-radius: 50%;
        height: 120px;
        width: 120px;
        background-repeat: no-repeat !important;
        background-size: contain !important;
        background-position: center center !important;
        margin: 0 auto;
        border: 1px solid #00646d;
    }

    .bg-blue-2{
        background: #06171B !important;
    }

    .bg-green-2{
        background: #68FF71 !important;
    }

    .bg-orange-alt {
        background: #00646d;
        border-color: #00646d;
    }

    .bg-gradient-danger {
        background: #640064;
        color: #FFFFFF;
        background-image: -webkit-linear-gradient(60deg, #EA5455, rgba(234, 84, 85, 0.5));
        background-image: linear-gradient(30deg, #0a343e, rgba(42, 223, 192, 0.5)) !important;
        background-repeat: repeat-x;
    }

    .bg-orange-alt-2 {
        background: rgba(207, 96, 70, 0.5)
    }

    .bg-blue-alt-2 {
        background: rgba(62, 135, 175, 0.5)
    }

    .text-alt-blue {
        color: #3e87af;
    }

    .text-alt-orange {
        color: #00646d;
    }

    .custom-control-input:checked~.custom-control-label::before {
        border-color: #00646d;
        background-color: #00646d;
    }

    .btn-primary{
        background: #66FFCC 0% 0% no-repeat padding-box;
        border-radius: 4px;
        opacity: 1;
        letter-spacing: 0px;
        color: #06171B;
        opacity: 1;
    }

    .page-head{
        background: #11262C;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 40px;
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
    }
</style>

@stack('custom_css')

<!-- END: Custom CSS-->