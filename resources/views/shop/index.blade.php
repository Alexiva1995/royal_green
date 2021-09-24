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

<div id="adminServices" class="">
    <div class="card" style="background:transparent;">
        <div class="card-content">
            <div class="card-body card-dashboard">
                <h1 class="text-white">Articulos de la tienda</h1>
                <p class="text-white">Desliza para seleccionar</p> 
                <div class="row justify-content-between align-items-center">
                    <div class="text-left">
                        <img class="m-2" id="imagePackage" src="{{Auth::user()->inversionMasAlta() != null ?Auth::user()->inversionMasAlta()->getPackageOrden->img() : asset('assets/img/royal_green/logos/logo.svg')}}" alt="" style="width: 150px; heigh:auto;">
                    </div>
                    <form class="text-right mr-3" action="{{route('shop.procces')}}" method="POST" target="_blank" class="d-inline">
                        @csrf
                        <input type="hidden" name="idproduct" id="idProduct">
                        <input type="hidden" id="oldId" value="{{(isset($idInvertido)) ? $idInvertido : 0}}">
                        <button class="btn btn'outline-dark rounded" style="border: 1px solid #66FFCC;" id="submit" type="submit" disabled>
                            @if($invertido == null)
                                Comprar
                            @else
                                Actualizar
                            @endif
                        </button>
                    </form>
                </div>
                
                <div class="row col-12 justify-content-center mt-2">
                        <input class="inputrange" id="inputrange" list="packages" type="range" min="{{(isset($idInvertido)) ? $idInvertido : 2}}" max="{{count($packages)}}" step="1" value="0">
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
        let oldId = document.querySelector("#oldId").value;
        let imagePackage = document.querySelector("#imagePackage");
        let submit = document.querySelector("#submit");
        inputrange.addEventListener("change", myScript);
        let src = '';
        
        function myScript(){
            idProduct.value = inputrange.value;
            // submit.removeAttribute('disabled');
            if(oldId == idProduct.value){
                submit.setAttribute('disabled', 'disabled');
            }else{
                submit.removeAttribute('disabled');
            }
  
            let img;
            switch (idProduct.value) {
                case '1':
                    img = "paquetes/rg50.png";
                break;
                case '2':
                    img = "paquetes/rg100.png";
                break;
                case '3':
                    img = "paquetes/rg250.png";
                break;
                case '4':
                    img = "paquetes/rg500.png";
                break;
                case '5':
                    img = "paquetes/rg1000.png";
                break;
                case '6':
                    img = "paquetes/rg2000.png";
                break;
                case '7':
                    img = "paquetes/rg5000.png";
                break;
                case '8':
                    img = "paquetes/rg10000.png";
                break;
                case '9':
                    img = "paquetes/rg25000.png";
                break;
                case '10':
                    img = "paquetes/rg50000.png";
                break;
            
                default:
                img = "logos/logo.svg";
                    break;
            }
            src = window.url_asset+'assets/img/royal_green/'+img;
            imagePackage.src = src;
        }
    });
</script>

@endsection