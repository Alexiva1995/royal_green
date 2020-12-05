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
            <div class="table-r">
                <table id="mytable" class="table zero-configuration">
                    <thead>
                        <tr class="text-center">
                            <th class="text-center">
                                #
                            </th>
                            <th class="text-center">
                                ID Orden
                            </th>
                            <th class="text-center">
                                Productos
                            </th>
                            <th class="text-center">
                                Fecha Activacion
                            </th>
                            <th class="text-center">
                                Total Ganado
                            </th>
                            <th class="text-center">
                                Total Retirado
                            </th>
                            <th class="text-center">
                                Balance General
                            </th>
                            {{-- <th class="text-center">
                                Registro de actividades
                            </th> --}}
                            <th class="text-center">
                                Accion
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rentabilidads as $rentabilidad)
                        <tr class="text-center">
                            <td>
                                {{$rentabilidad->id}}
                            </td>
                            <td>
                                {{$rentabilidad->idcompra}}
                            </td>
                            <td>
                                {{$rentabilidad->producto->nombre }} - {{$rentabilidad->idproducto}}
                            </td>
                            <td>
                                {{date('Y-m-d', strtotime($rentabilidad->created_at))}}
                            </td>
                            <td>
                                {{$rentabilidad->ganado}} $
                            </td>
                            <td>
                                {{$rentabilidad->retirado}} $
                            </td>
                            <td>
                                {{$rentabilidad->balance}} $
                            </td>
                            {{-- <td>
                                
                            </td> --}}
                            <td>
                                @if (Auth::user()->rol_id != 0)
                                    @if ($rentabilidad->nivel_minimo_cobro <= Auth::user()->rol_id)
                                    <button class="btn btn-primary btn-block" onclick="openModal({{$rentabilidad->balance}}, {{$rentabilidad->id}})">Retiro</button>
                                    @else
                                    El retiro esta permito para los niveles Igual al Topacio Elite o Superior
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('rentabilidad.componentes.formRetiro', ['tipowallet' => 1])

@push('custom_js')
<script>
    $(document).ready(function () {
        $('.retirarbtn').click(function () {
            console.log('entre');
            retirarpago()
            $('.formretiro').submit();
        })
    })

    function openModal(disponible, id) {
        $('#montodisponible').val(disponible)
        $('#idrentabilidad').val(id)
        $('#myModalRetiro').modal('show')
    }

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