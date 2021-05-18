@extends('layouts.dashboard')

@section('content')
{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- alertas --}}
@include('dashboard.componentView.alert')


@if (Auth::user()->ID == 1)
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <form method="GET" action="{{ route('removerbilletera.index') }}">
                <div class="row">
                    {{ csrf_field() }}
                <div class="col-12 col-sm-6 col-md-10">
                    <label class="control-label " style="text-align: center; margin-top:4px;">ID Usuario</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="number" autocomplete="off"
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

@if ($user != null)
@if ($user == 'Usuario no encontrado')
<div class="alert alert-warning">
	<strong>{{$user}}</strong>
</div>
@else
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4>Billetera del usuario: {{$user->display_name}}</h4>
            <div class="table-responsive">
                <table id="mytable" class="table zero-configuration">
                    <thead>
                        <tr>
                            <th class="text-center">
                                #
                            </th>
                            <th class="text-center">
                                Fecha
                            </th>
                            {{-- <th class="text-center">
                                Usuario
                            </th> --}}
                            <th class="text-center">
                                Email Referido
                            </th>
                            
                            <th class="text-center">
                                Descripción
                            </th>
                            <th class="text-center">
                                Cash $
                            </th>
                            <th class="text-center">
                                Eliminar
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($billetera as $wallet)
                        <tr>
                            <td class="text-center">
                                {{$wallet->id}}
                            </td>
                            <td class="text-center">
                                {{date('Y-m-d', strtotime($wallet->created_at))}}
                            </td>
                            {{-- <td class="text-center">
                                {{$wallet->usuario}}
                            </td> --}}
                            <td class="text-center">
                                {{$wallet->email}}
                            </td>
                            <td class="text-center">
                                {{$wallet->descripcion}}
                            </td>
                            <td class="text-center">
                                {{number_format($wallet->debito, 2, '.', ',')}}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger" onclick="remover({{$wallet->id}}, {{$total}}, {{$wallet->debito}})">
                                    <i class="feather icon-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
@endif

<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Eliminar Transacion</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form class="" action="{{route ('removerbilletera.remover')}}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <h4>ID Transacion: <strong id="idtransh"></strong></h4>
            <h4>Total Disponible: <strong>{{$total}}</strong></h4>
            <h4>Restante: <strong id="restante"></strong></h4>
            <h4>Nuevo Disponible: <strong id="newdispo"></strong></h4>
            <input type="hidden" name="idtransacion" id="idtransi">
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-block">Eliminar</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
        </div>
      </div>
    </div>
</div>


@endsection