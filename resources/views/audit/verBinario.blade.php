@extends('layouts.dashboard')

@section('content')
    <div class="col-12 mt-3">
        <div class="card bg-lp">
            <div class="card-content">
                <div class="card-body card-dashboard py-3">
                    <div class="card-title text-center py-2">Seleccione un usuario para ver su arbol binario </div>
                    <div class="d-flex justify-content-center">
                        <select class="form-control w-50 select2" name="binarioId" id="binarioId">
                            <option class="text-center" value=""> --Seleccione un usuario-- </option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- permite llamar a las opciones de las tablas --}}
    @endsection
    @include('audit.verBinarioScript')
