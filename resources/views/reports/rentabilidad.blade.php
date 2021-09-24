@extends('layouts.dashboard')

@section('content')
<div id="logs-list">
    <div class="col-12">
        <div class="card bg-lp">
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <h1 class="text-white">Rentabilidad</h1>
                        <table class="table nowrap scroll-horizontal-vertical myTable table-striped">
                            <thead class="">

                                <tr class="text-center text-white bg-purple-alt2">
                                    <th>ID</th>
                                    <th>Porcentaje</th>
                                    <th>Fecha</th>

                                </tr>

                            </thead>
                            @foreach($table as $tables)
                            <tbody class="text-center">
                                <th>{{$tables->id}}</th>
                                <th>{{$tables->porcentaje_utilidad}}</th>
                                <th>{{$tables->created_at}}</th>


                            </tbody>
                            @endforeach
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