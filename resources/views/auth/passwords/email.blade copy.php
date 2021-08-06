@extends('layouts.auth')

@push('custom_css')
<style>
    .bg-fucsia {
        background: transparent linear-gradient(180deg, #007DFF 0%, linear-gradient(90deg, rgba(17,38,44,1) 0%, rgba(54,99,112,1) 68%) 100%) 0% 0% no-repeat padding-box;
    }

    .text-rosado {
        color: #007DFF;
    }

    .btn-login {
        padding: 0.6rem 2rem;
        border-radius: 1.429rem;
    }

    .text-input-holder {
        font-weight: 800;
        color: #000000;
    }

    .card{
        border-radius: 1.5rem;
    }

</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4 col-sm-8 col-12">
            {{-- header --}}
            <div class="col-12 text-center mb-2">
                <img src="{{asset('assets/img/HDLRS--vertical-blanco.png')}}" alt="logo" height="150" width="190">
                <h5 class="text-white">Bienvenido a HDLRS</h5>
            </div>
            {{-- cuerpo login --}}
            <div class="card card-margin">
                <div class="card-header text-center">
                    <a class="text-rosado float-left" href="{{route('login')}}">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <h5 class="card-title col-11 text-input-holder">{{ __('Restablecer Contraseña') }}</h5>
                </div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <p class="text-help">
                        <small>Te vamos a enviar un código a la dirección de correo que ingreses para que recuperes tu contraseña.</small>
                    </p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-12">
                                <input id="email" type="email"
                                    class="form-control text-input-holder @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                    placeholder="Ingresa tu email">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-12">
                                <button type="submit" class="btn bg-fucsia btn-login text-white btn-block">
                                    {{ __('Enviar Código') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
