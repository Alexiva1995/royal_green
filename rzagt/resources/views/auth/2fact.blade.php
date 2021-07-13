@extends('layouts.login')

@section('content')

<section class="row flexbox-container">
    <div class="col-xl-8 col-11 d-flex justify-content-center">
        <div class="card bg-authentication rounded-0 mb-0">
            <div class="row m-0">
                <div class="col-lg-6 col-12 text-center align-self-center px-1 py-0">
                    @if ($urlqb != '')
                    <img src="{{$urlqb}}" alt="branding logo" width="250">
                    @else
                    <img src="{{asset('assets/imgLanding/logo2.png')}}" alt="branding logo" width="250">
                    @endif
                    {{-- <img src="{{asset('assets/imgLanding/logo-10.svg')}}" alt="branding logo" width="250"> --}}

                    {{-- <img src="{{asset('app-assets/images/pages/login.png')}}" alt="branding logo"> --}}
                </div>
                <div class="col-lg-6 col-12 p-0">
                    <div class="card rounded-0 mb-0 px-2">
                        <div class="card-header pb-1">
                            <div class="card-title recuperar">
                                <h4 class="mb-0">Validacion de 2FACT</h4>
                            </div>
                        </div>
                        {{-- alertas --}}
                        @include('dashboard.componentView.alert')

                        {{-- <p class="px-2">Welcome back, please login to your account.</p> --}}
                        <div class="card-content">
                            <div class="card-body pt-1">
                                {{-- reset password --}}
                                <form class="forget-form recuperar" action="{{route('autenticacion.2fact')}}"
                                    method="post">
                                    {{ csrf_field() }}
                                    <div class="form-label-group">
                                        <input type="text" id="inputEmail" name="code" class="form-control"
                                            placeholder="Code 2fact">
                                        <label for="inputEmail">Code 2fact</label>
                                    </div>

                                    <div class="float-md-right d-block mb-1 col-12">
                                        <button type="submit" class="btn btn-primary btn-block px-75">Validar
                                            Código</button>
                                        <a class="btn btn-danger btn-block px-75" href="{{ route('logout') }}"
                                            data-toggle="dropdown"
                                            onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                            Cancelar
                                        </a>
                                    </div>
                                </form>
                                {{-- Salir del sistema --}}
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection