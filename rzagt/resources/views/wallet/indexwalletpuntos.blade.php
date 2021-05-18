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

    @endsection