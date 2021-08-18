@extends('layouts.dashboard')

@section('content')
<div class="col-12 mt-3">
    <div class="card bg-lp">
        <div class="card-content">
            <div class="card-body card-dashboard p-0">
                <div class="table-responsive">
                <h3 class="text-white p-1">Últimos Pedidos</h3>
                    <table class="table nowrap scroll-horizontal-vertical myTable2">
                        <thead>

                            <tr class="text-center text-dark text-uppercase pl-2">                                
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Rango</th>
                                <th>Fecha</th>    
                            </tr>

                        </thead>
                        <tbody>
                            @foreach($logRanks as $rank)
                            <tr class="text-center text-white pl-2">
                                <td>{{$rank->id}}</td>
                                <td>{{$rank->getUserRank->name}}</td>
                                <td>{{$rank->getRank->name}}</td>
                                <td>{{$rank->created_at}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- permite llamar a las opciones de las tablas --}}
@include('layouts.componenteDashboard.optionDatatable')
