@extends('layouts.dashboard')

@section('content')
<div id="logs-list">
    <div class="col-12">
        <div class="card bg-lp">
            <div class="card-content">
                <div class="card-body card-dashboard">
                    <h1 class="text-white">Auditorías</h1>
                    <div>
                        <table class="table w-100 nowrap scroll-horizontal-vertical myTable table-striped w-100 text-white ">

                            <thead class="">

                                <tr class="text-center text-white bg-purple-alt2">
                                    <th>#</th>
                                    <th>Correo</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
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
