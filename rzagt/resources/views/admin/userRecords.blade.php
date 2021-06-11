@extends('layouts.dashboard')

@section('content')
{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- alertas --}}
@include('dashboard.componentView.alert')

<div class="card">
	<div class="card-content">
		<div class="card-body">
			<form action="{{route('admin.userrecords')}}" method="get">
				<div class="form-group">
					<label for="">Ingrese correo</label>
					<input type="text" class="form-control" required name="email">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-info">Buscar</button>
					@empty(!request()->email)
					<a href="{{route('admin.userrecords')}}" class="btn btn-danger">Regresar al Listado</a>
				@endempty
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
						<tr>
							<th class="text-center">
								ID
							</th>
							<th class="text-center">
								Usuario
							</th>
							<th class="text-center">
								Correo
							</th>
							<th class="text-center">
								Telefono
							</th>
							<th class="text-center">
								Pais
							</th>
							<th class="text-center">
								Referido por
							</th>
							{{-- <th class="text-center">
								Rango
							</th> --}}
							<th class="text-center">
								Binario
							</th>
							<th class="text-center">
								Estatus
							</th>
							<th class="text-center">
								Google Autenthic
							</th>
							<th class="text-center">
								Rentabilidad
							</th>
							<th class="text-center">
								Retiro
							</th>
							<th class="text-center">
								Accion
							</th>
						</tr>
					</thead>
					<tbody>
						@php
						$cont = "";
						@endphp
						@foreach($datos as $usuario)
						@php

						$falta = DB::table('user_campo')
						->where('ID', '=', $usuario['ID'])
						->get();

						$roles = DB::table('roles')->get();
						@endphp
						<tr>
							<td class="text-center">
								{{ $usuario['ID'] }}
							</td>
							<td class="text-center">
								{{ $usuario['display_name'] }}
							</td>
							<td class="text-center">
								{{ $usuario['user_email'] }}
							</td>
							<td class="text-center">
								{{ $usuario['phone'] }}
							</td>
							<td class="text-center">
								@foreach($falta as $fal)
								{{ $fal->pais }}
								@endforeach
							</td>
							<td class="text-center">
								{{ $usuario['nombre_referido'] }}
							</td>

							<td class="text-center">

								@if ($usuario['binario'] == 1)
								Activo
								@else
								Inactivo
								@endif

							</td>

							<td class="text-center">

								@if ($usuario['status'] == 1)
								Activo
								@else
								Inactivo
								@endif

							</td>
							<td class="text-center">
								@if ($usuario['2fact'] == 1)
								<a class="btn btn-info" href="{{ route('disable_2fact.update', $usuario['ID']) }}">
									Activar
									</a>
									@else
									<a class="btn btn-danger" href="{{ route('disable_2fact.update', $usuario['ID']) }}">
										Desactivar
									</a>
									@endif
							</td>
							<td class="text-center">
								@if ($usuario['renta'] == 0)
								<a class="btn btn-info" href="{{ route('disable_renta.update', $usuario['ID']) }}">
									Activar
									</a>
									@else
									<a class="btn btn-danger" href="{{ route('disable_renta.update', $usuario['ID']) }}">
										Desactivar
									</a>
									@endif
							</td>
							<td class="text-center">  
								@if ($usuario['retiro'] == 0)
								<a class="btn btn-info" href="{{ route('disable_retiro.update', [$usuario['ID'], false]) }}">
									Activar
									</a>
									@else
									<a class="btn btn-danger" href="{{ route('disable_retiro.update', [$usuario['ID'], false]) }}">
										Desactivar
									</a>
									@endif
							</td>
							<td class="text-center">
								<a class="btn btn-info" href="{{ route('admin.useredit', $usuario['ID']) }}">
									<i class="fa fa-edit"></i>
								</a>
								<a class="btn btn-success" href="{{ route('admin.reset-qr', $usuario['ID']) }}">
									<i class="fa fa-qrcode"></i>
								</a>
								<a class="btn btn-warning" onclick="eliminarProducto({{$usuario['ID']}}, {{$usuario['wallet']}})">
									<i class="fa fa-credit-card"></i>
								</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@empty(request()->email)
					{{$usuarios->links()}}
				@endempty
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalRetiro" tabindex="-1" role="dialog" aria-labelledby="modalRetiroLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalRetiroLabel">Retirar</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form action="{{ route('wallet.admin.retiro') }}" method="post">
					{{ csrf_field() }}
					<div class="row">
						<input class="form-control" type="hidden" name="userdelete" id="userdelete"/>
						<div class="col-12 col-sm-6">
						  <label for="">Seleccione un Metodo de Pago</label>
						  <input class="form-control" type="text" value="Wallet" readonly/>
						</div>
						<div class="form-group col-12 col-sm-6">
						  <label>Monto Disponible</label>
						  <input class="form-control" type="text" name="montodisponible" id="disponible" readonly/>
						</div>
						<div class="form-group col-12 col-sm-6">
						  <label id="">Comision por Retiro</label>
						  <input class="form-control" type="text" readonly value="4.5"/>
						  <input class="form-control" type="hidden" name="tipowallet" value="1"/>
						</div>
						<div class="form-group col-12 col-sm-6">
						  <label>Cantidad a Retirar</label>
						  <input class="form-control" type="number" name="monto" step="any" required onkeyup="totalRetiro(this.value)"/>
						  {{-- <input id="total" type="hidden" class="form-control" name="total" readonly/> --}}
						</div>
						<div class="form-group col-12 col-sm-6">
							<label>Monto minimo a Retirar</label>
							<input id="monto_min" class="form-control" name="monto_min" value="50" readonly/>
						  </div>
						<div class="form-group col-12 col-sm-6">
						  <label>Monto final a Retirar</label>
						  <input id="total" class="form-control" name="total" readonly/>
						</div>
						<div class="form-group col-12">
						  <label>Wallet de la cuenta asociada al metodo de pago</label>
						  <input type="text" class="form-control" name="wallet" required/>
						</div>
			
							<div class="form-group col-12 text-center">
								<button type="submit" class="btn btn-success">Retirar</button>
							</div>
					  </div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

@endsection