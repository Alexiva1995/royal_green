@extends('layouts.login')
<style type="text/css">
    
#user-name::placeholder {
 color:teal;
}

#user-password::placeholder {
 color:teal;
}

</style>
@section('content')
<div class="navbar-wrapper">
    <button type="button" class="btn btn-default btn-lg text-white">
    <span class="glyphicon glyphicon-arrow-left"></span>   
        🡠 Regresar al inicio 
    </button>
</div>
 

<section class="row flexbox-container">
    <div class="col-xl-8 col-11 d-flex justify-content-center">
        <div class="card bg-authentication rounded-0 mb-0" style="background: transparent;">
            <div class="row m-0">
                <div class="col-12 p-0">
                    <div class="card rounded-0 mb-0 px-2" style="background: transparent;">
                        <img src="{{asset('assets/imgLanding/logo2.png')}}" alt="" class="m-auto branding logo" height="130" width="130">
                        <div class="card-header pb-1">
                            <div class="card-title text-left">
                                <h4 class="mb-0 text-white text-left"> INICIAR SESIÓN</h4>
                            </div>
                            <div class="card-title recuperar" style="display:none;">
                                <h4 class="mb-0 text-white">Recuperar tu clave</h4>
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
                                    <fieldset class="form-label-group form-group position-relative has-icon-left ">
                                        <input type="text-white" class="form-control btn-outline-primary" id="user-name" placeholder="Usuario"
                                            required value="{{ old('user_email') }}" name="user_email">
                                        <div class="form-control-position">
                                            <i class="feather icon-user"></i>
                                        </div>
                                        <label for="user-name">Nombre de Usuario</label>
                                    </fieldset>

                                    <fieldset class="form-label-group position-relative has-icon-left">
                                        <input type="password" class="form-control btn-outline-primary" id="user-password"
                                            placeholder="Contraseña" required name="password" style="placeholder">
                                        <div class="form-control-position">
                                            <i class="feather icon-lock"></i>
                                        </div>
                                        <label for="user-password">Clave</label>
                                    </fieldset>
                                    <div class="form-group d-flex justify-content-between align-items-center">
                                        <div class="text-left d-none">
                                            <fieldset class="checkbox">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" checked>
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">Recuerdame</span>
                                                </div>
                                            </fieldset>
                                        </div>
                                        
                                    </div><button type="submit" class="btn btn-primary float-right btn-inline col-12 mt-2"> CONECTAR
                                    </button>
                                    {{-- <div class="row">
                                        <a href="{{route('autenticacion.new-register')}}"
                                        class="btn btn-outline-primary float-left btn-inline col-12">Registro</a> 
                                    </div> --}}
                                    <div class="text-left">
                                            <a class="card-link" onclick="toggle()" href="javascript:;">
                                                ¿Olvidaste tu Clave?
                                            </a>
                                       
                                        </div>

                                        <div class="text-right">
                                            
                                            <button type="button " class="btn btn-icon rounded-circle btn-default bg-dark text-white ">
                                                <i class="font-medium-5 fa fa-paper-plane-o mr-50"></i> 
                                            </button>
                                        </div>
                                        
                                </form>
                                {{-- reset password 
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
                                --}}
                            </div>

                        </div>
                        <hr>
                         <div class="login-footer">

                            <div class="footer-btn d-inline" role="group" aria-label="Basic example">
                                                    <button type="button" class="btn btn-default waves-effect waves-light"><i class="feather icon-facebook"></i></button>
                                                     <button type="button" class="btn btn-default waves-effect waves-light"><i class="feather icon-youtube"></i></button>
                                                     <button type="button" class="btn btn-default waves-effect waves-light"><i class="feather icon-instagram"></i></button>
                                                     <button type="button" class="btn btn-default waves-effect waves-light"><i class="feather icon-twitter"></i></button>
                                                    <br>
                             <div class="text-center">
                                ©Royal Green
                            </div>
                            </div>

                              
                        </div> 
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