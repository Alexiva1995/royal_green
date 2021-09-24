@extends('layouts.dashboard')

@section('content')
    <div class="col-12 mt-3">
        <div class="card bg-lp">
            <div class="card-content">
                <div class="card-body card-dashboard p-0">
                    <div class="card-title text-center py-2">Seleccione un usuario para ver sus comisiones </div>
                    <div class="d-flex justify-content-center">
                        <select class="form-control w-25 mb-2 select2" name="comisionesId" id="comisionesId">
                            <option class="text-center" value=""> --Seleccione un usuario-- </option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="table-responsive">
                        <h3 class="text-white p-1">Comisiones del Usuario</h3>
                        <table class="table nowrap scroll-horizontal-vertical myTable2 yajra-datatable"
                            id="comisiones-datatable">
                            <thead>
                                <tr class="text-center text-dark text-uppercase pl-2">
                                    <th>Id</th>
                                    <th>Usuario</th>
                                    <th>Descripción</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- permite llamar a las opciones de las tablas --}}
    @endsection
    @include('audit.comisionesDatatableSideServer')
