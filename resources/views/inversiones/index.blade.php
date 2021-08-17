@extends('layouts.dashboard')

@section('content')
<div id="logs-list">
    <div class="col-12">
        <div class="card bg-lp">
            <div class="card-content">
                <div class="card-body card-dashboard">
                    @if(auth()->user()->admin == 1)
                    {{-- <button class="btn btn-primary text-white float-right" data-toggle="modal" data-target="#modalPorcentajeGanancia">Cambiar %</button> --}}
                    <div class="row d-flex justify-content-center">
                        <h1 class="text-white col-10">Inversiones</h1>
                        <div class="col-2">
                            <label for="status">Filtro de Estado</label>
                            <select class="form-control" data-toggle="select" onchange="filterTable()" name="status"
                                id="userTypeFilter">
                                <option value="0">Todas la Inversiones</option>
                                <option value="Activo">Activos</option>
                                <option value="Culminado">Culminados</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    <div>
                        <table
                            class="table w-100 nowrap scroll-horizontal-vertical myTable table-striped w-100 text-white">
                            <thead>
                                <tr class="text-center text-white bg-purple-alt2">
                                    <th>#</th>
                                    <th>Correo</th>
                                    {{-- <th>Paquete</th> --}}
                                    <th>Inversion</th>
                                    <th>Ganancia</th>
                                    {{-- <th>Capital</th> --}}
                                    <th>Progreso</th>
                                    {{-- <th>Ganancia acumulada</th> --}}
                                    {{-- <th>Porcentaje fondo</th> --}}
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inversiones as $inversion)
                                {{-- @php
                                $ganancia = $inversion->capital - $inversion->invertido;
                                $porcentaje = ($ganancia / $inversion->invertido) * 100;
                                @endphp --}}
                                <tr class="text-center text-white">
                                    <td>{{$inversion->id}}</td>
                                    <td>{{$inversion->correo}}</td>
                                    {{-- <td>{{$inversion->getPackageOrden->getGroup->name }} -
                                    {{$inversion->getPackageOrden->name}}</td> --}}
                                    <td>$ {{number_format($inversion->invertido, 2, ',', '.')}}</td>
                                    <td>$ {{number_format($inversion->ganacia, 2, ',', '.')}}</td>
                                    {{-- <td>$ {{number_format($inversion->capital, 2, ',', '.')}}</td> --}}
                                    <td>{{number_format($inversion->progreso() * 2,2, ',', '.')}} %</td>
                                    {{-- <td>$ {{number_format($inversion->ganancia_acumulada,2, ',', '.')}}</td> --}}
                                    {{-- <td>{{number_format($inversion->porcentaje_fondo,2, ',', '.')}} %</td> --}}
                                    <td>{{date('Y-m-d', strtotime($inversion->created_at))}}</td>
                                    <td>
                                        @if($inversion->status == 1)
                                        Activo
                                        @elseif($inversion->status == 2)
                                        Culminado
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-success text-white float-right" data-toggle="modal"
                                            data-target="#modalRentabilidad">Pagar Rentabilidad</button></td>
                                </tr>
                                <!-- MODAL PARA ACTUALIZAR PORCENTAJE DE GANANCIA -->
                                @include('inversiones.componentes.rentabilidad')
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA ACTUALIZAR PORCENTAJE DE GANANCIA -->
@include('inversiones.componentes.gain')

@endsection

{{-- permite llamar a las opciones de las tablas --}}
@include('layouts.componenteDashboard.optionDatatable')

@push('custom_js')

<script>
    function filterTable() {
        $('.myTable').DataTable().draw();
    }

    $(document).ready(function () {
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                let userTypeColumnData = data[6];
                if (!filterByUserType(userTypeColumnData)) {
                    return false;
                }
                return true;
            }
        );
    });


    function filterByUserType(userTypeColumnData) {
        let userTypeSelected = $('#userTypeFilter').val();
        if (userTypeSelected === "0") {
            return true;
        }
        return userTypeColumnData === userTypeSelected;
    }
</script>
@endpush