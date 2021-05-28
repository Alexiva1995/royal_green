@extends('layouts.dashboard')

@section('content')
{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

<div class="card">
    <div class="card-content">
        <div class="card-body">
            <form method="POST" action="{{ route('price-filtro') }}">
                <div class="row">
                    {{ csrf_field() }}
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="control-label " style="text-align: center; margin-top:4px;">Fecha Desde</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="date" autocomplete="off"
                        name="desde" required style="background-color:f7f7f7;" />
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="control-label " style="text-align: center; margin-top:4px;">Fecha Hasta</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="date" autocomplete="off"
                        name="hasta" required style="background-color:f7f7f7;" />
                </div>
				<input type="hidden" name="tipo" value="{{$tipo}}">
                <div class="col-12 text-center col-md-2" style="padding-left: 10px;">
                    <button class="btn btn-primary mt-2" type="submit" id="btn">Buscar</button>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- alertas --}}
@include('dashboard.componentView.alert')

{{-- fecha --}}
@if (!empty($fechas['desde']) && !empty($fechas['desde']))
<div class="card">
	<div class="card-content">
		<div class="card-body">
			<div class="row">
				<div class="form-group col-12 col-md-6">
					<label>Date From</label>
					<h5>{{ date('d-m-Y', strtotime($fechas['desde'])) }}</h5>
				</div>
				<div class="form-group col-12 col-md-6">
					<label>Date To</label>
					<h5>{{date('d-m-Y', strtotime($fechas['hasta']))}}</h5>
				</div>
			</div>
		</div>
	</div>
</div>
@endif

@php
$total = 0;
@endphp
<div class="card">
	<div class="card-content">
		<div class="card-body">
			<div class="table-responsive">
				<table id="mytable" class="table zero-configuration">
					<thead>
						<tr class="text-center">
							<th>
								#
							</th>
							<th>
								Usuario
							</th>
							<th>
								Correo
							</th>
							<th>
								Monto
							</th>
							<th>
								Fecha
							</th>
							<th>
								Billetera
							</th>
							<th>
								Estado
							</th>
							<th>
								Accion
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach($pagos as $pago)
						@php
						$total = ($total + $pago->monto);
						@endphp
						<tr class="text-center">
							<td>
								{{$pago->id}}
							</td>
							<td>
								{{$pago->username}}
							</td>
							<td>
								{{$pago->email}}
							</td>
							<td>
								
									@if ($moneda->mostrar_a_d)
									{{$moneda->simbolo}} {{number_format($pago->monto, 2, ',', '.')}}
									@else
									{{number_format($pago->monto, 2, ',', '.')}} {{$moneda->simbolo}}
									@endif
								
							</td>
							<td>
								{{$pago->fechasoli}}
							</td>
							<td>
								{{$pago->tipopago}}
							</td>
							<td>
								@if ($pago->estado == 0)
								Pendiente
								@endif
							</td>
							<td>
								
									<a class="btn btn-info" href="{{route('price-aprobar', [$pago->id])}}">
										<i class="fa fa-check"></i></a>
									<a class="btn btn-danger" href="{{route('price-rechazar', [$pago->id])}}">
										<i class="fa fa-times"></i></a>
								
							</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<td colspan="3"> Total:</td>
						<td colspan="5"> {{$total}}</td>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection