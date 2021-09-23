@extends('layouts.auth')

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/auth/css/email.css')}}">
@endpush

@section('content')


<div class="row auth-inner">
    <!-- Left bg-->
    <div class="col-sm-6 col-md-8 col-lg-8 d-none d-sm-flex d-md-flex d-lg-flex royal_bg">
        <div class="">
            <img src="{{ asset('assets/img/royal_green/logos/logo.svg') }}" alt="" class="logo">
        </div>
    </div>


    <div class="col-12 col-sm-6 col-lg-4 col-md-4 d-flex align-items-center p-2 px-4">
        <div class="row">
            <div class="card-header d-flex justify-content-center">

                <h3 class="card-title text-input-holder text-white"><a href="{{route('login')}}"><i class="fas fa-arrow-left mr-3"></i></a> <b>Restablecer Contraseña</b></h3>

            </div>
            <div class="card-body col-12">
                <h5 class="text-white mb-2">
                    Te vamos a enviar un código a la dirección de correo que ingreses para que recuperes
                    tu contraseña.
                </h5>
                @if (session('status'))
                <div class="alert alert-info" role="alert">
                    {{ session('status') }}
                </div>
                @endif
                <form method="POST" id="validate" action="{{ route('password.email') }}">
                    @csrf
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-12">

                            <div class="input-text">
                                <input type="email" class="form-control border border-primary rounded" name="email" value="{{ old('email') }}" required placeholder="Ingresa tu email" style="background-color:#11262c;">
                                <label class="text-white">Ingresa tu email</label>
                            </div>

                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-primary rounded mt-1">
                                <b>Enviar Código</b>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        input::placeholder {
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .form-control {
            color: #FFFFFF;
        }

        .input-text {
            position: relative;
        }

        input {
            font-size: 18px;
            border-width: 0 0 1px 0;
            padding-top: 10px;
            padding-bottom: 10px;
            width: 100%;

        }

        input:not(:placeholder-shown)+label {
            top: -21px;
            opacity: 1;
            visibility: visible;
            font-weight: bold;
        }

        label {
            position: absolute;
            left: 0;
            top: 10px;
            color: #fff;
            font-size: 14px;
            opacity: 1;
            visibility: hidden;
            transition: 0.17s all ease-in-out;
        }
    </style>
    @endsection

    @push('custom_js')
    <script>
        $("#validate").validate();
    </script>
    @endpush