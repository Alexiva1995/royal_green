@extends('layouts.dashboard')

@section('content')
	<script>
		$(document).ready( function () {
			$('#mytable').DataTable( {
			    dom: 'lBrtip',
			    buttons: [
					'csv', 'pdf', 'print', 'excel'
			    ]
			} );
		});
	</script>

	@if (Session::has('msj'))
        <div class="alert alert-success">
            <strong>{{Session::get('msj')}}</strong>
        </div>
    @endif

	<table id="mytable" class="table table-bordered table-hover table-responsive">
		<thead>
			<tr>
				<th><center>#</center></th>
				<th><center>Monto</center></th>
				<th><center>Fecha</center></th>
			</tr>
		</thead>
		<tbody>
			@php
				$cont = 0;
			@endphp
			@foreach($transferencias as $transferencia)
				@php
					$cont++;
				@endphp
				<tr>
					<td><center>{{ $cont }}</center></td>
					<td><center>$ {{ $transferencia->amount }}</center></td>
					<td><center>{{ date('d-m-Y', strtotime($transferencia->date)) }}</center></td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection