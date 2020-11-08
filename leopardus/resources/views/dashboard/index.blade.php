@extends('layouts.dashboard')

@section('content')
<section id="dashboard-analytics">
		{{-- primeros cuadro --}}
		@include('dashboard.componenteIndex.first_square')
		{{-- secundo cuadro --}}
		@include('dashboard.componenteIndex.second_square')
		{{-- tecer cuadro --}}
		{{-- @include('dashboard.componenteIndex.third_square') --}}
</section>

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

	/**
	* Permite modificar el lado binario donde se van a ir registrando los usuarios
	*/
	function updateSideBinary(value) {
		let url = "{{route('change.side')}}"
		let valor = value
		let data = {
			ladoregistrar: valor,
			_token: "{{ csrf_token() }}",
		}
		let lado = (valor == 'D') ? 'Derecha' : 'Izquierda'
		$.post(url, data, function(response){
			if (response = 1) {
				Swal.fire({
				title: 'Lado Matrix Actualizado',
				text: "Su nuevo lado de registro binario es por la "+ lado,
				type: "success",
				confirmButtonClass: 'btn btn-primary',
				buttonsStyling: false,
			}).then((value) => {
				if (value) {
					window.location.reload()
				}
			})
			}else{
				Swal.fire({
				title: 'Error',
				text: "No se pudo actualizar el lado a registrar intente de nuevo",
				type: "danger",
				confirmButtonClass: 'btn btn-primary',
				buttonsStyling: false,
			}).then((value) => {
				if (value) {
					window.location.reload()
				}
			})
			}
		})
	}
</script>
@endpush

{{-- vendor css --}}
@push('vendor_css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/apexcharts.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/tether-theme-arrows.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/tether.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/shepherd-theme-default.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
<style>
	#tippy-1{
		display: none;
	}
</style>
@endpush

{{-- page css --}}
@push('page_css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/dashboard-analytics.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/card-analytics.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/tour/tour.css')}}">
@endpush

{{-- page vendor js --}}
@push('page_vendor_js')
<script src="{{asset('app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/tether.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/shepherd.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
@endpush

{{-- page js --}}
@push('page_js')
<script src="{{asset('app-assets/js/scripts/pages/dashboard-analytics.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/cards/card-statistics.js')}}"></script>
@endpush