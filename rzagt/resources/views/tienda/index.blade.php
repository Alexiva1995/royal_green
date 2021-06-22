@extends('layouts.dashboard')

@section('content')

{{-- alertas --}}
@include('dashboard.componentView.alert')

<div class="alert alert-info">
    <button class="close" data-close="alert"></button>
    <h4 style="color: white">
        Todas las compras realizadas con saldos tardan un par de minutos. Por favor no se salga de la página ni la actualice, esto podría ocasionar un error en la activación de su cuenta.
    </h4>
  </div>
  <div class="alert alert-info">
    <button class="close" data-close="alert"></button>
    <h4 style="color: white">
        Para poder realizar compra con las comisiones por favor contactese con nosotros.
    </h4>
  </div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Artículos de la Tienda</h4>
    </div>

    <div class="card-content">
        <div class="card-body">
            <div class="row">
            @foreach ($productos as $item)
            
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card text-center bg-transparent">
                    <div class="card-content">
                        <img src="{{$item->imagen}}" alt="{{$item->post_title}}" class="card-img img-fluid">
                        <div class="card-img d-flex justify-content-center align-items-end">
                        @if ($item->actualizar != 'No Disponible')
                            <a class="btn btn-info mt-1 text-white" onclick="detalles({{json_encode($item)}}, 0)">{{$item->actualizar}}</a>
                            @if (Auth::user()->activar_pay_comision == 1)
                                <a class="btn btn-info mt-1 text-white" onclick="detalles({{json_encode($item)}}, 1)">Pagar con <br> Comisiones Generadas</a>
                            @endif
                        @else
                            <button class="btn btn-info mt-1 text-white" disabled>{{$item->actualizar}}</button>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        </div>
    </div>
</div>

{{-- modales --}}
@include('tienda.modalCompra')
@include('tienda.modalRegister')
{{-- @include('tienda.modalCupon') --}}


@endsection