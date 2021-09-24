@extends('layouts.dashboard')

@push('page_css')
    <style>
        #id_user::placeholder {
            color:white;
        }
    </style>
@endpush

@section('content')
<div class="col-12 mt-3">

    <div class="card">
        <div class="card-body">
            <div class="input-group mb-3">
                <input style="border: 1px solid #66FFCC;" type="number" class="form-control" placeholder="ID de usuario" aria-label="ID de usuario" min="1" id="id_user">
                <div class="input-group-append">
                  <button class="btn btn-outline-primary" type="button" id="btn_Search">Button</button>
                </div>
            </div>

            <div class="table-responsive">
            <h3 class="text-white p-1">Historial de Puntos Binarios</h3>
                <table class="table nowrap scroll-horizontal-vertical myTable2 yajra-datatable" id="puntos-datatable">
                    <thead>

                        <tr class="text-center text-dark text-uppercase pl-2">                                
                            <th>Id</th>
                            <th>Usuario</th>
                            <th>Referido</th>
                            <th>Puntos Derecha</th>
                            <th>Puntos Izquierda</th> 
                            <th>Lado</th>
                            <th>Estado</th>
                        </tr>

                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- permite llamar a las opciones de las tablas --}}
@include('audit.puntosDatatableSideServer')