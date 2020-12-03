@extends('layouts.dashboard')

@section('content')
{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- formulario de fecha  --}}
@include('dashboard.componentView.formSearch', ['route' => 'buscarnetwork', 'name1' => 'fecha1', 'name2' => 'fecha2', 'text1' => 'Fecha Desde', 'text1' => 'Fecha Hasta', 'type' => 'date'])

<div class="card">
    <div class="card-content">
        <div class="card-body">
            <form method="POST" action="{{route('buscarnetworknivel')}}">
                <div class="row">
                    {{ csrf_field() }}
                <div class="col-12 col-sm-6 col-md-10">
                    <label class="control-label " style="text-align: center; margin-top:4px;">Nivel a Filtrar</label>
                    <select name="nivel" class="form-control">
						<option value="" disabled selected>Selecione una opcion</option>
						<option value="1">Nivel 1</option>
						<option value="2">Nivel 2</option>
						<option value="3">Nivel 3</option>
						<option value="4">Nivel 4</option>
						<option value="0">todos</option>
					</select>
                </div>
                <div class="col-12 text-center col-md-2" style="padding-left: 10px;">
                    <button class="btn btn-primary mt-2" type="submit" id="btn">Buscar</button>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="card">
	<div class="card-content">
		<div class="card-body">
			<div class="table-responsive">
				<table id="mytable" class="table zero-configuration">
					<thead>
						<tr class="text-center">
							<th>ID</th>
							{{-- <th>Nombre</th> --}}
							<th>Correo</th>
							<th>Paquete</th>
							<th>Estato</th>
							<th>Auspiciador</th>
							<th>Nivel de Referido</th>
							<th>Ingreso</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($allReferido as $referido)
						@php
						$paquete = null;
							$nombre = 'Sin Paquete';
							if ($referido->status == 1) {
								$paquete = json_decode($referido->paquete);
								if (!empty($paquete)) {
									$nombre = $paquete->nombre;
								} 
							}
						@endphp
						<tr class="text-center">
							<td>{{ $referido->ID }}</td>
							{{-- <td>{{ $referido->display_name }}</td> --}}
							<td>{{ $referido->user_email }}</td>
							<td>{{ $nombre }}</td>
							@if ($referido->status == 0)
							<td>Inactive</td>
							@else
							<td>Active</td>
							@endif
							<td>
								{{$referido->patrocinador}}
							</td>
							<td>
								{{$referido->nivel}}
							</td>
							<td>{{ date('d-m-Y', strtotime($referido->created_at)) }}</td>
						</tr>

						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


@endsection