@extends('layouts.landing')

@section('content')
{{-- @php
    if(!request()->secure())
    {
        header('location: https://comunidadlevelup.com/');
        // redirect()->secure(request()->getPathInfo(),301);
    }
@endphp --}}
@if ($landing == 0)
    {{-- Seccion de Filosofia --}}
    @include('landing.component.quienessomos')
    {{-- Seccion de Alianza --}}
    @include('landing.component.comofunciona')
    {{-- Seccion de Compañias asociadas --}}
    @include('landing.component.services')
    {{-- Seccion Como Participar --}}
    @include('landing.component.comoparticipar')
    {{-- Seccion Comunicate --}}
    @include('landing.component.comunicate')
@elseif($landing == 2)
@include('landing.component.faq')
@endif
@endsection