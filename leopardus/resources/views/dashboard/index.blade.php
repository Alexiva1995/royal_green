@extends('layouts.dashboard')

@section('content')
<div class="contai2">
	<div class="row">
		{{-- primeros cuadro --}}
		@include('dashboard.componenteIndex.first_square')
		{{-- secundo cuadro --}}
		{{-- @include('dashboard.componenteIndex.second_square') --}}
		{{-- tecer cuadro --}}
		@include('dashboard.componenteIndex.third_square')
	</div>
</div>

@endsection
@push('custom_js')
<script src="{{asset('assets/scripts/graficas.js')}}"></script>
<script type="text/javascript">
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
@endpush

@push('vendor_css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/apexcharts.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endpush

@push('page_vendor_js')
<script src="{{asset('app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
@endpush