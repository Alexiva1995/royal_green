@extends('layouts.dashboard')

@section('content')

{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- alertas --}}
@include('dashboard.componentView.alert')

{{-- <div class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="table-r">
                <table id="mytable" class="table zero-configuration">
                    <thead>
                        <tr class="text-center">
                            <th class="text-center">
                                #
                            </th>
                            <th class="text-center">
                                Usuario
                            </th>
                            <th class="text-center">
                                Correo
                            </th>
                            <th class="text-center">
                                Estado del Pasivo
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista_usuarios as $idx=> $usuario)
                        <tr class="text-center">
                            <td>
                                {{ $idx + 1 }}
                            </td>
                            <td>
                                {{ $usuario->user_login }}
                            </td>
                            <td>
                                {{ $usuario->user_email }}
                            </td>
                            <td>
                                {{ $usuario->paquete }}
                            </td>
                            <td>
                                {{ $usuario->pay_rentabilidad }}
                            </td>
                            <td>
                                @if ($usuario->pay_rentabilidad == 1)
                                <a href="" class="btn btn-sm btn-danger">
                                    Desactivar
                                </a>
                                @else
                                <a href="" class="btn btn-sm btn-success">
                                    Activar
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> --}}

<div class="card">
	<div class="card-content">
		<div class="card-body">
			<div class="table-responsive">
				<table id="mytable" class="table zero-configuration">
					<thead>
						<tr class="color">
							<th class="center">#</th>
							<th class="center">Usuario</th>
							<th class="center">Email</th>
							<th class="center">Paquete</th>
							<th class="center">Acciones</th>
						</tr>
					</thead>
                    <tbody>
                        @foreach ($lista_usuarios as $idx =>  $user)
                        <tr>
                            <td>{{ ($idx + 1) }}</td>
                            <td>{{ $user->usuario }}</td>
                            <td>{{ $user->correo }}</td>
                            <td>{{ $user->paquete }}</td>
                            <td>
                                @if ($user->activo==1)
                                    <a href="{{ route('disable_renta.update',$user->id) }}" class="btn btn-small btn-danger" data-user="{{ $user->id }}">Desactivar</a>
                                @else
                                    <a href="{{ route('disable_renta.update',$user->id) }}" class="btn btn-small btn btn-info waves-effect waves-light" data-user="{{ $user->id }}">Activar</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
			</div>
		</div>
	</div>
</div>

@endsection
