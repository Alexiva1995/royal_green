@extends('layouts.email')


@section('content')


<h4 class="card-title">
    <strong>Codigo de Validacion de Retiro</strong>
</h4>

    <p class="card-text text-justify">
        Su Billetera: {{$wallet}}
        <br>
        Para completar su proceso de Retiro
        debe agregar este código valido por 15 min
        Código: {{$codigo}}
    </p>

@endsection
{{-- @push('quote')
De seguro que serás nuestro próximo UPPER, <br>
asi pues que sigamos
@endpush --}}