@extends('layouts.email')


@section('content')


<h4 class="card-title">
    <strong>Codigo de Validacion de Retiro</strong>
</h4>

    <p class="card-text text-justify">
        Para completar su proceso de cambio de correo
        debe agregar este código 
        Código: {{$code}}
    </p>

@endsection
{{-- @push('quote')
De seguro que serás nuestro próximo UPPER, <br>
asi pues que sigamos
@endpush --}}