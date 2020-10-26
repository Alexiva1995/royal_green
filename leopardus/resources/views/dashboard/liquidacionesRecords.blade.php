@extends('layouts.app')

@section('content')
    @if(Auth::user()->ID==1)
        <a href="{{url('admin/generarliquidaciones')}}" class="btn btn-primary">Liquidate now</a>

        @if($total>0)
            <a href="{{url('admin/liquidar_todo')}}" class="btn btn-success">Approve all settlements</a>
		@endif
    @endif

    <script>
		$(document).ready( function () {
			$('#mytable').DataTable( {
			    dom: 'flBrtip',
			    buttons: [
					'csv', 'pdf', 'print', 'excel'
			    ]
			} );
		});
	</script>
    @if(Session::has('flash_message'))
        <div class="alert alert-success" style="margin-top: 10px;">
            <button class="close" data-close="alert"></button>
            <span>
               {{Session::get('flash_message')}}
            </span>
        </div>
    @endif
    <style>
       .sorting_asc, .sorting{
            background: red !important;
        }
    </style>

    <div style="margin-top: 20px;">
	<table id="mytable" class="table table-bordered table-hover table-responsive">
		<thead>
			<tr>
				<th><center>Nameuser</center></th>
				<th><center>Date</center></th>
				<th><center>Total Commission</center></th>
				<th><center>State</center></th>
				@if(Auth::user()->ID==1)
				<th><center>Option</center></th>
				@endif
			</tr>
		</thead>
		<tbody>
		    @if(count($liquidaciones)>0)
		    @foreach($liquidaciones as $liquidacion)
				<tr>
					<td><center>
					       {{ ($liquidacion->username) }}
					     </center></td>
					<td><center>{{ date('d-m-Y', strtotime($liquidacion->fecha)) }}</center></td>
					<td><center>{{ ($liquidacion->comision) }} $</center></td>
					<td>@if ($liquidacion->estado == '0')
							<center>On holding</center>
						@elseif ($liquidacion->estado == '2')
							<center>Cancelled</center>
						@else
						    <center>Aproved</center>
						@endif
					</td>
					@if(Auth::user()->ID==1)
					<td>
					    @if ($liquidacion->estado == '0')
					        <center>
					            <button class="btn btn-primary" onclick="liquidar({{$liquidacion->ID}}, 1)"> <i class="fas fa-check"></i> </button>
                      <button class="btn btn-danger" onclick="liquidar({{$liquidacion->ID}}, 2)"> <i class="fas fa-trash"></i> </button>
					        </center>
					    @endif
					</td>
					@endif
				</tr>
			@endforeach
			@endif
		</tbody>
	</table>
	</div>

<script type="text/javascript">
	function liquidar(ID, estado) {
		var  datos={"ID":ID, "estado":estado, "_token":'{{ csrf_token() }}'};
		$.ajax({
		  type: 'POST',
		  url: '{{url('admin/liquidacion_estatus')}}',
		  data: datos,
		  dataType: "json",
		  complete:function(data){
		       location.reload().delay(5000);
		  },
		  success: function (response) { },
		  error: function (error) {
		    console.log(error);
			}
		});

	}


	function modal_liquidacion(ID) {
		$.ajax({
		  url: '{{url('admin/liquidacion_modal')}}/'+ID,
		  dataType: "json",
		  complete:function(data){
		      $('#modal-content').html(data.responseText);
		       $('#ModalLiquidacion').modal('toggle');


		  },
		  success: function (response) { },
		  error: function (error) {
		    console.log(error);
			}
		});

	}
</script>


<div class="modal fade" id="ModalLiquidacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="modal-content">

    </div>
  </div>
</div>
@endsection
