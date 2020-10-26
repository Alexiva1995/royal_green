@extends('layouts.dashboard')

@section('content')
{{-- <style>
    .title-producto {
        border-bottom: 2px double #ffb103 !important;
        margin: 10px;
        padding: 5px;
        font-size: 14px;
        font-weight: 800;
        color: #ffb103;
    }
</style> --}}

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
            @if ($item->ID == 5618 || $item->ID == 5659)
                @if (Auth::user()->ID == 2 || Auth::user()->ID == 315 || Auth::user()->ID == 316 || Auth::user()->ID == 317 || Auth::user()->ID == 318)
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card text-center bg-transparent">
                        <div class="card-content">
                            <img src="{{$item->imagen}}" alt="{{$item->post_title}}" class="card-img img-fluid">
                            <div class="card-img-overlay d-flex justify-content-center align-items-end">
                            <a class="btn btn-info mt-1 text-white" onclick="detalles({{json_encode($item)}}, '{{$item->link->id}}', '{{$item->link->code}}')">Visualizar</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @else
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card text-center bg-transparent">
                    <div class="card-content">
                        <img src="{{$item->imagen}}" alt="{{$item->post_title}}" class="card-img img-fluid">
                        <div class="card-img-overlay d-flex justify-content-center align-items-end">
                        <a class="btn btn-info mt-1 text-white" onclick="detalles({{json_encode($item)}}, '{{$item->link->id}}', '{{$item->link->code}}')">Visualizar</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        </div>
    </div>
</div>

{{-- modales --}}
@include('tienda.modalCompra')
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
        $('#id_coinbase').val(id)
        $('#code_coinbase').val(code)
        $('#myModal1').modal('show')
    }

    function validarCupon() {
        let cupon = $('#cupon').val();
        let url = '{{route('tienda-verificar-cupon')}}'
        let token = '{{ csrf_token() }}'
        $.post(url, {'_token': token, 'cupon': cupon}).done(function(response){
            let data = JSON.parse(response)
            if (data.msj != '') {
                alert(data.msj)
            }else{
                $("#tipo1").val(data.tipo)
                $("#producto" + 1).val(data.paquete)
                $("#total" + 1).val(data.precio)
                $("#myModalLabel1").text('Cupon del Producto ' + data.paquete)
                $("#idproducto" + 1).val(data.idpaquete)
                $("#restante" + 1).val(0)
                $("#btn" + 1).text('Recibir Cupon')
                $("#cupon" + 1).val(data.cupon)
                $("#myModal" + 1).modal('show')
            }
        })
    }
</script>
@endsection