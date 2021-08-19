<script src="{{asset('assets/app-assets/vendors/js/vendors.min.js')}}"></script>

@stack('vendor_js')

@stack('page_vendor_js')

<script src="{{asset('assets/app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('assets/app-assets/js/core/app.js')}}"></script>
<script src="{{asset('assets/app-assets/js/scripts/components.js')}}"></script>

@stack('theme_js')

@stack('page_js')

<script type="text/javascript">      
    window.csrf_token = "{{ csrf_token() }}"
    window.url_asset = "{{asset('/')}}"
  </script>

@stack('custom_js')