@extends('layouts.dashboard')

@section('content')
<div id="logs-list">
        <div class="card">
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <div class="table-responsive">
                        <table class="table w-100 nowrap scroll-horizontal-vertical myTable">
                            <thead class="">

                                <tr class="text-center text-white bg-purple-alt2">
                                    <th>ID</th>
                                    <th>Paquete</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Fecha de Creación</th>
                                </tr>

                            </thead>
                            <tbody>

                                @foreach ($ordenes as $orden)
                                <tr class="text-center">
                                    <td>{{$orden->id}}</td>
                                    <td>{{-- {{$orden->getGroupOrden->name}} --}} {{$orden->getPackageOrden->name}}</td>
                                    <td>{{$orden->total}}</td>

                                    @if ($orden->status == '0')
                                    <td> <a class=" btn btn-outline-primary text-white text-bold-600">Esperando</a></td>
                                    @elseif($orden->status == '1')
                                    <td> <a class=" btn btn-success text-white text-bold-600">Aprobado</a></td>
                                    @elseif($orden->status >= '2')
                                    <td> <a class=" btn btn-danger text-white text-bold-600">Cancelado</a></td>
                                    @endif

                                    <td>{{date('Y-M-d', strtotime($orden->created_at))}}</td>
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


