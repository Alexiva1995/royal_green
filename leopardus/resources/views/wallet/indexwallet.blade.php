@extends('layouts.dashboard')

@section('content')
@php
use Carbon\Carbon;
$fecha = Carbon::now();
$activo = false;
if ($fecha->dayOfWeek >= 1 && $fecha->dayOfWeek <= 2) { $activo=true; } 
@endphp 

{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- alertas --}}
@include('dashboard.componentView.alert')


<div class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="table-responsive">
                <table id="mytable" class="table zero-configuration">
                    <thead>
                        <tr>
                            <th class="text-center">
                                #
                            </th>
                            <th class="text-center">
                                Usuario
                            </th>
                            <th class="text-center">
                                Fecha
                            </th>
                            <th class="text-center">
                                Descripción
                            </th>
                            <th class="text-center">
                                Cash
                            </th>
                            <th class="text-center">
                                Credito
                            </th>
                            <th class="text-center">
                                Feed
                            </th>
                            <th class="text-center">
                                Balance
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wallets as $wallet)
                        <tr>
                            <td class="text-center">
                                {{$wallet->id}}
                            </td>
                            <td class="text-center">
                                {{$wallet->usuario}}
                            </td>
                            <td class="text-center">
                                {{date('d-m-Y', strtotime($wallet->created_at))}}
                            </td>
                            <td class="text-center">
                                {{$wallet->descripcion}}
                            </td>
                            <td class="text-center">
                                
                                    @if ($moneda->mostrar_a_d)
                                    {{$moneda->simbolo}} {{$wallet->debito}}
                                    @else
                                    {{$wallet->debito}} {{$moneda->simbolo}}
                                    @endif
                                
                            </td>
                            <td class="text-center">
                                
                                    @if ($moneda->mostrar_a_d)
                                    {{$moneda->simbolo}} {{$wallet->credito}}
                                    @else
                                    {{$wallet->credito}} {{$moneda->simbolo}}
                                    @endif
                                
                            </td>
                            <td class="text-center">
                                
                                    @if ($moneda->mostrar_a_d)
                                    {{$moneda->simbolo}} {{$wallet->descuento}}
                                    @else
                                    {{$wallet->descuento}} {{$moneda->simbolo}}
                                    @endif
                                
                            </td>
                            <td class="text-center">
                                
                                    @if ($moneda->mostrar_a_d)
                                    {{$moneda->simbolo}} {{$wallet->balance}}
                                    @else
                                    {{$wallet->balance}} {{$moneda->simbolo}}
                                    @endif
                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if (Auth::user()->rol_id != 0)
        <div class="col-xs-12 col-sm-6">
            <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#myModalRetiro">Retiro</button>
        </div>
        @endif
    </div>
</div>

@include('wallet/componentes/formRetiro', ['disponible' => Auth::user()->wallet_amount, 'tipowallet' => 1])
@include('wallet/componentes/formTransferencia')

<script>
    $(document).ready(function () {
        $('.retirarbtn').click(function () {
            console.log('entre');
            retirarpago()
            $('.formretiro').submit();
        })
    })

    function metodospago() {
        $('#correo').hide()
        $('#wallet').hide()
        $('#bancario').hide()
        let url = 'wallet/obtenermetodo/' + $('#metodopago').val()
        $.get(url, function (response) {
            let data = JSON.parse(response)
            $('#total').val(0)
            if (data.tipofeed == 1) {
                $('#comision').val(data.feed * 100)
                $('#lblcomision').text('Comision de Retiro en Porcentaje')
                $('#comisionH').val(data.feed)
                $('#tipo').val(data.tipofeed)
                $('#monto_min').val(data.monto_min)
            } else {
                $('#comision').val(data.feed)
                $('#lblcomision').text('Comision de Retiro Fija')
                $('#comisionH').val(data.feed)
                $('#tipo').val(data.tipofeed)
                $('#monto_min').val(data.monto_min)
            }
            if (data.correo == 1) {
                $('#correo').show()
            }
            if (data.wallet == 1) {
                $('#wallet').show()
            }
            if (data.bancario == 1) {
                $('#bancario').show()
            }
            $('#retirar').show()
        })
    }

    function retirarpago() {
        $('.formretiro').submit();
    }

    function totalRetiro(valor) {
        let resul = valor
        // if ($('#tipo').val() == 1) {
        //     let tmp = valor * $('#comisionH').val()
        //     resul = valor - tmp
        // } else {
        //     resul = valor - $('#comisionH').val()
        // }
        $('#total').val(resul)
    }
</script>
@endsection