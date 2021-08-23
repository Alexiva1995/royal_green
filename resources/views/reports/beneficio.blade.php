@extends('layouts.dashboard')

@section('content')
<div id="logs-list">
    <div class="col-12">

        <div class="row match-height">
            <div class="col-md-4 col-12 mt-2">
                <div class="card btn-secondary text-white text-center mx-2">
                    <p class="card-title  my-2">Ganancia Total</p>
                    <span class="font-large-2 font-weight-bolder">{{number_format($comision + $retiro,2,".",",")}}</span>
                </div>
            </div>
            
            <div class="col-md-4 col-12 mt-2">
                <div class="card btn-secondary text-white text-center mx-2">
                    <p class="card-title my-2">Comisión</p>
                    <span class="font-large-1 font-weight-bold">{{number_format($comision, 2, ".",",")}}</span>
                </div>
            </div>

            <div class="col-md-4 col-12 mt-2">
                <div class="card btn-secondary text-white text-center mx-2">
                    <p class="card-title my-2">Retiro</p>
                    <span class="font-large-1 font-weight-bold">{{number_format($retiro,2,".",",")}}</span>
                </div>
            </div>
        </div>
    </div>

        <div class="card bg-lp">
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                      <h1 class="text-white">Beneficio Royal</h1>
                        <table class="table nowrap scroll-horizontal-vertical myTable table-striped">
                            <thead class="">

                                <tr class="text-center text-white bg-purple-alt2">                                
                                    <th>ID</th>
                                    <th>Tipo de Transacción</th>
                                    <th>Correo</th>
                                    <th>Monto</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($beneficios as $orden)
                                    <tr class="text-center text-white">
                                        <td>{{$orden->id}}</td>
                                        <td>
                                        @if($orden->tipo_transaction == 0)
                                            <strong>Comisión</strong>
                                            @else
                                            <strong>Retiro</strong>
                                        @endif
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


