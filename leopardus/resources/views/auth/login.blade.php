@extends('layouts.login')
@section('content')
<section class="row flexbox-container box-shadow" >

<div class="container">      
  <div class="row">

        <div class="col-md-3 d-flex justify-content-center ">
        </div>

        <div class="col-md-3 d-flex justify-content-center ">

           <img src="{{asset('assets/imgLanding/logo2.png')}}" alt="" class=" m-auto branding logo" height="130" width="130">
        </div>
       
            

    <div class="col-md-4 col-11 d-flex justify-content-center">
        <div class="card bg-authentication rounded-0 mb-0" style="background: transparent;">
            <div class="card rounded-3 mb-0 px-2" style="background: transparent;">
                 
                 {{--Cabecera --}}
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

                                        <button type="submit" class="btn btn-primary float-right btn-inline col-12 mt-2">CONECTAR</button>
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
                                        <div class="text-right">
                                            <a class="card-link" onclick="toggle()" href="javascript:;">
                                                ¿Olvidaste tu Clave?
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <a href="{{route('autenticacion.new-register')}}"
                                        class="btn btn-outline-primary float-left btn-inline col-12" style="display: none">Registro</a>
                                    
                                    </div>
                                    
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
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=" col-md-12 text-right">
    <button type="button " class="btn btn-icon rounded-circle btn-default bg-dark text-white ">
        <i class="font-medium-5 fa fa-paper-plane-o mr-50"></i> 
    </button>

 </div>

</section>

<script type="text/javascript">
    function toggle() {
        $('.inicio').toggle('slow')
        $('.recuperar').toggle('slow')
    }
</script>
@endsection