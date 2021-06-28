@extends('layouts.dashboard')

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (Session::has('msj'))
<div class="alert alert-success">
    <strong>¡Felicitaciones!</strong> {{Session::get('msj')}}
</div>
@endif

@if (Session::has('msj2'))
<div class="alert alert-success">
    <button class="close" data-close="alert"></button>
    <span>
        {{Session::get('msj2')}}
    </span>
</div>
@endif

@if (Session::has('msj4'))
<div class="alert alert-info">
    <button class="close" data-close="alert"></button>
    <span>
        {{Session::get('msj4')}}
    </span>
</div>
@endif

@if (Session::has('msj3'))
<div class="alert alert-danger">
    <button class="close" data-close="alert"></button>
    <span>
        {{Session::get('msj3')}}
    </span>
</div>
@endif

<div class="card">
    <div class="card-content">
        <div class="card-body">
            <form method="POST" action="{{route('report_direct')}}">
                <div class="row">
                    {{ csrf_field() }}
                <div class="col-12 col-md-4">
                    <label class="control-label " style="text-align: center; margin-top:4px;">ID Usuario</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="number" autocomplete="off"
                        name="iduser" required style="background-color:f7f7f7;" />
                </div>
                <div class="col-12 col-md-4">
                    <label class="control-label " style="text-align: center; margin-top:4px;">Desde</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="date" autocomplete="off"
                        name="desde" required style="background-color:f7f7f7;" />
                </div>
                <div class="col-12 col-md-4">
                    <label class="control-label " style="text-align: center; margin-top:4px;">Hasta</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="date" autocomplete="off"
                        name="hasta" required style="background-color:f7f7f7;" />
                </div>
                <div class="col-12 text-center col-md-2" style="padding-left: 10px;">
                    <button class="btn btn-primary mt-2" type="submit" id="btn">Buscar</button>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if (!empty($data))
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4>Fecha de {{$data['desde']}} a {{$data['hasta']}}</h4>
            <div class="row">
                <div class="col-12 col-sm-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            ID Usuario
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <h5><b>{{$data['iduser']}}</b></h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            Nombre Usuario
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <h5><b>{{$data['name']}}</b></h5>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            Total Compras Directos
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <h5><b>{{$data['total']}}</b></h5>
                        </div>
                    </div>
                </div>
            </div>      
        </div>
    </div>
</div>
@endif
@endsection