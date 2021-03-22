@extends('layouts.app')

@section('content')
<style>
	.nombre {
		margin: -22px 5px 0px;
		background: #1199c4;
		color: #ffffff;
		padding: 3px 3px;
	}

	a {
		position: relative;
	}

	a img:hover+.inforuser {
		transform: translateY(0px);
	}

	.inforuser {
		width: 200px;
		background: white;
		position: absolute;
		top: -150px;
		left: -60px;
		z-index: 9996;
		padding: 10px;
		border: 2px solid #887722;
		box-shadow: 4px 4px 8px 5px;
		transition: 0.8s all;
		transform: translateY(-1000px);
	}

	.inforuser h3 {
		margin-top: 0;
		background: #887722;
	}

	.inforuser h3 img {
		margin: 10px;
		width: 35px;
	}

	.inforuser h5 {
		padding: 0 10px;
	}

	.inforuser h5 b.rol {
		border-radius: 50px;
		padding: 2px 16px;
		background: blueviolet;
		color: #fff;
	}
</style>
<div class="col-md-10">
	<div class="tree" style="margin-left:0%; width:250%;" scroll="auto">
		<ul>
			<li>
				<!-- NODO PRINCIPAL -->
				<a onclick="" href="#">
					<img title="{{ ucwords($referidoBase['nombre']) }}" src="{{ asset('assets/img/') }}/{{ $referidoBase['picture'] }}"
					 style="width:35px">
					<div class="inforuser">
						<h3><img title="{{ ucwords($referidoBase['nombre']) }}" src="{{ asset('avatar') }}/{{ $referidoBase['avatar'] }}"></h3>
						<h4>{{ ucwords($referidoBase['nombre']) }}</h4>
						<h5><span>Ingreso</span>: <b>{{ date('d-m-Y', strtotime($referidoBase['fechaingreso'])) }}</b></h5>
						<h5><b class="rol">{{ $referidoBase['rol'] }}</b></h5>
					</div>
					{{-- <h5 class="nombre">{{ ucwords($referidoBase['nombre']) }}</h5> --}}
				</a>
				{{-- nivel 1 --}}
				<ul>
					@foreach ($referidosAll as $item)
					@if ($item['subreferido'] == 0)
						@if($item['idpatrocinador'] == $referidoBase['ID'] && $item['nivel'] == 1)
						<li>
							<a href="{{ route('moretablero', ['id' => $item['ID'], 'tablero' => $tablero]) }}">
								<img title="{{ ucwords($item['nombre']) }}" src="{{ asset('assets/img/') }}/{{ $item['picture'] }}" style="width:35px">
								<div class="inforuser">
									<h3><img title="{{ ucwords($item['nombre']) }}" src="{{ asset('avatar') }}/{{ $item['avatar'] }}"></h3>
									<h4>{{ ucwords($item['nombre']) }}</h4>
									<h5><span>Ingreso</span>: <b>{{ date('d-m-Y', strtotime($item['fechaingreso'])) }}</b></h5>
									<h5><b class="rol">{{ $item['rol'] }}</b></h5>
								</div>
								{{-- <h5 class="nombre">{{ ucwords($item['nombre']) }}</h5> --}}
							</a>
						</li>
						@endif
					@else
					@if($item['idpatrocinador'] == $referidoBase['ID'] && $item['nivel'] == 1)
					<li>
						<a href="{{ route('moretablero', ['id' => $item['ID'], 'tablero' => $tablero]) }}" class="del">
							<img title="{{ ucwords($item['nombre']) }}" src="{{ asset('assets/img/') }}/{{ $item['picture'] }}" style="width:35px">
							<div class="inforuser">
								<h3><img title="{{ ucwords($item['nombre']) }}" src="{{ asset('avatar') }}/{{ $item['avatar'] }}"></h3>
								<h4>{{ ucwords($item['nombre']) }}</h4>
								<h5><span>Ingreso</span>: <b>{{ date('d-m-Y', strtotime($item['fechaingreso'])) }}</b></h5>
								<h5><b class="rol">{{ $item['rol'] }}</b></h5>
							</div>
							{{-- <h5 class="nombre">{{ ucwords($item['nombre']) }}</h5> --}}
							{{-- nivel2 --}}
							<ul class="nivel2">
								@foreach ($referidosAll as $elemento)
								@if($elemento['subreferido'] == 0)
									@if($elemento['idpatrocinador'] == $item['ID'] && $elemento['nivel'] == 2)
									<li>
										<a href="{{ route('moretablero', ['id' => $elemento['ID'], 'tablero' => $tablero]) }}" class="">
											<img title="{{ ucwords($elemento['nombre']) }}" src="{{ asset('assets/img/') }}/{{ $elemento['picture'] }}"
											style="width:35px">
											<div class="inforuser">
												<h3><img title="{{ ucwords($elemento['nombre']) }}" src="{{ asset('avatar') }}/{{ $elemento['avatar'] }}"></h3>
												<h4>{{ ucwords($elemento['nombre']) }}</h4>
												<h5><span>Ingreso</span>: <b>{{ date('d-m-Y', strtotime($elemento['fechaingreso'])) }}</b></h5>
												<h5><b class="rol">{{ $elemento['rol'] }}</b></h5>
											</div>
											{{-- <h5 class="nombre">{{ ucwords($elemento['nombre']) }}</h5> --}}
										</a>
									</li> 
									@endif
								@else
								@if($elemento['idpatrocinador'] == $item['ID'] && $elemento['nivel'] == 2)
								<li>
									<a href="{{ route('moretablero', ['id' => $elemento['ID'], 'tablero' => $tablero]) }}" class="del2">
										<img title="{{ ucwords($elemento['nombre']) }}" src="{{ asset('assets/img/') }}/{{$elemento['picture'] }}"
										 style="width:35px">
										<div class="inforuser">
											<h3><img title="{{ ucwords($elemento['nombre']) }}" src="{{ asset('avatar') }}/{{ $elemento['avatar'] }}"></h3>
											<h4>{{ ucwords($elemento['nombre']) }}</h4>
											<h5><span>Ingreso</span>: <b>{{ date('d-m-Y', strtotime($elemento['fechaingreso'])) }}</b></h5>
											<h5><b class="rol">{{ $elemento['rol'] }}</b></h5>
										</div>
										{{-- <h5 class="nombre">{{ ucwords($elemento['nombre']) }}</h5> --}}
										{{-- nivel 3 --}}
										<ul class="nivel3">
											@foreach ($referidosAll as $elemento2)
											@if($elemento2['idpatrocinador'] == $elemento['ID'] && $elemento2['nivel'] == 3) 
											<li>
												<a href="{{ route('moretablero', ['id' => $elemento2['ID'], 'tablero' => $tablero]) }}">
													<img title="{{ ucwords($elemento2['nombre']) }}" src="{{ asset('assets/img/') }}/{{ $elemento2['picture'] }}"
													 style="width:35px">
													<div class="inforuser">
														<h3><img title="{{ ucwords($elemento2['nombre']) }}" src="{{ asset('avatar') }}/{{ $elemento2['avatar'] }}"></h3>
														<h4>{{ ucwords($elemento2['nombre']) }}</h4>
														<h5><span>Ingreso</span>: <b>{{ date('d-m-Y', strtotime($elemento2['fechaingreso'])) }}</b></h5>
														<h5><b class="rol">{{ ucwords($elemento2['rol']) }}</b></h5>
													</div>
													{{-- <h5 class="nombre">{{ ucwords($elemento2['nombre']) }}</h5> --}}
												</a>
											</li>
											@endif
											@endforeach
										</ul>
									</a>
								</li>
								@endif
								@endif
								@endforeach
							</ul>
						</a>
					</li>
					@endif
					@endif
					@endforeach
				</ul>
			</li>
		</ul>
	</div>
</div>

<div class="col-md-2">
	@if ($principal == 'NO')
	<center> <a href="{{ route('tablero', [$tablero])}}">Regresar a mi árbol</a></center>
	@endif
</div>

<script>
	$(document).ready(function(){
		$(".nivel2 .del").remove()
		$(".nivel3 .del2").remove()
	})
</script>
@endsection