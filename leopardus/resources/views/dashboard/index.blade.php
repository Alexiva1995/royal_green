@extends('layouts.dashboard')

@section('content')
<section id="dashboard-analytics">
	<div class="row">
		{{-- primeros cuadro --}}
		@include('dashboard.componenteIndex.first_square')
		{{-- secundo cuadro --}}
		@include('dashboard.componenteIndex.second_square')
	</div>
		
		{{-- tecer cuadro --}}
		{{-- @include('dashboard.componenteIndex.third_square') --}}
</section>

@endsection
@push('custom_js')
<script src="{{asset('assets/scripts/graficas.js')}}"></script>
<script type="text/javascript">

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
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/css-circular-prog-bar.css')}}">
<style>
	#tippy-1{
		display: none;
	}

	.progress-circular{
		background:#11504d;
		color: #ffffff;
		height: 60px;
		width: 60px;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 50%;
		margin: auto;
		position: absolute;
		left: 5px;
		transform: rotate(180deg);
		font-size: 1.2rem;
	}

	.rotate-progress{
		height: 70px !important;
		width: 70px !important;
		border-radius: 50% !important;
		transform: rotate(180deg);
		background: #ffffff !important;
	}

	/* .progress-circular::before{
		content: ''
	} */

	.progress-bar-primary{
		background: #17967a !important;
		padding: 4px;
	}

	.progress.progress-xl{
		height: 1.3rem !important;
	}

	.progress-bar-primary .progress-bar{
		background: #12edd0 !important;
	}

	.card-green-alt{
		background: #11262c !important;
		border: 2px solid #0f2228 !important;
	}

	.color-green-alt{
		color: #5eeabd;
	}

	.color-red-alt{
		color: #e80332;
	}

	.table-index.table th, .table td{
		border-bottom: 1px solid #ffffff !important;
		border-top: 0px !important;
	}

	.slick-list.draggable{
		padding-top: 20px !important;
		padding-bottom: 20px !important;
		transition: all 0.8s;
	}

	.text-center.slick-slide.slick-current.slick-active.slick-center{
		transform: scale(1.2);
		transition: all 0.8s;
		box-shadow: 0px 0px 40px 70px #11262c;
	}

</style>
@endpush

{{-- page css --}}
@push('page_css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/dashboard-analytics.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/card-analytics.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/tour/tour.css')}}">
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
@endpush

{{-- page vendor js --}}
@push('page_vendor_js')
<script src="{{asset('app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/tether.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/shepherd.min.js')}}"></script>
@endpush

{{-- page js --}}
@push('page_js')
<script src="{{asset('app-assets/js/scripts/pages/dashboard-analytics.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/cards/card-statistics.js')}}"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
@endpush