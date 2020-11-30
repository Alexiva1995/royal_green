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
@stack('custom_js')

<script>
    function copyToClipboard(element) {
		var aux = document.createElement("input");
		aux.setAttribute("value", document.getElementById(element).innerHTML.replace('&amp;', '&').trim());
		document.body.appendChild(aux);
		aux.select();
		document.execCommand("copy");
		document.body.removeChild(aux);
		Swal.fire({
			title: '¡Link Copiado!',
			text: "Su link de referido esta listo para pegar",
      		type: "success",
			confirmButtonClass: 'btn btn-primary',
			buttonsStyling: false,
		})
	}
</script>
{{-- END: Custom js --}}