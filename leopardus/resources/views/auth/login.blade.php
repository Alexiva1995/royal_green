@extends('layouts.login')

@section('content')
{{-- @php
    if(!request()->secure())
    {
        header('location: https://greenviewmds.com/mioficina/login');
        // redirect()->secure(request()->getPathInfo(),301);
    }
@endphp --}}


<section class="row flexbox-container">
    <div class="col-xl-8 col-11 d-flex justify-content-center">
        <div class="card bg-authentication rounded-0 mb-0">
            <div class="row m-0">
                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                    <img src="{{asset('assets/imgLanding/logo2.png')}}" alt="branding logo" width="300">
                    {{-- <img src="{{asset('app-assets/images/pages/login.png')}}" alt="branding logo"> --}}
                </div>
                <div class="col-lg-6 col-12 p-0">
                    <div class="card rounded-0 mb-0 px-2">
                        <div class="card-header pb-1">
                            <div class="card-title inicio">
                                <h4 class="mb-0">Sesion</h4>
                            </div>
                            <div class="card-title recuperar" style="display:none;">
                                <h4 class="mb-0">Recuperar tu clave</h4>
                            </div>
                        </div>
                        {{-- alertas --}}
                        @include('dashboard.componentView.alert')

                        {{-- <p class="px-2">Welcome back, please login to your account.</p> --}}
                        <div class="card-content">
                            <div class="card-body pt-1">
                                {{-- registro --}}
                                <form class="login-form inicio" method="POST"
                                    action="{{ route('autenticacion-login') }}">
                                    {{ csrf_field() }}
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" id="user-name" placeholder="Username"
                                            required value="{{ old('user_email') }}" name="user_email">
                                        <div class="form-control-position">
                                            <i class="feather icon-user"></i>
                                        </div>
                                        <label for="user-name">Nombre de Usuario</label>
                                    </fieldset>

                                    <fieldset class="form-label-group position-relative has-icon-left">
                                        <input type="password" class="form-control" id="user-password"
                                            placeholder="Password" required name="password">
                                        <div class="form-control-position">
                                            <i class="feather icon-lock"></i>
                                        </div>
                                        <label for="user-password">Clave</label>
                                    </fieldset>
                                    <div class="form-group d-flex justify-content-between align-items-center">
                                        <div class="text-left">
                                            <fieldset class="checkbox">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox">
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">Recuerdame</span>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="text-right">
                                            <a class="card-link" onclick="toggle()" href="javascript:;">
                                                ¿Olvidaste tu Clave?
                                            </a>
                                        </div>
                                    </div>
                                    <a href="{{route('autenticacion.new-register')}}"
                                        class="btn btn-outline-primary float-left btn-inline">Registro</a>
                                    <button type="submit" class="btn btn-primary float-right btn-inline">Session</button>
                                </form>
                                {{-- reset password --}}
                                <form class="forget-form recuperar" action="{{route('autenticacion.clave')}}"
                                    method="post" style="display:none;">
                                    {{ csrf_field() }}
                                    <div class="form-label-group">
                                        <input type="email" id="inputEmail" class="form-control" placeholder="Email" name="email">
                                        <label for="inputEmail">Correo</label>
                                    </div>

                                    <div class="float-md-right d-block mb-1 col-12">
                                        <button type="submit" class="btn btn-primary btn-block px-75">Recuperar tu Clave</button>
                                    </div>
                                    <div class="float-md-left d-block mb-1 col-12">
                                        <a href="javascript:;" class="btn btn-outline-primary btn-block px-75"
                                            onclick="toggle()">Regresar al Login</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr>
                        {{-- <div class="login-footer">
                            <div class="divider">
                                <div class="divider-text">OR</div>
                            </div>
                            <div class="footer-btn d-inline">
                                <a href="#" class="btn btn-facebook"><span class="fa fa-facebook"></span></a>
                                <a href="#" class="btn btn-twitter white"><span class="fa fa-twitter"></span></a>
                                <a href="#" class="btn btn-google"><span class="fa fa-google"></span></a>
                                <a href="#" class="btn btn-github"><span class="fa fa-github-alt"></span></a>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function toggle() {
        $('.inicio').toggle('slow')
        $('.recuperar').toggle('slow')
    }
</script>
@endsection