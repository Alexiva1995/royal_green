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
                            {{-- <th class="text-center">
                                #
                            </th> --}}
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
                                Cash
                            </th>
                            <th class="text-center">
                                Credito
                            </th>
                            <th class="text-center">
                                Feed
                            </th>
                            {{-- <th class="text-center">
                                Balance
                            </th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wallets as $wallet)
                        <tr>
                            {{-- <td class="text-center">
                                {{$wallet['id']}}
                            </td> --}}
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
                                
                                    @if ($moneda->mostrar_a_d)
                                    {{$moneda->simbolo}} {{number_format($wallet['debito'], 2, ',', '.')}}
                                    @else
                                    {{number_format($wallet['debito'], 2, ',', '.')}} {{$moneda->simbolo}}
                                    @endif
                                
                            </td>
                            <td class="text-center">
                                
                                    @if ($moneda->mostrar_a_d)
                                    {{$moneda->simbolo}} {{number_format($wallet['credito'], 2, ',', '.')}}
                                    @else
                                    {{number_format($wallet['credito'], 2, ',', '.')}} {{$moneda->simbolo}}
                                    @endif
                                
                            </td>
                            <td class="text-center">
                                
                                    @if ($moneda->mostrar_a_d)
                                    {{$moneda->simbolo}} {{$wallet['descuento']}}
                                    @else
                                    {{$wallet['descuento']}} {{$moneda->simbolo}}
                                    @endif
                                
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
            @if ($diaRetiro)
            <div class="col-xs-12 col-sm-6">
                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#myModalRetiro">Retiro</button>
            </div>
             @else
             <h5>
                 Los dias de retiro son todos los viernes
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
        console.log(valor);
        let resul = valor
        let tmp = valor * $('#comisionH').val()
        resul = valor - tmp
        console.log(resul);
        $('#total').val(resul)
    }
</script>
@endpush
@endsection