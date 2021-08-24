@extends('layouts.dashboard')

{{-- contenido --}}
@section('content')
<div class="row">
    <div class="col-sm-4 col-12 mt-1">
        <div class="card h-80 p-2 art-2"> 
            <div class="card-header d-flex align-items-center text-right pb-0 pt-0 white">
                <h2 class="mt-1 mb-0 text-white font-weight-light"><b>Saldo disponible</b></h2>
            </div>
            <div class="card-sub d-flex align-items-center">
                <h1 class="text-white mb-0"><b style="color: #66FFCC;">$ {{Auth::user()->saldoDisponible()}}</b></h1>
            </div>
    
            <div class="card-header d-flex align-items-center mt-3">
                <button class="btn btn-dark rounded" data-toggle="modal" data-target="#modalSaldoDisponible" style="border: 1px solid #fff;"><b>RETIRAR</b></button>
            </div>
        </div>
    </div>
    
    <div class="col-sm-8 col-12 mt-1">
        <div class="card bg-lp">
            <div class="card-content">
                <div class="card-body card-dashboard p-0">
                    <div class="table-responsive">
                    <h3 class="text-white p-1">Billetera</h3>
                        <table class="table nowrap scroll-horizontal-vertical myTable2">
                            <thead>

                                <tr class="text-center text-dark text-uppercase pl-2">                                
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Email</th>
                                    <th>Monto</th>    
                                </tr>

                            </thead>
                            <tbody>
                                @foreach($wallets as $orden)
                                <tr class="text-center text-white pl-2">
                                    <td>{{date('d-m-Y', strtotime($orden->created_at));}}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            {{-- @if($orden->tipo_transaction == 0)
                                            <strong>Comisión</strong>
                                            @else
                                            <strong>Retiro</strong>
                                            @endif --}}

                                                <p style="margin: 0; padding: 0; position: relative;">
                                                    {{-- @if ($orden->status == '0')
                                                    <span class="dot enespera"></span> En Espera
                                                    @elseif($orden->status == '1')
                                                    <span class="dot completado"></span> Completado
                                                    @elseif($orden->status >= '2')
                                                    <span class="dot cancelado"></span> Cancelado
                                                    @endif --}}
                                                    {{$orden->descripcion}}
                                                </p>
                                                    
                                        </div>
                                    </td>
                                    <td>{{$orden->getWalletUser->email}}</td>
                                    <td>{{$orden->monto}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
{{-- permite llamar a las opciones de las tablas --}}
@include('layouts.componenteDashboard.optionDatatable')

@include('layouts.componenteDashboard.modalRetirar')
