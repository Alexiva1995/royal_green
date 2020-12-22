@extends('layouts.dashboard')

@section('content')
{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- formulario de fecha  --}}
@include('dashboard.componentView.formSearch', ['route' => 'buscarnetworkorder', 'name1' => 'fecha1', 'name2' => 'fecha2', 'text1' => 'Fecha Desde', 'text1' => 'Fecha Hasta', 'type' => 'date'])
{{-- formulario simple --}}
@if (Auth::user()->ID == 1)
<div class="card">

    <div class="card-content">
        <div class="card-body">
            <form method="POST" action="{{route('networkorders_filtre')}}">
                <div class="row">
                    {{ csrf_field() }}
                <div class="col-12 col-sm-6 col-md-10">
                    <label class="control-label " style="text-align: center; margin-top:4px;">Seleccione el tipo de Activacion</label>
                    <select name="filtro" id="" class="form-control">
						<option value="" disabled selected>Seleccione una opcion</option>
						<option value="Manual">Manual</option>
						<option value="Coinbase">Coinbase</option>
					</select>
                </div>
                <div class="col-12 text-center col-md-2" style="padding-left: 10px;">
                    <button class="btn btn-primary mt-2" type="submit" id="btn">Filtrar</button>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="card">
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
							@if (Auth::user()->ID != 1)
							<th>Generacion</th>
							@endif
							<th>Estado</th>
							@if (Auth::user()->ID == 1)
							<th>Tipo Activacion</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach ($compras as $compra)
							<tr class="text-center">
								<th>{{$compra['idorden']}}</th>
								<th>{{$compra['nombreusuario']}}</th>
								@if (Auth::user()->ID == 1)
								<th>{{$compra['correouser']}}</th>
								@endif
								<th>{{date('Y-m-d', strtotime($compra['fechacompra']))}}</th>
								<th>{{$compra['producto']}}</th>
								<th>{{$compra['total']}}</th>
								@if (Auth::user()->ID != 1)
								<th>{{$compra['nivel']}}</th>
								@endif
								<th>{{$compra['estado']}}</th>
								@if (Auth::user()->ID == 1)
								<th>{{$compra['activacion']}}</th>
								@endif
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection