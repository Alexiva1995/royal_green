@extends('layouts.dashboard')

@section('content')

{{-- alertas --}}
@include('dashboard.componentView.alert')

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
                        {{-- @if ($item->meta_value > 0) --}}
                            <a class="btn btn-info mt-1 text-white" onclick="detalles({{json_encode($item)}}, 0)">{{$item->actualizar}}</a>
                            <a class="btn btn-info mt-1 text-white" onclick="detalles({{json_encode($item)}}, 1)">Pagar Con Saldo</a>
                        {{-- @else
                            <button class="btn btn-info mt-1 text-white" disabled>{{$item->actualizar}}</button>
                        @endif --}}
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