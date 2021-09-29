@extends('layouts.dashboard')

{{-- contenido --}}
@section('content')

<div class="col col-12 mt-1">
    <div class="card bg-lp">
        <div class="card-content">
            <div class="card-body card-dashboard p-0">
                <div class="table-responsive">
                    <h1 class="text-white p-1">Wallet</h1>

                    <form action="{{route('wallet.adminWallets')}}" method="get">
                    @csrf
                        <div class="input-group ml-5 mb-5 col-10 mt-3">
                            <input name="iduser" type="text" class="form-control" placeholder="">
                            <button class="btn btn-outline-primary" type="submit" id="button-addon2">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- permite llamar a las opciones de las tablas --}}
@include('layouts.componenteDashboard.optionDatatable')

@include('layouts.componenteDashboard.modalRetirar')
@endsection