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
            <form method="GET" action="{{ route('tienda-solicitudes') }}">
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
            <h4>Activacion del usuario: {{$user->display_name}}</h4>
            <form action="{{route('tienda-activar-paquete')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="iduser" value="{{$user->ID}}">
                <div class="row">
                    <div class="form-group col-xs-12 col-md-6">
                        <label for="">Seleccion un Producto</label>
                        <select name="producto" id="" class="form-control">
                            <option value="" disabled selected>Seleccione una Opcion</option>
                            @foreach ($productos as $producto)
                                <option value="{{$producto->ID}}">{{$producto->post_title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-xs-12 col-md-6">
                        <label for="">Tipo de Activacion</label>
                        <select name="activacion" id="" class="form-control">
                            <option value="" disabled selected>Seleccione una Opcion</option>
                            <option value="Coinbase">Coinbase</option>
                            <option value="Manual">Patrocinado</option>
                        </select>
                    </div>
                    <div class="form-group col-12 text-center">
                        <button type="submit" class="btn btn-success">Activar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endif
@endsection