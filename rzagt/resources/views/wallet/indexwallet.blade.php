@extends('layouts.dashboard')

@section('content')
@php
use Carbon\Carbon;
$fecha = Carbon::now();
$activo = false;
if ($fecha->dayOfWeek >= 1 && $fecha->dayOfWeek <= 2) { $activo=true; } 
@endphp 

<style>
    label{
        color: black;
    }

    .p-2{
        color: black;
    }
</style>

{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

@if (Auth::user()->ID == 1)
    {{-- formulario de fecha  --}}
@include('dashboard.componentView.formSearchSimple', ['route' => 'wallet-user', 'name1' => 'iduser', 'type' => 'text', 'text' => 'ID Usuario'])
@endif

<div class="alert alert-info">
    <button class="close" data-close="alert"></button>
    <span style="color: white">
        Todos los retiros realizados serán procesados los días viernes y sábados
    </span>
  </div>

{{-- alertas --}}
@include('dashboard.componentView.alert')


<div class="card">
    <div class="card-content">
        <div class="card-body">
            @if ($nombre_user != '')
            <h4>Billetera del usuario <strong>{{$nombre_user}}</strong></h4>
            @endif
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
                                Credito $
                            </th>
                            <th class="text-center">
                                Feed $
                            </th>
                            {{-- <th class="text-center">
                                Balance
                            </th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wallets as $wallet)
                        <tr>
                            <td class="text-center">
                                {{$wallet['id']}}
                            </td>
                            <td class="text-center">
                                {{$wallet['fecha']}}
                            </td>
                            {{-- <td class="text-center">
                                {{$wallet['usuario']}}
                            </td> --}}
                            <td class="text-center">
                                {{$wallet['email']}}
                            </td>
                            
                            <td class="text-center">
                                {{$wallet['descripcion']}}
                            </td>
                            <td class="text-center">
                                {{number_format($wallet['debito'], 2, '.', ',')}}
                            </td>
                            <td class="text-center">
                                {{number_format($wallet['credito'], 2, '.', ',')}}
                            </td>
                            <td class="text-center">
                                {{$wallet['descuento']}}
                            </td>
                            {{-- <td class="text-center">
                                
                                    @if ($moneda->mostrar_a_d)
                                    {{$moneda->simbolo}} {{$wallet['balance']}}
                                    @else
                                    {{$wallet['balance']}} {{$moneda->simbolo}}
                                    @endif
                                
                            </td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if (Auth::user()->rol_id != 0 && !$pagosPendientes)
            @if (Auth::user()->pay_retiro == 1)
            <div class="col-xs-12 col-sm-6">
                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#myModalRetiro">Retiro</button>
            </div>
             @else
             <h5>
                Los retiros se encontrarán disponibles de Lunes a Sábado hasta las 12pm GMT-6: México
             </h5>
            @endif
        @endif
        @if ($pagosPendientes)
        <div class="col-xs-12 col-sm-6">
            <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#myModalValidacion">Validar Retiro</button>
        </div>
        @endif
    </div>
</div>

@include('wallet/componentes/formRetiro', ['disponible' => $disponible, 'tipowallet' => 1])
{{-- @include('wallet/componentes/formTransferencia') --}}
@include('wallet/componentes/modalValidacion')

@if ($pagosPendientes)
@push('custom_js')
<script>
    $(document).ready(function () {
        $('#myModalValidacion').modal('show');
    })
</script>
@endpush
@endif

@push('custom_js')

@endpush
@endsection