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
            <div class="card-title text-center py-2">Seleccione un usuario para ver sus Puntos </div>
                    <div class="d-flex justify-content-center">
                        <select class="form-control w-25 mb-2 select2" name="puntosId" id="puntosId">
                            <option class="text-center" value=""> --Seleccione un usuario-- </option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>

             <div class="table-responsive">
            <h3 class="text-white p-1">Historial de Puntos Binarios</h3>
                <table class="table nowrap scroll-horizontal-vertical myTable2 yajra-datatable" 
                    id="puntos-datatable">
                    <thead>

                            <tr class="text-center text-dark text-uppercase pl-2">                                 
                            <th>Id</th>
                            <th>Usuario</th>
                            <th>Referido</th>
                            <th>Puntos</th>
                            <th>Lado</th>
                            <th>Estado</th>
                            </tr>

                            </thead>
                            <tbody>

                                @foreach ($users as $item) 
                                <tr class="text-center">
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->fullname}}</td>
                                    <td>{{$item->referido($item->referred_id)}}</td>
                                    <td>{{$item->puntos_reales}}</td>
                                    <td>{{$item->side}}</td>
                                     @if ($item->status == '0')
                                        <td>En espera</td>
                                         @elseif($item->status == '1')
                                         <td>Pagado</td>
                                         @elseif($item->status == '2')
                                         <td>Cancelado</td>
                                         @endif
                                </tr>
                                @endforeach
                         </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- permite llamar a las opciones de las tablas --}}
@include('audit.puntosDatatableSideServer')
