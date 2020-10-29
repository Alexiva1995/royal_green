<div class="row">
    <div class="col-lg-6 col-md-12 col-12 mt-1">
        <div class="card bg-analytics bg-blue-2 text-white h-100">
            <div class="card-content">
                <div class="card-body text-center">
                    <img src="{{asset('assets/img/sistema/ban-der.svg')}}" class="img-left" alt="card-img-left">
                    <img src="{{asset('assets/img/sistema/ban-izq.svg')}}" class="img-right" alt="card-img-right">
                    <img src="{{asset('assets/img/sistema/confe-der.svg')}}" class="img-left" alt="card-img-left"
                        style="height: 100%">
                    <img src="{{asset('assets/img/sistema/confe-izq.svg')}}" class="img-right" alt="card-img-right"
                        style="height: 100%">
                    <div class="avatar avatar-xl bg-green-2 shadow m-0 mb-1">
                        <img src="{{asset('assets/img/sistema/usuario.png')}}" alt="card-img-left">
                        {{-- <div class="avatar-content">
                         <i class="feather icon-award white font-large-1"></i> 
                        </div> --}}
                    </div>
                    <div class="text-center">
                        <h1 class="mb-2 text-white">Bienvenido {{Auth::user()->display_name}}</h1>
                        {{-- <p class="m-auto w-75">
                            Tu saldo actual es $ {{number_format(Auth::user()->wallet, '2', ',', '.')}} <br>
                            ¿Qué tal recargar tu saldo?
                        </p> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 col-12 mt-1">
        <div class="card text-white bg-gradient-danger bg-red-alt h-100">
            <div class="card-content d-flex justify-contents-start align-items-center">
                <div class="card-body pb-0 pt-1">
                    <img src="{{asset('assets/img/sistema/card-img.svg')}}" alt="element 03" width="250" height="250"
                        class="float-right px-1">
                    <p class="card-text mt-3">Invita a tus amigos <br> y gana una comision</p>
                    <h4 class="card-title text-white">¡Todo es mejor con <br> amigos!</h4>
                    <a href="{{route('autenticacion.new-register').'?referred_id='.Auth::user()->ID}}" target="_blank"
                        class="btn btn-primary padding-button-short bg-white mt-1 waves-effect waves-light">
                        LINK REFERIDO
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
