@extends('layouts.dashboard')

@section('content')


{{-- option datatable --}}
@include('dashboard.componentView.optionDatatable')

{{-- alertas --}}
@include('dashboard.componentView.alert')


<div class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="table-r">
                <table id="mytable" class="table zero-configuration">
                    <thead>
                        <tr class="text-center">
                            <th class="text-center">
                                Fecha de Pago
                            </th>
                            <th class="text-center">
                                Porcentaje Pagado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rentabilidads as $rentabilidad)
                        <tr class="text-center">
                            <td>
                                {{date('Y-m-d', strtotime($rentabilidad->fecha_pago))}}
                            </td>
                            <td>
                                {{($rentabilidad->porcentaje * 100)}} %
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection