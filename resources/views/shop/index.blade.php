@extends('layouts.dashboard')

@push('vendor_css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
@endpush

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/app-assets/css/pages/custom-shop.css')}}">
@endpush

@push('page_vendor_js')
<script src="{{asset('assets/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
<script src="{{asset('assets/app-assets/vendors/js/extensions/polyfill.min.js')}}"></script>
@endpush


@section('content')
<div id="adminServices">
    <div class="col-12">
        <div class="card" style="background:#11262C">
            <div class="card-content">
                <div class="card-body card-dashboard">
                   <h1 class="text-white">Lista de Paquetes</h1>
                    <div class="row">
                        @foreach ($packages as $items)
                            <div class="col col-md-4">
                                <div class="card text-center" style="background:#11262C">
                                    <div class="card-body">
                                        <div class="card-header d-flex align-items-center" style="background: #173138;">
                                            <img class="mb-1" src="{{$items->img()}}" alt="" style="width: 100%; heigh:100%;">
                                        </div>
                                        <form action="{{route('shop.procces')}}" method="POST" target="_blank" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="idproduct" value="{{$items->id}}">
                                        <button class="btn btn-block btn-outline-primary text-white rounded" type="submit" @if($invertido >= $items->price) disabled @endif>
                                            @if($invertido == null)
                                                Comprar
                                            @else
                                                Upgrade
                                            @endif
                                        </button>
                                        </form>
                                    </div>
                                </div>
                            </div>  
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="adminServices" class="d-none">
    <div class="card" style="background:transparent;">
        <div class="card-content">
            <div class="card-body card-dashboard">
                <h3 class="text-white">Articulos de la tienda</h3>
                <p class="text-white">Desliza para seleccionar</p>
                <div class="row justify-content-between align-items-center">
                    <div class="text-left">
                        <img class="m-2" id="imagePackage" src="{{asset('assets/img/royal_green/logos/logo.svg')}}" alt="" style="width: 150px; heigh:auto;">
                    </div>
                    <form class="text-right mr-3" action="{{route('shop.procces')}}" method="POST" target="_blank" class="d-inline">
                        @csrf
                        <input type="hidden" name="idproduct" id="idProduct">
                        <button class="btn text-white btn-outline-light rounded" type="submit" @if($invertido >= $items->price) disabled @endif>
                            @if($invertido == null)
                                Comprar
                            @else
                                Actualizar
                            @endif
                        </button>
                    </form>
                </div>
                <div class="row col-12 justify-content-center mt-2">
                        <input class="inputrange" id="inputrange" list="packages" type="range" min="1" max="{{count($packages)}}" step="1" value="1">
                    <datalist id="packages">
                        @foreach ($packages as $items)
                        <option value="{{$items->id}}">
                        @endforeach
                    </datalist>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        let inputrange = document.querySelector("#inputrange");
        let idProduct = document.querySelector("#idProduct");
        let imagePackage = document.querySelector("#imagePackage");
        inputrange.addEventListener("change", myScript);
        let src = '';
        
        function myScript(){
            idProduct.value = inputrange.value;
            src = window.url_asset+'assets/img/royal_green/paquetes/'+idProduct.value +'.png';
            imagePackage.src = src;
            console.log(src);
        }
    });
</script>

@endsection