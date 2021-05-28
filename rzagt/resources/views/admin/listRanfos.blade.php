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
                                Rango Actual
                            </th>
                            {{-- <th class="text-center">
                                Rango Siguiente
                            </th> --}}
						</tr>
					</thead>
					<tbody>
						@foreach($users as $user)
                        <tr class="text-center">
                            <td>
                                {{$user->ID}}
                            </td>
                            <td>
                                {{$user->display_name}}
                            </td>
                            <td>
                                {{$user->user_email}}
                            </td>
                            <td>
                                {{$user->rol_name}}
                            </td>
                            {{-- <td>
                                {{$user->rol_name_next}}
                            </td> --}}
                        </tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


@endsection