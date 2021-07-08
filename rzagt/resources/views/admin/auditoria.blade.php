@extends('layouts.dashboard')

@section('content')
{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- alertas --}}
@include('dashboard.componentView.alert')

<div class="card">
	<div class="card-content">
		<div class="card-body">
			<div class="table-responsive">
				<table id="mytable" class="table zero-configuration">
					<thead>
						<tr>
							<th>ID Usuario</th>
                            <th>Usuario</th>
                            <th>Campo Modificado</th>
                            <th>Valor Anterior</th>
                            <th>Valor Nuevo</th>
                            <th>Usuario Que Realizo el cambio</th>
                            {{-- <td></td> --}}
						</tr>
					</thead>
					<tbody>			
						@foreach($auditorias as $auditoria)
						<tr>
							<td>{{$auditoria->iduser}}</td>
                            <td>{{$auditoria->nombre}}</td>
                            <td>{{$auditoria->campo}}</td>
                            <td>{{$auditoria->valor_old}}</td>
                            <td>{{$auditoria->valor_new}}</td>
                            <td>{{$auditoria->user_change}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>

			</div>
		</div>
	</div>
</div>


@endsection