<!-- BEGIN: Vendor JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
@stack('vendor_js')
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/tether.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/shepherd.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
@stack('page_vendor_js')
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('app-assets/js/core/app.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/components.js')}}"></script>
@stack('theme_js')
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
{{-- <script src="{{asset('app-assets/js/scripts/pages/dashboard-analytics.js')}}"></script> --}}
@stack('page_js')
<!-- END: Page JS-->

{{-- BEGIN: Custom JS --}}
@routes
@stack('custom_js')
<script>
    window.csrf_token = "{{ csrf_token() }}"
</script>
<script src="{{asset('assets/scripts/general.js')}}"></script>
<script>
    

</script>
{{-- END: Custom js --}}