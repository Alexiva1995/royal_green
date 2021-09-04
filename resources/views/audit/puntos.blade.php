@extends('layouts.dashboard')

@section('content')
<div class="col-12 mt-3">
    <div class="card bg-lp">
        <div class="card-content">
            <div class="card-body card-dashboard p-0">
                <div class="table-responsive">
                <h3 class="text-white p-1">Historial de Puntos Binarios</h3>
                    <table class="table nowrap scroll-horizontal-vertical myTable2 yajra-datatable" id="puntos-datatable">
                        <thead>

                            <tr class="text-center text-dark text-uppercase pl-2">                                
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Referido</th>
                                <th>Puntos Derecha</th>
                                <th>Puntos Izquierda</th> 
                            </tr>

                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- permite llamar a las opciones de las tablas --}}
@include('audit.puntosDatatableSideServer')