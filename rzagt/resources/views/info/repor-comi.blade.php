@extends('layouts.dashboard')

@section('content')

{{-- formulario de fecha  --}}
@include('dashboard.componentView.formSearch', ['route' => 'info.repor-todos', 'name1' => 'primero', 'name2' =>
'segundo', 'text1' => 'Fecha Desde', 'text2' => 'Fecha Hasta', 'type' => 'date'])

@endsection


