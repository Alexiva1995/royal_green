@extends('layouts.dashboard')

@section('content')

{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- alertas --}}
@include('dashboard.componentView.alert')

{{-- formulario de id usuario  --}}
@include('dashboard.componentView.formSearchSimple', ['route' => 'wallet.binario', 'name1' => 'id', 'type' => 'number', 'text' => 'ID Usuario'])

{{-- formulario de fecha  --}}
@include('dashboard.componentView.formSearch', ['route' => 'wallet.binario', 'name1' => 'fecha1', 'name2' => 'fecha2', 'text1' => 'Fecha Desde', 'text2' => 'Fecha Hasta', 'type' => 'date'])

<div class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="table-responsive">
                <table id="mytable" class="table zero-configuration">
                    <thead>
                        <tr class="text-center">
                            <th>
                                ID
                            </th>
                            <th>
                                Referido
                            </th>
                            <th>
                                Puntos
                            </th>
                            <th>
                                Lado
                            </th>
                            <th>
                                Fecha
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wallets as $wallet)
                        <tr class="text-center">
                            <td>
                                {{$wallet->iduser}}
                            </td>
                            <td>
                                {{$wallet->email_referred}}
                            </td>
                            <td>
                                {{$wallet->tmppuntos}}
                            </td>
                            <td>
                                {{$wallet->lado}}
                            </td>
                            <td>
                                {{date('Y-m-d', strtotime($wallet->created_at))}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- 
    @include('wallet/componentes/formRetiro', ['disponible' => 0, 'tipowallet' => 0])
    @include('wallet/componentes/formTransferencia')

    <script>
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
                $('#retirar').removeAttr('disabled')
            })
        }

        function totalRetiro(valor) {
            let resul = 0
            if ($('#tipo').val() == 1) {
                let tmp = valor * $('#comisionH').val()
                resul = valor - tmp
            } else {
                resul = valor - $('#comisionH').val()
            }
            $('#total').val(resul)
        }
    </script> --}}
    @endsection