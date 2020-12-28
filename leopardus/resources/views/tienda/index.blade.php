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
                        <div class="card-img-overlay d-flex justify-content-center align-items-end">
                        @if ($item->meta_value > 0)
                            <a class="btn btn-info mt-1 text-white" onclick="detalles({{json_encode($item)}})">Comprar</a>
                        @else
                            <button class="btn btn-info mt-1 text-white" disabled>Comprar</button>
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

<script>
    function detalles(product, id, code) {
        $('#idproducto').val(product.ID)
        $('#img').attr('src',product.imagen)
        $('#title').html(product.post_title)
        $('#title2').val(product.post_title)
        $('#content').html(product.post_content)
        $('#price').html('$ '+product.meta_value)
        $('#price2').val(product.meta_value)
        $('#pagarcompra').click()
        // $('#myModal1').modal('show')
    }
</script>
@if (session('msj'))
    <div class="alert alert-success">
        {{ session('msj') }}
    </div>
@endif

@endsection