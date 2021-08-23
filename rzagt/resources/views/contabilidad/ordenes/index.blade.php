@extends('layouts.dashboard')

@section('content')
{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')


{{-- alertas --}}
@include('dashboard.componentView.alert')

@if (Auth::user()->ID == 1)
{{-- formulario de fecha  --}}
<h5>Filtro por Rango de Fecha</h5>
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <form method="get" action="{{ route('new.contabilidad.ordnes.filter.date') }}">
                <div class="row">
                    {{-- {{ csrf_field() }} --}}
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="control-label " style="text-align: center; margin-top:4px;">Fecha Desde</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="date" autocomplete="off"
                        name="fecha1" required style="background-color:f7f7f7;" />
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="control-label " style="text-align: center; margin-top:4px;">Fecha Hasta</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="date" autocomplete="off"
                        name="fecha2" required style="background-color:f7f7f7;" />
                </div>
                <div class="col-12 text-center col-md-2" style="padding-left: 10px;">
                    <button class="btn btn-primary mt-2" type="submit" id="btn">Buscar</button>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- filtro por tipo de orden --}}
<h5>Filtro por tipo de orden</h5>
<div class="card">
	<div class="card-content">
		<div class="card-body">
			<form method="get" action="{{route('new.contabilidad.ordnes.filter.type')}}" id="formfilter">
				<div class="row">
					<input type="hidden" name="orden" id="filtroorden">
					<div class="col-12 col-md-4 text-center">
						<button class="btn btn-primary mt-2" type="button" onclick="filter('Manual')">Manual</button>
					</div>
					<div class="col-12 col-md-4 text-center">						
						<button class="btn btn-primary mt-2" type="button" onclick="filter('Saldo')">Comision por Activacion</button>
					</div>
					<div class="col-12 col-md-4 text-center">						
						<button class="btn btn-primary mt-2" type="button" onclick="filter('Coinbase')">Coinbase</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- filtro por tipo de orden --}}
<h5>Filtro por estado</h5>
<div class="card">
	<div class="card-content">
		<div class="card-body">
			<form method="get" action="{{route('new.contabilidad.ordnes.filter.status')}}" id="formfilterstatus">
				<div class="row">
					<input type="hidden" name="status" id="filtrostatus">
					<div class="col-12 col-md-6 text-center">
						<button class="btn btn-primary mt-2" type="button" onclick="filterstatus('En Espera')">En Espera</button>
					</div>
					<div class="col-12 col-md-6 text-center">						
						<button class="btn btn-primary mt-2" type="button" onclick="filterstatus('Completado')">Completados</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
{{-- por tipo de idusuario  --}}
<h5>Filtro por id de usuario</h5>
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <form method="get" action="{{ route('new.contabilidad.ordnes.filter.user') }}">
                <div class="row">
                    {{-- {{ csrf_field() }} --}}
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="control-label " style="text-align: center; margin-top:4px;">ID Usuario</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="integer" autocomplete="off"
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
@endif

@if (!empty($ordenes))
<div class="card">
    <div class="card-header">
        @if (!empty(request()->fecha1))
        <div class="card-title">
            Filtro por Fecha - (Desde: {{request()->fecha1}} - Hasta: {{request()->fecha2}}) - Pagina: {{(empty(request()->page)) ? 1 : request()->page}}
        </div>
        @endif
        @if (!empty(request()->iduser))
        <div class="card-title">
            Filtro por Usuario - ID Usuario: {{request()->iduser}} - Pagina: {{(empty(request()->page)) ? 1 : request()->page}}
        </div>
        @endif
        @if (!empty(request()->orden))
        <div class="card-title">
            Filtro por Tipo Orden - Tipo: {{request()->orden}} - Pagina: {{(empty(request()->page)) ? 1 : request()->page}}
        </div>
        @endif
		@if (!empty(request()->status))
        <div class="card-title">
            Filtro por Estado - Tipo: {{request()->status}} - Pagina: {{(empty(request()->page)) ? 1 : request()->page}}
        </div>
        @endif
    </div>
	<div class="card-content">
		<div class="card-body">
			<div class="table-responsive">
				<table id="mytable" class="table zero-configuration">
					<thead>
						<tr class="text-center">
							<th>Numero de Orden</th>
							<th>Usuario</th>
							@if (Auth::user()->ID == 1)
							<th>Correo</th>
							@endif
							<th>Fecha</th>
							<th>Concepto</th>
							<th>Total</th>
							<th>Estado</th>
							@if (Auth::user()->ID == 1)
							<th>Tipo Activacion</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach ($ordenes as $orden)
						<tr class="text-center">
							<th>{{$orden->idorden}}</th>
							<th>{{$orden->fullname}}</th>
							@if (Auth::user()->ID == 1)
							<th>{{$orden->email}}</th>
							@endif
							<th>{{$orden->fecha}}</th>
							<th>{{$orden->concepto}}</th>
							<th>{{$orden->price}}</th>
							<th>{{$orden->estado}}</th>
							@if (Auth::user()->ID == 1)
							<th>{{$orden->tipo_activacion}}</th>
							@endif
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="col-12 mt-2 text-center">
				{{$ordenes->appends(request()->except('page'))->links()}}
			</div>
		</div>
	</div>
</div>
@endif

<script>
	function filter(filter) {
		$('#filtroorden').val(filter)
		$("#formfilter").submit()
	}
	function filterstatus(filter) {
		$('#filtrostatus').val(filter)
		$("#formfilterstatus").submit()
	}
</script>

@endsection