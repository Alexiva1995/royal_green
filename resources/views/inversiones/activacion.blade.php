@extends('layouts.dashboard')

@section('content')
<div id="logs-list">
    <div class="card">
        <div class="card-content">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table w-100 nowrap scroll-horizontal-vertical myTable w-100 ">
                        <thead>
                            <tr class="text-center text-white bg-purple-alt2">
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Accion</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($user as $user)
                            <tr class="text-center">
                                <td>{{$user->id}}</td>
                                <td>{{$user->fullname}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    @if($user->status == 0)
                                    <a class="btn btn-warning">Inactivo</a>
                                    @elseif($user->status == 1)
                                    <a class="btn btn-success">Activo</a>
                                    @elseif($user->status == 2)
                                    <a class="btn btn-danger">Eliminado</a>
                                    @endif
                                </td>
                                <td>
                                    @if($user->inversionMasAlta() == null)
                                    <a class=" btn btn-outline-primary text-white text-bold-600" data-toggle="modal"
                                        data-target="#ModalActivacion{{$user->id}}">Activar</a> @endif </td>
                            </tr>
                            {{-- modal de activacion --}}
                            @include('inversiones.componentes.activacion-modal')
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
