@extends('layouts.dashboard')

@section('content')
<section id="dashboard-analytics">
    {{-- formulario de fecha  --}}
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.subdashboard') }}">
                    <div class="row">
                        {{ csrf_field() }}
                    <div class="col-12 col-sm-6 col-md-10">
                        <label class="control-label " style="text-align: center; margin-top:4px;">ID Usuarios</label>
                        <input class="form-control form-control-solid placeholder-no-fix" type="number" autocomplete="off"
                            name="iduser" required style="background-color:f7f7f7;" />
                    </div>
                    <div class="col-12 text-center col-md-2" style="padding-left: 10px;">
                        <button class="btn btn-primary mt-2" type="submit" id="btn">Buscar</button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

	@include('dashboard.componentView.alert')
	@if ($iduser != 1)
    <div class="row">
		{{-- primeros cuadro --}}
		@include('dashboard.componenteIndex.first_square')
		{{-- secundo cuadro --}}
		@include('dashboard.componenteIndex.second_square')
	</div>
    @endif
		
</section>

@endsection
@push('custom_js')
<script src="{{asset('assets/scripts/graficas.js')}}"></script>

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

	.progress-bar-info .progress-bar{
		background: #01e4f9 !important;
	}

	.rotate-progress{
		height: 70px !important;
		width: 70px !important;
		border-radius: 50% !important;
		transform: rotate(180deg);
		background: #ffffff !important;
	}

	.no_tocar::before{
		content: '';
		background: transparent;
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 5;
	}

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

	.carrusel_rango .slick-list.draggable{
		padding-top: 20px !important;
		padding-bottom: 20px !important;
		transition: all 0.8s;
	}

	.carrusel_rango .text-center.slick-slide.slick-current.slick-active.slick-center{
		transform: scale(1.2);
		transition: all 0.8s;
		box-shadow: 0px 0px 40px 70px #11262c;
	}

</style>
@endpush

{{-- page css --}}
@push('page_css')
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
@endpush


{{-- page js --}}
@push('page_js')
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
@endpush